<?php

namespace Matodor\RegistryConstructor\widgets\RecordsViewer;

use Exception;
use Matodor\RegistryConstructor\data\RecordsDataProvider;
use Matodor\RegistryConstructor\models\Table;
use Matodor\RegistryConstructor\models\search\TableRecordSearch;
use Matodor\Common\traits\HasCssContainer;
use Matodor\Common\traits\HasWidgetSlots;
use yii\base\InvalidArgumentException;
use yii\helpers\Inflector;

class Widget extends \yii\base\Widget
{
    use HasCssContainer;
    use HasWidgetSlots;

    public const VIEW_TYPE_CARDS = 'cards';
    public const VIEW_TYPE_GRID = 'grid';

    /**
     * @var Table
     */
    public $table = null;

    /**
     * @var TableRecordSearch
     */
    public $searchModel = null;

    /**
     * @var RecordsDataProvider
     */
    public $dataProvider = null;

    /**
     * @var string
     */
    public $viewType = null;

    /**
     * @var string|\yii\base\Widget
     */
    public $listWidgetClass = \Matodor\RegistryConstructor\widgets\RecordsCardsList\Widget::class;

    /**
     * @var string|\yii\base\Widget
     */
    public $gridWidgetClass = \Matodor\RegistryConstructor\widgets\RecordsGrid\Widget::class;

    public function init()
    {
        parent::init();

        if (!in_array($this->viewType, $this->getViewTypes())) {
            $this->viewType = static::VIEW_TYPE_CARDS;
        }
    }

    /**
     * @return string[]
     */
    public function getViewTypes()
    {
        return [
            static::VIEW_TYPE_CARDS,
            static::VIEW_TYPE_GRID,
        ];
    }

    /**
     * @return string
     * @throws \yii\base\Exception
     */
    public function run()
    {
        $content = '';

        if ($this->hasSlot('before')) {
            $content .= $this->getSlot('before');
        }

        if ($this->dataProvider->getCount() > 0) {
            $content .= $this->renderContainer($this->renderListByViewType($this->viewType),
                'registry-records-viewer');
        } else {
            $content .= $this->renderContainer($this->renderEmptyPlaceholder(),
                'registry-records-viewer');
        }

        if ($this->hasSlot('after')) {
            $content .= $this->getSlot('after');
        }

        return $content;
    }

    /**
     * @noinspection MissedViewInspection
     */
    public function renderEmptyPlaceholder()
    {
        return $this->render('empty', [
            'dataProvider' => $this->dataProvider,
            'table' => $this->table,
        ]);
    }

    public function renderListByViewType($viewType)
    {
        $method = 'renderRecordsAs' . Inflector::camelize($viewType);

        if ($this->hasMethod($method)) {
            return call_user_func([$this, $method]);
        }

        throw new InvalidArgumentException('Wrong view type');
    }

    /**
     * @return string
     * @throws Exception
     * @noinspection PhpUnused
     */
    public function renderRecordsAsCards()
    {
        return $this->listWidgetClass::widget([
            'table' => $this->table,
            'dataProvider' => $this->dataProvider,
        ]);
    }

    /**
     * @return string
     * @throws Exception
     * @noinspection PhpUnused
     */
    public function renderRecordsAsGrid()
    {
        return $this->gridWidgetClass::widget([
            'table' => $this->table,
            'dataProvider' => $this->dataProvider,
        ]);
    }
}
