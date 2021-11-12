<?php

use Matodor\RegistryConstructor\models\TableRecordValue;
use Matodor\RegistryConstructor\widgets\RecordCard\Widget;
use yii\web\View;

/** @var View $this */
/** @var TableRecordValue $fieldValue */
/** @var Widget $widget */

?>

<?php if ($fieldValue->getIsValueSet()) { ?>
    <?= $fieldValue->getFormattedValue() ?>
<?php } else { ?>
    <?= Yii::t('yii', '(not set)', [], Yii::$app->language) ?>
<?php }  ?>
