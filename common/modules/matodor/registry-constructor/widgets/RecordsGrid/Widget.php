<?php

namespace Matodor\RegistryConstructor\widgets\RecordsGrid;

use Matodor\RegistryConstructor\data\RecordsDataProvider;
use Matodor\RegistryConstructor\models\Table;
use Matodor\RegistryConstructor\models\TableField;
use Matodor\RegistryConstructor\models\TableRecord;
use Matodor\RegistryConstructor\models\TableRecordValue;
use Matodor\RegistryConstructor\traits\HasModulePropertiesTrait;
use Matodor\RegistryConstructor\widgets\RecordsGrid\assets\ResizableGridAssets;
use Matodor\RegistryConstructor\widgets\RecordsGrid\assets\WidgetAssets;
use Matodor\Common\traits\HasCssContainer;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;

class Widget extends \yii\base\Widget
{
    use HasCssContainer;
    use HasModulePropertiesTrait;

    /**
     * @var Table
     */
    public $table = null;

    /**
     * @var RecordsDataProvider
     */
    public $dataProvider = null;

    /**
     * @var array
     */
    public $tableOptions = ['class' => 'table'];

    /**
     * @var array
     */
    public $cellOptions = [];

    /**
     * @var array
     */
    public $rowOptions = [];

    /**
     * @var bool
     */
    public $stickyHeader = true;

    /**
     * @var bool
     */
    public $renderInfoColumn = true;

    /**
     * @var bool
     */
    public $renderActionsColumn = true;

    /**
     * @var bool
     */
    public $resizable = true;

    /**
     * @return string
     * @throws InvalidConfigException
     */
    public function run()
    {
        WidgetAssets::register($this->view);

        if ($this->resizable) {
            ResizableGridAssets::register($this->view);
        }

        return $this->renderContainer($this->renderTable(),
            'registry-records registry-records_grid');
    }

    /**
     * @return string
     * @throws InvalidConfigException
     */
    public function renderTable()
    {
        $options = $this->tableOptions;
        $options['data-table-id'] = $this->table->id;
        Html::addCssClass($options, 'registry-records_grid__table');

        if ($this->resizable) {
            Html::addCssClass($options, 'registry-records_grid__table_resizable');
            Html::addCssClass($options, 'registry-records_grid__table_resizable-loading');
        }

        $html = Html::beginTag('table', $options);
        {
            $html .= $this->renderTableHead();
            $html .= $this->renderTableBody();
        }
        $html .= Html::endTag('table');
        return $html;
    }

    /**
     * @return string
     */
    public function renderTableHead()
    {
        $html = Html::beginTag('thead');
        {
            $rowOptions = $this->rowOptions;
            Html::addCssClass($rowOptions, 'registry-records_grid__row');

            $html .= Html::beginTag('tr', $rowOptions);
            {
                if ($this->renderActionsColumn) {
                    $html .= $this->renderTableHeadCell('a', '#');
                }

                if ($this->renderInfoColumn) {
                    $html .= $this->renderTableHeadCell('id', 'Инфо');
                }

                foreach ($this->table->fields as $field) {
                    if (!$field->hasFlag(TableField::FLAGS_IS_VISIBLE)) {
                        continue;
                    }

                    $html .= $this->renderTableHeadCell($field->id, $field->name);
                }
            }
            $html .= Html::endTag('tr');
        }
        $html .= Html::endTag('thead');
        return $html;
    }

    public function renderTableHeadCell($columnId, $content)
    {
        $cellOptions = $this->cellOptions;
        $cellOptions['data-field-id'] = $columnId;
        Html::addCssClass($cellOptions, 'registry-records_grid__cell');

        if ($this->stickyHeader) {
            Html::addCssClass($cellOptions, 'registry-records_grid__cell_sticky');
        }

        return Html::tag('th', $content, $cellOptions);;
    }

    /**
     * @return string
     * @throws InvalidConfigException
     */
    public function renderTableBody()
    {
        $html = Html::beginTag('tbody');
        {
            foreach ($this->dataProvider->getModels() as $tableRecord) {
                $html .= $this->renderTableRow($tableRecord);
            }
        }
        $html .= Html::endTag('tbody');
        return $html;
    }

    /**
     * @param TableRecord $record
     *
     * @return string
     * @throws InvalidConfigException
     */
    public function renderTableRow(TableRecord $record)
    {
        $emptyValuePlaceholder = Yii::t('yii', '(not set)', [], Yii::$app->language);
        $options = $this->rowOptions;
        $options['data-record-id'] = $record->id;
        Html::addCssClass($options, 'registry-records_grid__row');

        $html = Html::beginTag('tr', $options);
        {
            if ($this->renderActionsColumn) {
                $html .= $this->renderTableActionsCell($record);
            }

            if ($this->renderInfoColumn) {
                $html .= $this->renderTableInfoCell($record);
            }

            foreach ($this->table->fields as $field) {
                if (!$field->hasFlag(TableField::FLAGS_IS_VISIBLE)) {
                    continue;
                }

                $recordValue = $record->getValueOrInstantiate($field);
                $cssClass = $recordValue->field->getValueTypeDefinition()->valueTypeUnderscored;
                $cssClass = "registry-records_grid__cell_{$cssClass}";

                /** @noinspection PhpUnusedLocalVariableInspection */
                $content = null;

                if ($recordValue->getIsValueSet()) {
                    $content = $this->renderRecordValue($record, $recordValue);
                } else {
                    $content = $emptyValuePlaceholder;
                    $cssClass .= ' empty';
                }

                $html .= $this->renderTableCell($content, $cssClass);
            }
        }
        $html .= Html::endTag('tr');
        return $html;
    }

    /**
     * @param TableRecord $record
     *
     * @return string
     */
    public function renderTableActionsCell(TableRecord $record)
    {
        $content = '';

        if ($record->canUpdate()) {
            $url = $this->module->toRoute([
                'records/edit',
                'tableId' => $record->registry_table_id,
                'recordId' => $record->primaryKey,
            ]);

            $content .= Html::a('<i class="fa fa-edit"></i>', $url, [
                'class' => 'btn btn-sm btn-primary m-1',
                'target' => '_blank',
            ]);
        }

        if ($record->canDelete()) {
            $url = $this->module->toRoute([
                'records/delete',
                'tableId' => $record->registry_table_id,
                'recordId' => $record->primaryKey,
            ]);

            $content .= Html::a('<i class="fa fa-trash"></i>', $url, [
                'class' => 'btn btn-sm btn-danger m-1',
                'target' => '_blank',
                'data-confirm' => 'Вы уверены, что хотите удалить этот элемент?',
                'data-method' => 'post',
            ]);
        }

        return $this->renderTableCell($content);
    }

    /**
     * @param TableRecord $record
     *
     * @return string
     */
    public function renderTableInfoCell(TableRecord $record)
    {
        /** @noinspection MissedViewInspection */
        $content = $this->render('_column-info', [
            'table' => $this->table,
            'record' => $record,
        ]);
        return $this->renderTableCell($content);
    }

    /**
     * @param string $content
     * @param string|false $cssClass
     *
     * @return string
     */
    public function renderTableCell(string $content, $cssClass = false)
    {
        $options = $this->cellOptions;
        Html::addCssClass($options, 'registry-records_grid__cell');

        if ($cssClass !== false) {
            Html::addCssClass($options, $cssClass);
        }

        return Html::tag('td', $content, $options);
    }

    /**
     * @param TableRecord $record
     * @param TableRecordValue $recordValue
     *
     * @return string
     */
    public function renderRecordValue(TableRecord $record, TableRecordValue $recordValue)
    {
        return $recordValue->getValueWithMarkup();
    }
}
