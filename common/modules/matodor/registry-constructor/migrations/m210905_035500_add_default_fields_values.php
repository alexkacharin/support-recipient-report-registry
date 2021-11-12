<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

use Matodor\Common\components\Migration;

/**
 * Class m210905_035500_add_default_fields_values
 */
class m210905_035500_add_default_fields_values extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            '{{%registry_table_records}}',
            'is_system',
            $this->boolean()->defaultValue(false),
        );

        $this->createIndex(
            'dx-registry_table_records-created_at',
            '{{%registry_table_records}}',
            'created_at'
        );

        $this->createIndex(
            'dx-registry_table_records-updated_at',
            '{{%registry_table_records}}',
            'updated_at'
        );

        $this->createIndex(
            'dx-registry_table_records-main-1',
            '{{%registry_table_records}}', [
                'is_system',
                'created_at',
            ]
        );

        $this->createIndex(
            'dx-registry_table_records-main-2',
            '{{%registry_table_records}}', [
                'is_system',
                'updated_at',
            ]
        );

        $this->createIndex(
            'dx-registry_table_records-main-3',
            '{{%registry_table_records}}', [
                'is_system',
                'user_created_id',
                'created_at',
            ]
        );

        $this->createIndex(
            'dx-registry_table_records-main-4',
            '{{%registry_table_records}}', [
                'is_system',
                'user_created_id',
                'updated_at',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('dx-registry_table_records-main-4', '{{%registry_table_records}}');
        $this->dropIndex('dx-registry_table_records-main-3', '{{%registry_table_records}}');
        $this->dropIndex('dx-registry_table_records-main-2', '{{%registry_table_records}}');
        $this->dropIndex('dx-registry_table_records-main-1', '{{%registry_table_records}}');
        $this->dropIndex('dx-registry_table_records-updated_at', '{{%registry_table_records}}');
        $this->dropIndex('dx-registry_table_records-created_at', '{{%registry_table_records}}');
        $this->dropColumn('{{%registry_table_records}}', 'is_system');
    }
}
