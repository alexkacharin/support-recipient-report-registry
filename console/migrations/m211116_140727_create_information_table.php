<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%information}}`.
 */
class m211116_140727_create_information_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%information}}', [
            'id' => $this->primaryKey(),
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
        $this->dropTable('{{%information}}');
    }
}
