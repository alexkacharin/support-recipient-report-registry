<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%information_status}}`.
 */
class m211116_140803_create_information_status_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%information_status}}', [
            'id' => $this->primaryKey(),
            'title' => $this ->string(255),
        ]);
        $this -> addForeignKey('status_id','information','status_id','information_status','id','CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%information_status}}');
        $this->dropForeignKey('status_id','status');
    }
}
