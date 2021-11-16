<?php

use yii\db\Migration;

/**
 * Class m211116_102610_testtableforreinkey
 */
class m211116_102610_testtableforreinkey extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this -> addForeignKey('status_id','information','status_id','status','id','CASCADE');
        $this -> addForeignKey('tip_id','information','tip_id','tip','id','CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('status_id','status');
        $this->dropForeignKey('tip_id','tip');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211116_102610_testtableforreinkey cannot be reverted.\n";

        return false;
    }
    */
}
