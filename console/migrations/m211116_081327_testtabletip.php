<?php

use yii\db\Migration;

/**
 * Class m211116_081327_testtabletip
 */
class m211116_081327_testtabletip extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
			$this -> createTable('tip',[
            'id' => $this -> primaryKey(),
            'title' => $this ->string(255),           
			]);
			$this->batchInsert('tip', ['title'], [
			['годовой'],
			[ 'полугодовой'],
		  ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
         $this ->dropTable('tip');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211116_081327_testtabletip cannot be reverted.\n";

        return false;
    }
    */
}
