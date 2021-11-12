<?php

/** @noinspection MissedViewInspection */

namespace Matodor\RegistryConstructor\components;

use Matodor\Common\components\Helper;
use Matodor\RegistryConstructor\models\forms\TableRecordForm;
use Matodor\RegistryConstructor\models\Table;
use Matodor\RegistryConstructor\models\TableField;
use Matodor\RegistryConstructor\traits\HasSpreadsheetTrait;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use Yii;
use yii\base\Component;
use yii\web\Response;

class RecordsImporter extends Component
{
    use HasSpreadsheetTrait;

    /**
     * @var Table
     */
    public $table;

    /**
     * @var Spreadsheet
     */
    protected $spreadsheet;

    /**
     * @return TableField[]
     */
    public function getTableImportFields()
    {
        $fields = [];

        foreach ($this->table->fields as $field) {
            if (!$field->hasFlag(TableField::FLAGS_IMPORT_FIELD)
                || $field->type === TableField::FIELD_TYPE_SELECT
            ) {
                continue;
            }

            $fields[] = $field;
        }

        return $fields;
    }

    public function downloadExample()
    {
        $this->spreadsheet = new Spreadsheet();
        $this->spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(15);
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->getSheetView()->setZoomScale(75);

        $columns = [];

        foreach ($this->getTableImportFields() as $field) {
            $column = [
                'header' => $field->name,
                'width' => 20,
            ];

            if ($field->type === TableField::FIELD_TYPE_INPUT
                && $field->value_type === ValueType::TYPE_TEXT
            ) {
                $column['width'] = 40;
            }

            $columns[] = $column;
        }

        $row = 1;
        $col = 1;
        $rowBefore = $row;
        $cols = count($columns);

        // Заполнение шапки
        $sheet->setCellValueByColumnAndRow($col, $row, "Бланк для заполнения таблицы: {$this->table->name}");
        $sheet->mergeCellsByColumnAndRow($col, $row, $col + $cols - 1, $row);

        $this->setBackground('CFE7F5', $col, $rowBefore, $col + $cols - 1, $row);
        $this->setAlignment(['horizontal' => Alignment::HORIZONTAL_LEFT], $col, $rowBefore, $col + $cols - 1, $row);
        $this->setOutlineBorder(Border::BORDER_THICK, $col, $rowBefore, $col + $cols - 1, $row);
        $this->setFontWeightBold($col, $rowBefore, $col + $cols - 1, $row);

        $row++;
        $rowBefore = $row;

        foreach ($columns as $column) {
            $sheet->setCellValueByColumnAndRow($col, $row, $column['header']);
            $sheet->getColumnDimensionByColumn($col)->setWidth($column['width']);
            $col++;
        }

        $col = 1;
        $this->setBackground('CFE7F5', $col, $rowBefore, $col + $cols - 1, $row);
        $this->setAllBorder(Border::BORDER_THIN, $col, $rowBefore, $col + $cols - 1, $row);

        $fileName = 'Бланк-' . date('Y-m-d-H-i') . '.xlsx';
        $filePath = Yii::getAlias('@app/runtime/' . $fileName);

        $writer = new XlsxWriter($this->spreadsheet);
        $writer->setOffice2003Compatibility(false);
        $writer->save($filePath);

        Yii::$app->response
            ->sendFile($filePath, $fileName)
            ->on(Response::EVENT_AFTER_SEND, function($event) {
                unlink($event->data);
            }, $filePath);
    }

    public function import($filePath)
    {
        $f = function ($r, $c) {
            $c = Coordinate::stringFromColumnIndex($c);
            return "[{$c}{$r}]";
        };

        $errors = [];
        $reader = new XlsxReader();
        $spreadsheet = $reader->load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        $tableColumnsRow = 2;
        $col = 1;
        $fields = $this->getTableImportFields();

        foreach ($fields as $field) {
            $colName = (string) $sheet->getCellByColumnAndRow($col, $tableColumnsRow)->getValue();
            $colName = trim($colName);

            if ($field->name !== $colName) {
                $errors[] = $f($tableColumnsRow, $col) . " Ожидаемый заголовок '{$field->name}', заголовок в файле '{$colName}'";
            }

            $col++;
        }

        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors,
            ];
        }

        /** @var TableRecordForm[] $records */
        $records = [];
        $row = $tableColumnsRow + 1;

        while (true) {
            $col = 1;
            $rowIsEmpty = true;

            $recordErrors = [];
            $tableRecord = new TableRecordForm();
            $tableRecord->registry_table_id = $this->table->id;
            $tableRecord->populateRelation('table', $this->table);

            foreach ($fields as $field) {
                $value = $sheet->getCellByColumnAndRow($col, $row)->getFormattedValue();
                $fieldValue = $field->instantiateRecordValue(true);

                if (!Helper::isEmpty($value)) {
                    $rowIsEmpty = false;
                    $fieldValue->loadFromImport($value);
                }

                $attributeNames = array_diff($fieldValue->attributes(), [
                    'id',
                    'registry_table_record_id',
                ]);

                if (!$fieldValue->validate($attributeNames, false)) {
                    $lines = array_unique($fieldValue->getErrorSummary(true));
                    $lines = array_values($lines);
                    $lines = join(', ', $lines);
                    $recordErrors[] = $f($row, $col) . ' ' . $lines;
                }

                $tableRecord->editValues[$field->id] = $fieldValue;
                $col++;
            }

            if ($rowIsEmpty) {
                break;
            }

            $records[] = $tableRecord;

            if (!empty($recordErrors)) {
                $errors = array_merge($errors, $recordErrors);
            }

            $row++;
        }

        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors,
            ];
        }

        $importedRecords = 0;

        foreach ($records as $record) {
            if ($record->save()) {
                $importedRecords++;
            }
        }

        return [
            'success' => true,
            'importedCount' => $importedRecords,
            'totalCount' => count($records),
        ];
    }
}
