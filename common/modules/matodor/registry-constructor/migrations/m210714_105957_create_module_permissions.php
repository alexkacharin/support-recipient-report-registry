<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

use Matodor\Common\components\Migration;

/**
 * Class m210714_105957_create_module_permissions
 */
class m210714_105957_create_module_permissions extends Migration
{
    const TYPE_ROLE = 1;
    const TYPE_PERMISSION = 2;

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%auth_item}}', [
            'name' => 'can_view_constructor_tables_list',
            'description' => 'Доступ к просмотру списка таблиц в конструкторе',
            'created_at' => time(),
            'updated_at' => time(),
            'type' => static::TYPE_PERMISSION,
        ]);

        $this->insert('{{%auth_item}}', [
            'name' => 'can_edit_constructor_table',
            'description' => 'Доступ к редактирование таблицы в конструкторе',
            'created_at' => time(),
            'updated_at' => time(),
            'type' => static::TYPE_PERMISSION,
        ]);

        $this->insert('{{%auth_item}}', [
            'name' => 'can_delete_constructor_table',
            'description' => 'Доступ к удалению таблицы в конструкторе',
            'created_at' => time(),
            'updated_at' => time(),
            'type' => static::TYPE_PERMISSION,
        ]);

        $this->insert('{{%auth_item}}', [
            'name' => 'can_create_constructor_table',
            'description' => 'Доступ к созданию таблицы в конструкторе',
            'created_at' => time(),
            'updated_at' => time(),
            'type' => static::TYPE_PERMISSION,
        ]);

        $this->insert('{{%auth_item}}', [
            'name' => 'has_backend_access',
            'description' => 'Доступ к админ-панели',
            'created_at' => time(),
            'updated_at' => time(),
            'type' => static::TYPE_PERMISSION,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%auth_item}}', ['name' => 'can_delete_constructor_table']);
        $this->delete('{{%auth_item}}', ['name' => 'can_edit_constructor_table']);
        $this->delete('{{%auth_item}}', ['name' => 'can_view_constructor_tables_list']);
    }
}
