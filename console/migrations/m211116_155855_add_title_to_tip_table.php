<?php

use yii\db\Migration;

/**
 * Class m211116_155855_add_title_to_tip_table
 */
class m211116_155855_add_title_to_tip_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand()->batchInsert('information_tip', ['title'], [
            ['годовой'],
            [ 'полугодовой'],
        ])->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand()-> delete('information_tip', ['in','title',
            ['годовой','полугодовой']])->execute();
    }
}
