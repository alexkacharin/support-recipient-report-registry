<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

use Matodor\Common\components\Migration;

/**
 * Class m210709_184031_add_records_files_data_type
 */
class m210709_184031_add_records_files_data_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        /**
         * Значение поля записи реестра
         */
        $this->createTable('{{%registry_table_records_data_file}}', [
            // ID
            'id' => $this->primaryKey(),
            // ID записи
            'registry_table_record_id' => $this->integer()->notNull(),
            // ID поля реестра
            'registry_table_field_id' => $this->integer()->notNull(),
            // Значение
            'file_token' => $this->string(32)->null(),
            'file_ext' => $this->string()->null(),
            'file_name' => $this->string()->null(),
            // Others
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk-registry_table_records_data_file-registry_table_record_id',
            '{{%registry_table_records_data_file}}',
            'registry_table_record_id',
            '{{%registry_table_records}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-registry_table_records_data_file-registry_table_field_id',
            '{{%registry_table_records_data_file}}',
            'registry_table_field_id',
            '{{%registry_table_fields}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createIndex('dx-records_data_file-file_token',
            '{{%registry_table_records_data_file}}',
            'file_token'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTableIfExist('{{%registry_table_records_data_file}}');
    }
}
