<?php

use yii\db\Migration;

/**
 * Class m211115_173441_testtable
 */
class m211115_173441_testtable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this -> createTable('information',[
            'id' => $this -> primaryKey(),
            'title' => $this ->string(255),
            'documents' => $this -> string(255),
            'otchet' => $this -> string(255),
			'kol_vo_rabotnikov' => $this -> integer(),
			'period' => $this -> date(),            
			'status_id' => $this -> integer()->defaultValue(0),
			'tip_id' => $this -> integer(), 
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this ->dropTable('information');

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211115_173441_testtable cannot be reverted.\n";

        return false;
    }
    */
}
