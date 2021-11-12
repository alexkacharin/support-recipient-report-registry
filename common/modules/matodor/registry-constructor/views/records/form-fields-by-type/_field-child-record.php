<?php

use Matodor\RegistryConstructor\models\data\ChildRecord\ValueForm;
use Matodor\RegistryConstructor\models\TableField;
use yii\web\View;
use yii\bootstrap4\ActiveField;
use yii\bootstrap4\ActiveForm;

/** @var View $this */
/** @var TableField $field */
/** @var ValueForm $fieldValue */
/** @var ActiveForm $form */
/** @var ActiveField $formField */
/** @var bool|null $isInSearchForm */

?>

<?php if (!$isInSearchForm && $fieldValue->field->variantsTable->canAddRecords()) { ?>
    <div class="block-box border-light py-0">
        <div class="d-flex flex-column">
            <?= $this->render('/records/_fields', [
                'table' => $fieldValue->valueRecord->table,
                'tableRecord' => $fieldValue->valueRecord,
                'form' => $form,
            ]) ?>
        </div>
    </div>
<?php } ?>
