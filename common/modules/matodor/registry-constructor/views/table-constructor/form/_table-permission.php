<?php

use kartik\select2\Select2;
use Matodor\RegistryConstructor\models\forms\TablePermissionsForm;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/** @var View $this */
/** @var TablePermissionsForm $permission */
/** @var ActiveForm $form */

?>

<?= Html::activeHiddenInput($permission, 'id') ?>
<?= Html::activeHiddenInput($permission, 'registry_table_id') ?>

<?php if ($permission->hasErrors()) { ?>
    <?= Html::errorSummary($permission) ?>
<?php } ?>

<div class="row pt-2 mx-n1">
    <div class="col-md-12 px-1 mt-1">
        <?= $form->field($permission, 'role')->widget(Select2::class, [
            'data' => $permission->getRolesSelectData(),
            'pluginLoading' => false,
            'theme' => Select2::THEME_BOOTSTRAP,
            'options' => [
                'id' => uniqid('id'),
                'placeholder' => 'Не заполнено',
            ],
        ]); ?>

        <?= $form->field($permission, 'can_add_records')->checkbox() ?>
    </div>

    <div class="col-md-6 px-1 mt-2">
        <div class="registry-table__form-permission-group info px-2 py-1 rounded">
            <?= $form->field($permission, 'can_view_all_records')->checkbox() ?>
            <?= $form->field($permission, 'can_view_self_records')->checkbox() ?>
        </div>
    </div>

    <div class="col-md-6 px-1 mt-2">
        <div class="registry-table__form-permission-group success px-2 py-1 rounded">
            <?= $form->field($permission, 'can_edit_all_records')->checkbox() ?>
            <?= $form->field($permission, 'can_edit_self_records')->checkbox() ?>
        </div>
    </div>

    <div class="col-md-6 px-1 mt-2">
        <div class="registry-table__form-permission-group danger px-2 py-1 rounded">
            <?= $form->field($permission, 'can_delete_all_records')->checkbox() ?>
            <?= $form->field($permission, 'can_delete_self_records')->checkbox() ?>
        </div>
    </div>
</div>

<div class="py-2">
    <div class="form-list__item-buttons d-flex justify-content-end m-n1">
        <div class="m-1 px-3 btn btn-danger btn-sm form-list__item-remove-item-btn">
            <i class="fas fa-trash"></i>
        </div>
    </div>
</div>
