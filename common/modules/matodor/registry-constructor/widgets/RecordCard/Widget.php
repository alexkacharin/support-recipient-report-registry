<?php

namespace Matodor\RegistryConstructor\widgets\RecordCard;

use Matodor\RegistryConstructor\models\data\File\Value as FileValue;
use Matodor\RegistryConstructor\models\Table;
use Matodor\RegistryConstructor\models\TableRecord;
use Matodor\RegistryConstructor\models\TableRecordValue;
use Matodor\Common\traits\HasCssContainer;
use Matodor\RegistryConstructor\widgets\RecordCard\assets\WidgetAssets;
use yii\base\InvalidArgumentException;

class Widget extends \yii\base\Widget
{
    use HasCssContainer;

    /**
     * @var TableRecord
     */
    public $tableRecord;

    /**
     * @var Table
     */
    public $table;

    public function __construct($config = [])
    {
        $this->containerCssClass = 'block-box border-default';

        parent::__construct($config);
    }

    public function run()
    {
        parent::run();

        if ($this->tableRecord === null
            || $this->table === null
            || $this->table->id !== $this->tableRecord->registry_table_id
        ) {
            throw new InvalidArgumentException();
        }

        WidgetAssets::register($this->view);

        $this
            ->tableRecord
            ->populateRelationIfNeeded('table', $this->table);

        return $this->renderContainer($this->renderCard(), 'registry-record-card');
    }

    /**
     * @return string
     * @noinspection MissedViewInspection
     */
    public function renderCard()
    {
        return $this->render('card', [
            'tableRecord' => $this->tableRecord,
            'table' => $this->table,
            'widget' => $this,
        ]);
    }

    public function renderField(TableRecordValue $fieldValue)
    {
        $view = 'fields/_default';
        $params = [
            'fieldValue' => $fieldValue,
        ];

        if ($fieldValue instanceof FileValue) {
            $view = 'fields/_file';
        }

        return $this->render($view, $params);
    }
}
