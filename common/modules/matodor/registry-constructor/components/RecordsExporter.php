<?php

/** @noinspection MissedViewInspection */

namespace Matodor\RegistryConstructor\components;

use Matodor\RegistryConstructor\models\Table;
use Matodor\RegistryConstructor\models\TableField;
use Matodor\RegistryConstructor\models\TableRecord;
use Matodor\RegistryConstructor\queries\TableRecordQuery;
use Matodor\RegistryConstructor\traits\HasSpreadsheetTrait;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\BaseWriter;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yii;
use yii\base\Component;
use yii\base\InvalidArgumentException;
use yii\web\Response;

class RecordsExporter extends Component
{
    use HasSpreadsheetTrait;

    /**
     * @var TableRecordQuery
     */
    public $query;

    /**
     * @var Table
     */
    public $table;

    /**
     * @var Spreadsheet
     */
    protected $spreadsheet;

    public function export()
    {
        $this->spreadsheet = new Spreadsheet();
        $this->spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(15);
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->getSheetView()->setZoomScale(75);

        $columns = [];
        $columns[] = [
            'header' => '#',
            'width' => 5,
            'extractValue' => function ($record, $index) {
                /** @var TableRecord $record */
                return $index + 1;
            },
        ];

        foreach ($this->table->fields as $field) {
            if (!$field->hasFlag(TableField::FLAGS_EXPORT_FIELD)) {
                continue;
            }

            $column = [
                'header' => $field->name,
                'width' => 20,
                'extractValue' => function ($record, $index) use ($field) {
                    /** @var TableRecord $record */
                    $fieldValue = $record->getValueOrInstantiate($field);

                    return $fieldValue->getIsValueSet()
                        ? $fieldValue->getExportValue()
                        : null;
                },
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
        $cols = count($columns);

        // Заполнение шапки
        $sheet->setCellValueByColumnAndRow($col, $row, "Экспорт таблицы: {$this->table->name}");
        $sheet->mergeCellsByColumnAndRow($col, $row, $col + $cols - 1, $row);
        $row++;

        $sheet->setCellValueByColumnAndRow($col, $row, 'Дата экспорта: ' . date('d.m.Y', time()));
        $sheet->mergeCellsByColumnAndRow($col, $row, $col + $cols - 1, $row);
        $row++;

        $this->setBackground('CFE7F5', $col, $row - 2, $col + $cols - 1, $row);
        $this->setAlignment(['horizontal' => Alignment::HORIZONTAL_LEFT], $col, $row - 2, $col + $cols - 1, $row);
        $this->setOutlineBorder(Border::BORDER_THICK, $col, $row - 2, $col + $cols - 1, $row);
        $this->setFontWeightBold($col, $row - 2, $col + $cols - 1, $row);

        foreach ($columns as $column) {
            $sheet->setCellValueByColumnAndRow($col, $row, $column['header']);
            $sheet->getColumnDimensionByColumn($col)->setWidth($column['width']);
            $col++;
        }

        $col = 1;
        $this->setAlignment([
            'horizontal' => Alignment::HORIZONTAL_LEFT,
            'wrapText' => true,
        ], $col, $row, $col + $cols - 1, $row);

        $row++;
        $recordsRowStart = $row;
        $recordIndex = 0;

        // Заполнение строк
        foreach ($this->query->each() as $record) {
            $col = 1;

            foreach ($columns as $column) {
                $value = $column['extractValue']($record, $recordIndex);
                if ($value !== null) {
                    $sheet->setCellValueByColumnAndRow($col, $row, $value);
                }
                $col++;
            }

            $recordIndex++;
            $row++;
        }

        $col = 1;
        $this->setAllBorder(Border::BORDER_THIN, $col, $recordsRowStart - 1, $col + $cols - 1, $row - 1);
        $this->setAlignment([
            'horizontal' => Alignment::HORIZONTAL_LEFT,
            'vertical' => Alignment::VERTICAL_TOP,
            'wrapText' => true,
        ], $col, $recordsRowStart - 1, $col + $cols - 1, $row - 1);

        return $this;
    }

    public function responseAsFile($fileName = null, $type = 'xlsx')
    {
        if (!in_array($type, ['xlsx', 'csv'])) {
            throw new InvalidArgumentException();
        }

        if ($fileName === null) {
            $fileName = 'Экспорт-' . date('Y-m-d-H-i') . '.' . $type;
        }

        /** @var BaseWriter $writer */
        /** @noinspection PhpUnusedLocalVariableInspection */
        $writer = null;
        $filePath = Yii::getAlias('@app/runtime/' . $fileName);

        if ($type === 'xlsx') {
            $writer = new Xlsx($this->spreadsheet);
        } else {
            $writer = new Csv($this->spreadsheet);
        }

        $writer->setOffice2003Compatibility(false);
        $writer->save($filePath);

        Yii::$app->response
            ->sendFile($filePath, $fileName)
            ->on(Response::EVENT_AFTER_SEND, function($event) {
                unlink($event->data);
            }, $filePath);
    }
}
