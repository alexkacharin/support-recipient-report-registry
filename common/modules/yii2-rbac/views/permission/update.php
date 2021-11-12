<?php

use dektrium\rbac\models\Role;
use yii\web\View;

/** @var Role $model */
/** @var View $this */

$this->title = Yii::t('rbac', 'Update permission');
$this->params['breadcrumbs'][] = $this->title;

?>

<?php $this->beginContent('@dektrium/rbac/views/layout.php') ?>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
<?php $this->endContent() ?>
