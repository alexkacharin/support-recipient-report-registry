<?php

use Matodor\Common\components\Helper;
use Matodor\RegistryConstructor\models\Table;
use Matodor\RegistryConstructor\models\TableField;
use Matodor\RegistryConstructor\models\TableRecord;
use Matodor\RegistryConstructor\Module;
use Matodor\RegistryConstructor\widgets\RecordCard\Widget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/** @var View $this */
/** @var TableRecord $tableRecord */
/** @var Table $table */
/** @var Widget $widget */

?>

<div class="row">
    <div class="col-12 col-md-4">
        <div class="registry-record-card__info">
            <div class="mb-2">
                <div class="m-n1">
                    <?php if ($tableRecord->canUpdate()) { ?>
                        <?= Html::a('<i class="fa fa-edit"></i>', Module::getInstance()->toRoute([
                            'records/edit',
                            'tableId' => $tableRecord->registry_table_id,
                            'recordId' => $tableRecord->primaryKey,
                        ]), [
                            'class' => 'btn btn-sm btn-primary m-1',
                            'target' => '_blank',
                        ]); ?>
                    <?php } ?>

                    <?php if ($tableRecord->canDelete()) { ?>
                        <?= Html::a('<i class="fa fa-trash"></i>', Module::getInstance()->toRoute([
                            'records/delete',
                            'tableId' => $tableRecord->registry_table_id,
                            'recordId' => $tableRecord->primaryKey,
                        ]), [
                            'class' => 'btn btn-sm btn-danger m-1',
                            'data' => [
                                'confirm' => 'Вы уверены, что хотите удалить этот элемент?',
                                'method' => 'post',
                            ],
                        ]) ?>
                    <?php } ?>
                </div>
            </div>

            <div>
                <b><?= $tableRecord->getAttributeLabel('id') ?></b>:
                <span class="badge badge-secondary">
                   #<?= $tableRecord->id ?>
                </span>
            </div>

            <div>
                <b><?= $tableRecord->getAttributeLabel('created_at') ?></b>:
                <span class="badge badge-secondary">
                    <?= $tableRecord->formattedCreatedAt ?>, <?= $tableRecord->getCreatedByUserTitle() ?>
                </span>
            </div>

            <?php if ($tableRecord->created_at !== $tableRecord->updated_at) { ?>
                <div>
                    <b><?= $tableRecord->getAttributeLabel('updated_at') ?></b>:
                    <span class="badge badge-info">
                        <?= $tableRecord->formattedUpdatedAt ?>, <?= $tableRecord->getUpdatedByUserTitle() ?>
                    </span>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="col-12 col-md-8 registry-record-card__fields">
        <?php foreach ($table->fields as $field) { ?>
            <?php
                if (!$field->hasFlag(TableField::FLAGS_IS_VISIBLE)) {
                    continue;
                }

                /** @noinspection PhpUnhandledExceptionInspection */
                $recordValue = $tableRecord->getValueOrInstantiate($field);
            ?>

            <div class="row no-gutters registry-record-card__field">
                <div class="col-12 col-md-4">
                    <div class="registry-record-card__field-header">
                        <?= $field->name ?>
                    </div>
                </div>
                <div class="col-12 col-md-8">
                    <?= Html::beginTag('div', [
                        'class' => Helper::filterCssClasses([
                            'registry-record-card__field-value' => true,
                            'empty' => !$recordValue->getIsValueSet(),
                        ]),
                    ]) ?>
                        <?= $widget->renderField($recordValue) ?>
                    <?= Html::endTag('div') ?>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
