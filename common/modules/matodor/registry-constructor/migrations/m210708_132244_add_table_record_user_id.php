<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

use Matodor\Common\components\Migration;

/**
 * Class m210708_132244_add_table_record_user_id
 */
class m210708_132244_add_table_record_user_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%registry_table_records}}', 'user_created_id',
            $this->integer()->null());
        $this->addColumn('{{%registry_table_records}}', 'user_updated_id',
            $this->integer()->null());

        $this->addForeignKey(
            'fk-registry_table_records-user_created_id',
            '{{%registry_table_records}}',
            'user_created_id',
            '{{%user}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-registry_table_records-user_updated_id',
            '{{%registry_table_records}}',
            'user_updated_id',
            '{{%user}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%registry_table_records}}', 'user_updated_id');
        $this->dropColumn('{{%registry_table_records}}', 'user_created_id');
    }
}
