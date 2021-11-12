<?php

use Matodor\RegistryConstructor\models\Table;
use Matodor\RegistryConstructor\Module;
use yii\helpers\Html;
use yii\web\View;

/** @var Table $table */
/** @var View $this */

$this->title = "Удаление таблицы: #{$table->id} {$table->name}";
$this->params['breadcrumbs'][] = ['label' => 'Конструктор таблиц', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="table-constructor-confirm-delete">
    <div class="jumbotron">
        <div class="text-center">
            <h2>Вы уверены что хотите удалить таблицу "<?= $table->name ?>"?</h2>
            <h6 class="text-muted">Вместе с таблицей будут также удалены все записи в этой таблице.</h6>

            <div class="mt-4">
                <?= Html::a('<i class="fa fa-trash"></i> Удалить', Module::getInstance()->toRoute([
                    'table-constructor/delete',
                    'id' => $table->primaryKey,
                ]), [
                    'class' => 'btn btn-lg btn-danger',
                    'data-method' => 'post',
                    'data-confirm' => 'Вы уверены, что хотите удалить этот элемент?',
                ]) ?>

                <?= Html::a('<i class="fa fa-undo"></i> Назад', Module::getInstance()->toRoute([
                    'table-constructor/index',
                ]), [
                    'class' => 'btn btn-lg btn-info',
                ]) ?>
            </div>
        </div>
    </div>
</div>
