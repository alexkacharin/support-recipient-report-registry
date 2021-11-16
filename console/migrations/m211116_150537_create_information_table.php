<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%information}}`.
 */
class m211116_150537_create_information_table extends Migration
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
            'information_tip_id' => $this -> integer()->defaultValue(0),
            'information_status_id' => $this -> integer(),
        ]);
        $this->createIndex(
            'idx-information_information_tip',
            'information',
            'information_tip_id'
        );
        $this -> addForeignKey(
                'fk-information_tip',
                 'information',
                'information_tip_id',
                'information_tip',
                'id',
                'CASCADE',
                'CASCADE'
        );
        $this->createIndex(
            'idx-information_information_status',
            'information',
            'information_tip_id'
        );
        $this -> addForeignKey(
            'fk-information_status',
            'information',
            'information_status_id',
            'information_status',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-information_status',
            'information'
        );
        $this->dropIndex(
            'idx-information_information_status',
            'information'
        );
        $this->dropForeignKey(
            'fk-information_tip',
            'information'
        );
        $this->dropIndex(
            'idx-information_information_tip',
            'information'
        );
        $this->dropTable('{{%information}}');
    }
}
