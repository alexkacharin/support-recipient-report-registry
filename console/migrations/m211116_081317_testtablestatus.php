<?php

use yii\db\Migration;

/**
 * Class m211116_081317_testtablestatus
 */
class m211116_081317_testtablestatus extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
			$this -> createTable('status',[
            'id' => $this -> primaryKey(),
            'title' => $this ->string(255),           
			]);
            Yii::$app->db->createCommand()->batchInsert('tip', ['title'], [
			['черновик'],
			['утвержден'],
			['на рассмотрении'],
			['удален'],			
		  ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this ->dropTable('status');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211116_081317_testtablestatus cannot be reverted.\n";

        return false;
    }
    */
}
