<?php

use yii\db\Migration;

/**
 * Class m211116_141302_add_title_to_status_table
 */
class m211116_141302_add_title_to_status_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand()-> batchInsert('information_status', ['title'], [
            ['черновик'],
            ['утвержден'],
            ['на рассмотрении'],
            ['удален'],
        ])->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211116_141302_add_title_to_status_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211116_141302_add_title_to_status_table cannot be reverted.\n";

        return false;
    }
    */
}
