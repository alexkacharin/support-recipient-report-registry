<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

use Matodor\Common\components\Migration;

/**
 * Class m210703_110721_add_records_data_select
 */
class m210703_110721_add_records_data_select extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%registry_table_records_data_select}}', [
            // ID
            'id' => $this->primaryKey(),
            // ID записи
            'registry_table_record_id' => $this->integer()->notNull(),
            // ID поля реестра
            'registry_table_field_id' => $this->integer()->notNull(),
            // Значение
            'value_record_id' => $this->integer()->null(),
            // Others
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk-registry_table_records_data_select-registry_table_record_id',
            '{{%registry_table_records_data_select}}',
            'registry_table_record_id',
            '{{%registry_table_records}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-registry_table_records_data_select-value_record_id',
            '{{%registry_table_records_data_select}}',
            'value_record_id',
            '{{%registry_table_records}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-registry_table_records_data_select-registry_table_field_id',
            '{{%registry_table_records_data_select}}',
            'registry_table_field_id',
            '{{%registry_table_fields}}',
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
        $this->dropTableIfExist('{{%registry_table_records_data_select}}');
    }
}
