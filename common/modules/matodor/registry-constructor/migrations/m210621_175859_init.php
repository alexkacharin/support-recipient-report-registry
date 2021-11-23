<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

use Matodor\Common\components\Migration;

/**
 * Class m210621_175859_init
 */
class m210621_175859_init extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        /**
         * Список реестров
         */
        $this->createTable('{{%registry_tables}}', [
            // ID
            'id' => $this->primaryKey(),
            // Название таблицы
            'name' => $this->string()->notNull(),
            // Inline-template
            'inline_markup' => $this->string()->notNull()->defaultValue(''),
            // Тип
            'type' => $this->tinyInteger()->notNull()->unsigned()->defaultValue(0),
            // Отображение в меню
            'visible_in_menu' => $this->boolean()->notNull()->defaultValue(false),
            // Others
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('dx-registry_tables-name', '{{%registry_tables}}', 'name');

        /**
         * Список полей реестра
         */
        $this->createTable('{{%registry_table_fields}}', [
            // ID
            'id' => $this->primaryKey(),
            // ID реестра
            'registry_table_id' => $this->integer()->notNull(),
            // Сортировка (max 255)
            'sort' => $this->tinyInteger()->notNull()->unsigned()->defaultValue(0),
            // Имя поля
            'name' => $this->string()->notNull(),
            // Placeholder
            'placeholder' => $this->string()->null(),
            // Обязательное поле
            'required' => $this->boolean()->notNull()->defaultValue(true),
            // Тип (Ввод значения, выбор, множественный выбор)
            'type' => $this->tinyInteger()->notNull()->unsigned()->defaultValue(0),
            // Тип значения (число, строка, дата, файл и т.д)
            'value_type' => $this->tinyInteger()->notNull()->unsigned()->defaultValue(0),
            // Опции отображения
            'flags' => $this->integer()->notNull()->unsigned()->defaultValue(0),
            // Others
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk-registry_table_fields-registry_table_id',
            '{{%registry_table_fields}}',
            'registry_table_id',
            '{{%registry_tables}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createIndex('dx-registry_table_fields-name', '{{%registry_table_fields}}', [
            'registry_table_id',
            'sort',
        ]);

        /**
         * Запись в реестре
         */
        $this->createTable('{{%registry_table_records}}', [
            // ID записи
            'id' => $this->primaryKey(),
            // ID реестра
            'registry_table_id' => $this->integer()->notNull(),
            // Others
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk-registry_table_records-registry_table_id',
            '{{%registry_table_records}}',
            'registry_table_id',
            '{{%registry_tables}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        foreach ([
            ['registry_table_records_data_int', 'value_int', $this->integer()->null()],
            ['registry_table_records_data_string', 'value_string', $this->string()->null()],
            ['registry_table_records_data_text', 'value_text', $this->text()],
            ['registry_table_records_data_boolean', 'value_boolean', $this->boolean()->notNull()->defaultValue(false)],
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
        foreach ([
            'registry_table_records_data_int',
            'registry_table_records_data_string',
            'registry_table_records_data_text',
            'registry_table_records_data_boolean',
        ] as $tableName) {
            $this->dropTableIfExist("{{%$tableName}}");
        }

        $this->dropTableIfExist('{{%registry_table_records}}');
        $this->dropTableIfExist('{{%registry_table_fields}}');
        $this->dropTableIfExist('{{%registry_tables}}');
    }
}
