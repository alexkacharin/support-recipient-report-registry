<?php

namespace Matodor\RegistryConstructor\widgets\RecordsViewerToolbar;

use Matodor\Common\traits\HasWidgetSlots;
use Matodor\RegistryConstructor\data\RecordsDataProvider;
use Matodor\RegistryConstructor\models\Table;
use Matodor\RegistryConstructor\models\TablePermissions;
use Matodor\Common\traits\HasCssContainer;
use Matodor\RegistryConstructor\traits\HasModulePropertiesTrait;
use Matodor\RegistryConstructor\widgets\RecordsViewer\Widget as RecordsViewer;
use Matodor\Common\components\Helper;
use Matodor\RegistryConstructor\widgets\RecordsViewerToolbar\assets\WidgetAssets;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class Widget extends \yii\base\Widget
{
    use HasModulePropertiesTrait;
    use HasCssContainer;
    use HasWidgetSlots;

    /**
     * @var string
     */
    public $columnClass = 'col-12 col-md-auto p-1';

    /**
     * @var string
     */
    public $searchBtnTarget = '#';

    /**
     * @var string
     */
    public $importBtnTarget = '#';

    /**
     * @var Table
     */
    public $table = null;

    /**
     * @var RecordsDataProvider
     */
    public $dataProvider = null;

    /**
     * @var string[]
     */
    public $viewTypeIcons = [
        RecordsViewer::VIEW_TYPE_CARDS => '<i class="fas fa-th-list"></i>',
        RecordsViewer::VIEW_TYPE_GRID => '<i class="fas fa-table"></i>',
    ];

    /**
     * @var array|false
     */
    public $sortLinkOptions = ['class' => 'dropdown-item dropdown-item_sort'];

    /**
     * @var string[]
     */
    public $viewTypes = [];

    /**
     * @var array|false|null
     */
    public $actions = [];

    /**
     * @var string|null
     */
    public $activeViewType = null;

    public function init()
    {
        parent::init();

        if ($this->actions === []) {
            $this->actions = $this->getDefaultActions();
        }
    }

    /**
     * @return array
     */
    public function getDefaultActions()
    {
        return [
            '<i class="fas fa-file-excel"></i> Экспорт XLSX' => [
                'url' => $this->module->toRoute([
                    'records/export',
                    'tableId' => $this->table->id,
                    'type' => 'xlsx',
                ]),
            ],
            '<i class="fas fa-file-csv"></i> Экспорт CSV' => [
                'url' => $this->module->toRoute([
                    'records/export',
                    'tableId' => $this->table->id,
                    'type' => 'csv',
                ]),
            ],
            '<i class="fas fa-file-import"></i> Импорт' => [
                'url' => '#',
                'options' => [
                    'data-toggle' => 'modal',
                    'data-target' => $this->importBtnTarget,
                ],
            ],
        ];
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function run()
    {
        WidgetAssets::register($this->view);

        return $this->renderContainer($this->renderRow(), 'records-viewer__toolbar');
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function renderRow()
    {
        $html = Html::beginTag('div', ['class' => 'row m-n1']);

        {
            if ($this->hasSlot('before')) {
                $html .= $this->getSlot('before');
            }

            if ($this->table->checkAccess(null, TablePermissions::CAN_ADD_RECORDS)) {
                $html .= $this->renderCol($this->renderAddBtn());
            }

            if ($this->searchBtnTarget) {
                $html .= $this->renderCol($this->renderSearchBtn());
            }

            if (!empty($this->viewTypes)) {
                $html .= $this->renderCol($this->renderViewTypeSelector());
            }

            if ($this->sortLinkOptions !== false) {
                $html .= $this->renderCol($this->renderSorterButtons());
            }

            if (!empty($this->actions)) {
                $html .= $this->renderCol($this->renderActionsButtons());
            }

            if ($this->hasSlot('after')) {
                $html .= $this->getSlot('after');
            }
        }

        $html .= Html::endTag('div');
        return $html;
    }

    /**
     * @param $content
     * @param null $class
     *
     * @return string
     */
    protected function renderCol($content, $class = null)
    {
        return Html::tag('div', $content, ['class' => $class ?? $this->columnClass]);
    }

    /**
     * @return string
     */
    protected function renderAddBtn()
    {
        return Html::a('Добавить запись', $this->module->toRoute([
            'records/create',
            'tableId' => $this->table->id,
        ]), [
            'class' => 'records-viewer__toolbar-add-btn btn btn-success w-100',
        ]);
    }

    /**
     * @return string
     */
    protected function renderSearchBtn()
    {
        return Html::a('Поиск', '#', [
            'class' => 'records-viewer__toolbar-search-btn btn btn-secondary w-100',
            'role' => 'button',
            'aria-expanded' => 'false',
            'aria-controls' => 'collapseSearch',
            'data-target' => $this->searchBtnTarget,
            'data-toggle' => 'collapse',
        ]);
    }

    /**
     * @return string
     */
    protected function renderViewTypeSelector()
    {
        $html = Html::beginTag('div', ['class' => 'btn-group']);

        {
            foreach ($this->viewTypes as $viewType) {
                $html .= $this->renderViewTypeBtn($viewType);
            }
        }


        $html .= Html::endTag('div');
        return $html;
    }

    /**
     * @param string $type
     *
     * @return string
     */
    protected function renderViewTypeBtn($type)
    {
        return Html::a($this->viewTypeIcons[$type], ArrayHelper::merge(Yii::$app->request->queryParams, [
            0 => '/' . Yii::$app->controller->route,
            'v' => $type,
        ]), [
            'class' => Helper::filterCssClasses([
                'btn btn-secondary' => true,
                'active' => $this->activeViewType === $type,
            ]),
        ]);
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function renderSorterButtons()
    {
        $html = Html::beginTag('div', ['class' => 'btn-group']);

        {
            $html .= Html::button('Сортировка', [
                'class' => 'records-viewer__toolbar-sorter-btn btn btn-secondary dropdown-toggle',
                'data-toggle' => 'dropdown',
                'aria-haspopup' => 'true',
                'aria-expanded' => 'false',
            ]);

            $html .= Html::beginTag('div', ['class' => 'dropdown-menu records-viewer__toolbar-sorter-dropdown']);

            {
                foreach ($this->dataProvider->sort->attributes as $attribute => $info) {
                    $html .= $this->dataProvider->sort
                        ->link($attribute, $this->sortLinkOptions);
                }
            }

            $html .= Html::endTag('div');
        }

        $html .= Html::endTag('div');
        return $html;
    }

    /**
     * @return string
     */
    protected function renderActionsButtons()
    {
        $html = Html::beginTag('div', ['class' => 'btn-group']);

        {
            $html .= Html::button('Действия', [
                'class' => 'records-viewer__toolbar-actions-btn btn btn-info dropdown-toggle',
                'data-toggle' => 'dropdown',
                'aria-haspopup' => 'true',
                'aria-expanded' => 'false',
            ]);

            $html .= Html::beginTag('div', ['class' => 'dropdown-menu']);

            {
                foreach ($this->actions as $text => $item) {
                    $options = $item['options'] ?? [];
                    Html::addCssClass($options, 'dropdown-item');
                    $html .= Html::a($text, $item['url'], $options);
                }
            }

            $html .= Html::endTag('div');
        }

        $html .= Html::endTag('div');
        return $html;
    }
}
