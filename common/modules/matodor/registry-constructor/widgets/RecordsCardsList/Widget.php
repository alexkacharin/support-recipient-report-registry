<?php

namespace Matodor\RegistryConstructor\widgets\RecordsCardsList;

use Matodor\RegistryConstructor\data\RecordsDataProvider;
use Matodor\RegistryConstructor\models\Table;
use Matodor\RegistryConstructor\models\TableRecord;
use Matodor\Common\traits\HasCssContainer;
use Matodor\RegistryConstructor\widgets\RecordsCardsList\assets\WidgetAssets;

class Widget extends \yii\base\Widget
{
    use HasCssContainer;

    /**
     * @var Table
     */
    public $table = null;

    /**
     * @var RecordsDataProvider
     */
    public $dataProvider = null;

    /**
     * @return string
     */
    public function run()
    {
        WidgetAssets::register($this->view);

        return $this->renderContainer($this->renderRecordsCards(),
            'registry-records registry-records_cards');
    }

    public function renderRecordsCards()
    {
        $out = '';

        foreach($this->dataProvider->getModels() as $tableRecord) {
            $out .= $this->renderRecordCard($tableRecord);
        }

        return $out;
    }

    public function renderRecordCard(TableRecord $record)
    {
        return \Matodor\RegistryConstructor\widgets\RecordCard\Widget::widget([
            'table' => $this->table,
            'tableRecord' => $record,
        ]);
    }
}
