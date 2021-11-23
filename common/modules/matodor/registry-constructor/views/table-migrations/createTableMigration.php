<?php

use Matodor\RegistryConstructor\models\Table;

/** @var string $className */
/** @var Table $table */


?>

<?php echo "<?php\n"; ?>

use \Matodor\RegistryConstructor\components\TableMigration;

/**
 *
 */
class <?= $className ?> extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
<?= $this->render('_createTable', [
    'table' => $table,
    'fields' => $fields,
    'foreignKeys' => $foreignKeys,
])
?>
<?php if (!empty($tableComment)) {
    echo $this->render('_addComments', [
        'table' => $table,
        'tableComment' => $tableComment,
    ]);
}
?>
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
<?= $this->render('_dropTable', [
    'table' => $table,
    'foreignKeys' => $foreignKeys,
])
?>
    }
}
