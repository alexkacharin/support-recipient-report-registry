<?php

use Matodor\RegistryConstructor\models\data\Select\Settings;
use Matodor\RegistryConstructor\models\forms\TableFieldForm;
use yii\web\View;

/** @var View $this */
/** @var TableFieldForm $tableFieldForm */
/** @var Settings $settings */
/** @var string $templateId */

?>

<div class="mb-1">
    <div class="d-flex flex-row flex-wrap align-items-center justify-content-start m-n1">
        <?php foreach ($tableFieldForm->variantsTable->fields as $variantField) { ?>
            <span class="badge badge-primary m-1 registry-table__form-field-template-variant"
                data-name-template="<?= $settings->getFieldNameTemplate($variantField) ?>"
                data-target-input="<?= "#{$templateId}" ?>"
            >
                <?= $settings->getFieldNameTemplate($variantField) ?>
            </span>
        <?php } ?>
    </div>
</div>
