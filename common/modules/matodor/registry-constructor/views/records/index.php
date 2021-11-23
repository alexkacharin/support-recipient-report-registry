<?php

/** @noinspection MissedViewInspection */

use Matodor\Common\widgets\DataProviderSummary;
use Matodor\RegistryConstructor\data\RecordsDataProvider;
use Matodor\RegistryConstructor\models\forms\TableRecordsImportForm;
use Matodor\RegistryConstructor\models\search\TableRecordSearch;
use Matodor\RegistryConstructor\models\Table;
use Matodor\RegistryConstructor\Module;
use Matodor\RegistryConstructor\widgets\RecordsSearch\Widget as RecordsSearch;
use Matodor\RegistryConstructor\widgets\RecordsViewerToolbar\Widget as RecordsViewerToolbar;
use Matodor\RegistryConstructor\widgets\RecordsViewer\Widget as RecordsViewer;
use yii\bootstrap4\LinkPager;
use yii\web\View;

/** @var Table $table */
/** @var TableRecordSearch $searchModel */
/** @var View $this */
/** @var RecordsDataProvider $dataProvider */
/** @var string $viewType */

$this->title = $table->name;
$this->params['breadcrumbs'][] = $this->title;

$searchCollapseId = uniqid('id-');
$importModalId = uniqid('id-');

?>

<div class="registry-constructor-records-index d-flex flex-column flex-grow-1">
    <?php $recordsViewer = $table->widget_class_viewer::begin([
        'table' => $table,
        'dataProvider' => $dataProvider,
        'searchModel' => $searchModel,
        'viewType' => $viewType,
    ]) ?>
        <!-- BEFORE BLOCK START -->
        <?php $this->params['bodyCssClass'] = $recordsViewer->viewType == RecordsViewer::VIEW_TYPE_GRID
            ? 'body-overflow-hidden'
            : false;
        ?>

        <?php $recordsViewer->beginSlot('before') ?>
            <?php $toolbar = $table->widget_class_toolbar::begin([
                'searchBtnTarget' => "#$searchCollapseId",
                'importBtnTarget' => "#$importModalId",
                'viewTypes' => $recordsViewer->getViewTypes(),
                'activeViewType' => $recordsViewer->viewType,
                'table' => $recordsViewer->table,
                'dataProvider' => $dataProvider,
            ]) ?>
                <?php $toolbar->beginSlot('after') ?>
                    <div class="col-12 col-md-auto p-1 flex-grow-1 d-flex justify-content-end">
                        <?php $dataProvider->prepare() ?>
                        <?= LinkPager::widget([
                            'options' => ['class' => 'my-0 pagination'],
                            'disabledListItemSubTagOptions' => ['class' => 'page-link'],
                            'listOptions' => ['class' => ['my-0 pagination']],
                            'linkContainerOptions' => ['class' => 'page-item'],
                            'linkOptions' => ['class' => 'page-link'],
                            'pagination' => $dataProvider->pagination,
                            'hideOnSinglePage' => false,
                            'view' => $this,
                        ]); ?>
                    </div>
                <?php $toolbar->endSlot('after') ?>
            <?php RecordsViewerToolbar::end() ?>

            <div class="collapse" id="<?= $searchCollapseId ?>">
                <div class="mt-3">
                    <?= $table->widget_class_search::widget([
                        'searchModel' => $searchModel,
                        'actionUrl' => Module::getInstance()->toRoute([
                            'records/index',
                            'tableId' => $table->id,
                            'v' => $recordsViewer->viewType,
                        ]),
                    ]) ?>
                </div>
            </div>

            <div class="my-2 text-center">
                <?= DataProviderSummary::widget([
                    'dataProvider' => $dataProvider,
                ]) ?>
            </div>
        <?php $recordsViewer->endSlot('before') ?>
        <!-- BEFORE BLOCK START -->

        <!-- AFTER BLOCK START -->
        <?php $recordsViewer->beginSlot('after') ?>

        <?php $recordsViewer->endSlot('after') ?>
        <!-- AFTER BLOCK END -->
    <?php RecordsViewer::end() ?>

    <div class="modal fade" id="<?= $importModalId ?>" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Импорт данных</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?= $this->render('_import-form', [
                        'importForm' => Yii::createObject([
                            'class' => TableRecordsImportForm::class,
                            'table' => $table,
                        ]),
                    ]) ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>
</div>
