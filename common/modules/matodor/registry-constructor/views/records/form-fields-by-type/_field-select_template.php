<?php

use Matodor\RegistryConstructor\models\data\Select\ValueForm;
use Matodor\RegistryConstructor\models\TableField;
use yii\web\View;

/** @var View $this */
/** @var TableField $field */
/** @var ValueForm $fieldValue */
/** @var string $collapseId */

?>

{label}
<div class="d-flex flex-row align-items-center m-n1">
    <div class="flex-grow-1 m-1">{input}</div>
    <span title="Добавить" data-toggle="tooltip" data-placement="top">
        <a
            href="#<?= $collapseId ?>"
            class="btn btn-sm btn-success m-1 py-0 px-1 no-collapse-icon collapse-variant-form collapsed"
            aria-expanded="false"
            data-toggle="collapse"
        >
            <i class="fas fa-plus"></i>
        </a>
    </span>
</div>
{hint}
{error}
