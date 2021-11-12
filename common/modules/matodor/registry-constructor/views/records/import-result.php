<?php

use kartik\file\FileInput;
use Matodor\RegistryConstructor\models\forms\TableRecordsImportForm;
use Matodor\RegistryConstructor\models\Table;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/** @var View $this */
/** @var Table $table */
/** @var array $importResult */

$this->title = 'Импорт';
$this->params['breadcrumbs'][] = ['label' => $table->name, 'url' => ['index', 'tableId' => $table->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="records-import-result">
    <?php if ($importResult['success']) { ?>
        <div class="alert alert-success">
            Импорт успешно выполнен! Было импортировано <b><?= $importResult['importedCount'] ?>/<?= $importResult['totalCount'] ?></b> строк
        </div>
    <?php } else { ?>
        <div class="alert alert-danger">
            Импорт не выполнен! Исправьте ошибки и повторите!
        </div>

        <ul>
            <?php foreach ($importResult['errors'] as $error) { ?>
                <li><?= $error ?></li>
            <?php } ?>
        </ul>
    <?php } ?>
</div>

<div class="text-center">
    <h5>Повторить импорт</h5>
    <div class="row d-flex justify-content-center">
        <div class="col-12 col-md-6">
            <?= $this->render('_import-form', [
                'importForm' => new TableRecordsImportForm([
                    'table' => $table,
                ]),
            ]) ?>
        </div>
    </div>
</div>



