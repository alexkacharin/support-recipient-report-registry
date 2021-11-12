<?php

use console\components\Migration;

/*
 * Class m210620_101752_create_admin
 */
class m210620_101752_create_admin extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%auth_item}}', [
            'name' => 'superadmin',
            'type' => 1,
            'description' => 'Технический администратор',
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $admin = new \dektrium\user\models\User();
        $admin->username = 'superadmin';
        $admin->email = 'admin@scrap-reports.test';
        $admin->password = 'admintws';

        if (!$admin->create()) {
            throw new \RuntimeException('Can\'t create admin user');
        }

        $this->insert('{{%auth_assignment}}', [
            'item_name' => 'superadmin',
            'user_id' => $admin->id,
            'created_at' => time(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210620_101752_create_admin cannot be reverted.\n";
        return false;
    }
}
