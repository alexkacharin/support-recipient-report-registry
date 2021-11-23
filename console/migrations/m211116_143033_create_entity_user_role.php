<?php

use yii\db\Migration;

/**
 * Class m211116_143033_create_entity_user_role
 */
class m211116_143033_create_entity_user_role extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%auth_item}}', [
            'name' => 'entity',
            'type' => 1,
            'description' => 'Проситель',
            'created_at' => time(),
            'updated_at' => time(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand()-> delete('auth_item', ['in','name',
            ['entity']])->execute();
    }
}
