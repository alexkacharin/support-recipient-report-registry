<?php

use Matodor\RegistryConstructor\models\data\File\Value;
use Matodor\RegistryConstructor\Module;
use Matodor\RegistryConstructor\widgets\RecordCard\Widget;
use yii\helpers\Html;
use yii\web\View;

/** @var View $this */
/** @var Value $fieldValue */
/** @var Widget $widget */

?>

<?= $fieldValue->getFormattedValue() ?>

<?php if ($fieldValue->getIsValueSet()) { ?>
    <?= Html::a('Скачать', Module::getInstance()->toRoute($fieldValue->downloadRoute)) ?>
<?php } ?>
