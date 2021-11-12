<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Class m210713_111751_assign_admin_role
 */
class m210713_111751_assign_admin_role extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%auth_item}}', [
            'name' => 'admin',
            'type' => 1,
            'description' => 'Администратор',
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $adminId = (new Query())
            ->from('user')
            ->select('id')
            ->where(['username' => 'superadmin'])
            ->scalar();

        if ($adminId === false) {
            return false;
        }

        $this->insert('{{%auth_assignment}}', [
            'item_name' => 'admin',
            'user_id' => $adminId,
            'created_at' => time(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210713_111751_assign_admin_role cannot be reverted.\n";
        return false;
    }
}
