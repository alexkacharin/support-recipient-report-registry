<?php

use dektrium\rbac\models\Rule;
use yii\web\View;

/** @var Rule $model */
/** @var View $this */

$this->title = Yii::t('rbac', 'Update rule');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac', 'Rules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<?php $this->beginContent('@dektrium/rbac/views/layout.php') ?>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
<?php $this->endContent() ?>
