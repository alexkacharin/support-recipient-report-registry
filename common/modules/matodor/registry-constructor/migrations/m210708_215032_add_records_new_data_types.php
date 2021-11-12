<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

use Matodor\Common\components\Migration;

/**
 * Class m210708_215032_add_records_new_data_types
 */
class m210708_215032_add_records_new_data_types extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        foreach ([
            ['registry_table_records_data_date', 'value_date', $this->date()->null()],
            ['registry_table_records_data_datetime', 'value_datetime', $this->dateTime()->null()],
            ['registry_table_records_data_float', 'value_float', $this->float()->null()],
        ] as [$tableName, $columnName, $column]) {
            /**
             * Значение поля записи реестра
             */
            $this->createTable("{{%$tableName}}", [
                // ID
                'id' => $this->primaryKey(),
                // ID записи
                'registry_table_record_id' => $this->integer()->notNull(),
                // ID поля реестра
                'registry_table_field_id' => $this->integer()->notNull(),
                // Значение
                $columnName => $column,
                // Others
                'created_at' => $this->integer()->notNull(),
                'updated_at' => $this->integer()->notNull(),
            ]);

            $this->addForeignKey(
                "fk-$tableName-registry_table_record_id",
                "{{%$tableName}}",
                'registry_table_record_id',
                '{{%registry_table_records}}',
                'id',
                'CASCADE',
                'CASCADE'
            );

            $this->addForeignKey(
                "fk-$tableName-registry_table_field_id",
                "{{%$tableName}}",
                'registry_table_field_id',
                '{{%registry_table_fields}}',
                'id',
                'CASCADE',
                'CASCADE'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTableIfExist('{{%registry_table_records_data_float}}');
        $this->dropTableIfExist('{{%registry_table_records_data_datetime}}');
        $this->dropTableIfExist('{{%registry_table_records_data_date}}');
    }
}
