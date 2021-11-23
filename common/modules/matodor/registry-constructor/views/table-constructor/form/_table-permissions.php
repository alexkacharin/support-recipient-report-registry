<?php

use kartik\select2\Select2;
use Matodor\Common\components\Helper;use Matodor\RegistryConstructor\models\forms\TableForm;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/** @var View $this */
/** @var TableForm $tableForm */
/** @var ActiveForm $form */

?>

<?= Html::a('Разрешения таблицы', '#collapsePermissions', [
    'role' => 'button',
    'data-toggle' => 'collapse',
    'aria-expanded' => 'false',
    'aria-controls' => 'collapseSearch',
    'class' => Helper::filterCssClasses([
        'btn w-100' => true,
        'btn-secondary' => !$tableForm->hasErrors('editPermissions'),
        'btn-danger' => $tableForm->hasErrors('editPermissions'),
    ]),
]) ?>

<?= Html::beginTag('div', [
    'id' => 'collapsePermissions',
    'class' => Helper::filterCssClasses([
        'collapse' => true,
        'show' => $tableForm->hasErrors('editPermissions'),
    ]),
]) ?>
    <div class="mt-4">
        <div class="registry-table__form-permissions form-list">
            <?php foreach ($tableForm->editPermissions as $permission) { ?>
                <div class="registry-table__form-permission form-list__item" data-uid="<?= $permission->uid ?>">
                    <?= $this->render('_table-permission', [
                        'permission' => $permission,
                        'form' => $form,
                    ]) ?>
                </div>
            <?php } ?>
        </div>

        <div class="mt-2">
            <?= Html::button('Добавить разрешение', [
                'class' => 'btn btn-primary registry-table__form-add-permission-btn',
            ]) ?>
        </div>
    </div>
<?= Html::endTag('div') ?>

