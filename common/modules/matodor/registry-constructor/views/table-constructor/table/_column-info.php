<?php

use Matodor\RegistryConstructor\models\Table;
use Matodor\RegistryConstructor\models\TablePermissions;
use yii\helpers\Html;
use yii\web\View;

/** @var Table $table */
/** @var View $this */

$usedByTables = $table
    ->getUsedByTables()
    ->select(['{{%registry_tables}}.name'])
    ->column();

$fields = $table
    ->getFields()
    ->select(['name'])
    ->column();

?>

<?php if (Yii::$app->user->can('superadmin')) { ?>
    <div>
        <span>Токен таблицы: </span>
        <span class="badge badge-info"><?= $table->token ?></span>
    </div>
<?php } ?>

<div>
    <span>Количество записей: </span> <?= $table->recordsCount ?>
</div>

<?php if (!empty($usedByTables)) { ?>
    <div>
        <span>Используется в справочниках: </span>
        <?php foreach ($usedByTables as $tableName) { ?>
            <span class="badge badge-info"><?= $tableName ?></span>
        <?php } ?>
    </div>
<?php } ?>

<div>
    <span>Поля таблицы: </span>
    <?php foreach ($fields as $fieldName) { ?>
        <span class="badge badge-secondary"><?= $fieldName ?></span>
    <?php } ?>
</div>

<div>
    <span>Разрешения таблицы: </span>
    <?php if (empty($table->permissions)) { ?>
        <span class="badge badge-danger">Разрешения не заданы</span>
    <?php } else { ?>
        <?php foreach ($table->permissions as $permission) { ?>
            <?php
                $title = join("\n", array_map(function ($item) use ($permission) {
                    $label = TablePermissions::instance()->getAttributeLabel($item);
                    $state = $permission->isAllowed($item)
                        ? '<span class="badge badge-success">Да</span>'
                        : '<span class="badge badge-danger">Нет</span>';
                    $state = Html::encode($state);
                    return "<div>{$label} - {$state}</div>";
                }, TablePermissions::getPermissionsList()));
            ?>

            <span
                class="badge badge-secondary"
                title="<?= $title ?>"
                data-toggle="tooltip"
                data-placement="top"
                data-html="true"
            >
                <?= $permission->mainRole ?>
            </span>
        <?php } ?>
    <?php } ?>
</div>
