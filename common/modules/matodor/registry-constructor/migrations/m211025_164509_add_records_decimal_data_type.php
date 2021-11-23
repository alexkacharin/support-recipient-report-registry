<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

use Matodor\Common\components\Migration;

/**
 * Class m211025_164509_add_records_decimal_data_type
 */
class m211025_164509_add_records_decimal_data_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        /**
         * Значение поля записи реестра
         */
        $this->createTable('{{%registry_table_records_data_decimal}}', [
            // ID
            'id' => $this->primaryKey(),
            // ID записи
            'registry_table_record_id' => $this->integer()->notNull(),
            // ID поля реестра
            'registry_table_field_id' => $this->integer()->notNull(),
            // Значение
            'value_decimal' => $this->decimal(15, 2)->null(),
            // Others
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk-registry_table_record_id',
            '{{%registry_table_records_data_decimal}}',
            'registry_table_record_id',
            '{{%registry_table_records}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-registry_table_field_id',
            '{{%registry_table_records_data_decimal}}',
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
        $this->dropTableIfExist('{{%registry_table_records_data_decimal}}');
    }
}
