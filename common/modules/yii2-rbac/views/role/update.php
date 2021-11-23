<?php

use dektrium\rbac\models\Role;
use yii\web\View;

/** @var View $this */
/** @var Role $model */

$this->title = Yii::t('rbac', 'Update role');
$this->params['breadcrumbs'][] = $this->title;

?>

<?php $this->beginContent('@dektrium/rbac/views/layout.php') ?>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
<?php $this->endContent() ?>
