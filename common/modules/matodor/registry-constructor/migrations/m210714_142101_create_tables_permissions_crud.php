<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

use Matodor\Common\components\Migration;

/**
 * Class m210714_142101_create_tables_permissions_crud
 */
class m210714_142101_create_tables_permissions_crud extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%registry_table_permissions}}', [
            // ID
            'id' => $this->primaryKey(),
            // ID реестра
            'registry_table_id' => $this->integer()->notNull(),
            //
            'special_role' => $this->string(1)->null(),
            // role
            'role' => $this->string(64)->null(),

            'can_add_records' => $this->boolean()->notNull()->defaultValue(false),
            'can_view_all_records' => $this->boolean()->notNull()->defaultValue(false),
            'can_view_self_records' => $this->boolean()->notNull()->defaultValue(false),
            'can_edit_self_records' => $this->boolean()->notNull()->defaultValue(false),
            'can_edit_all_records' => $this->boolean()->notNull()->defaultValue(false),
            'can_delete_self_records' => $this->boolean()->notNull()->defaultValue(false),
            'can_delete_all_records' => $this->boolean()->notNull()->defaultValue(false),

            // Others
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk-registry_table_permissions-registry_table_id',
            '{{%registry_table_permissions}}',
            'registry_table_id',
            '{{%registry_tables}}',
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
        $this->dropTableIfExist('{{%registry_table_permissions}}');
    }
}
