<?php

use Matodor\RegistryConstructor\models\Table;
use Matodor\RegistryConstructor\models\TableRecord;
use yii\web\View;

/** @var Table $table */
/** @var TableRecord $record */
/** @var View $this */

?>

<div>
    <div><b>ID: </b> #<?= $record->id ?></div>
    <div data-toggle="tooltip" data-placement="top" title="<?= $record->getCreatedByUserTitle() ?>">
        <b>Создано: </b>
        <span><?= $record->formattedCreatedAt ?></span>
    </div>

    <div data-toggle="tooltip" data-placement="top" title="<?= $record->getUpdatedByUserTitle() ?>">
        <b>Обновлено: </b>
        <span><?= $record->formattedUpdatedAt ?></span>
    </div>
</div>
