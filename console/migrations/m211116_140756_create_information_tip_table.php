<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%information_tip}}`.
 */
class m211116_140756_create_information_tip_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%information_tip}}', [
            'id' => $this->primaryKey(),
            'title' => $this ->string(255),
        ]);
        $this -> addForeignKey('tip_id','information','tip_id','information_tip','id','CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%information_tip}}');
        $this->dropForeignKey('tip_id','tip');
    }
}
