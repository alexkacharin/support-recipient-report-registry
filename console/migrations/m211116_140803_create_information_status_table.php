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
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%information_status}}');
    }
}
