<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%profile}}`.
 */
class m211123_141641_add_inn_column_email_column_address_column_to_profile_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%profile}}', 'inn', $this->integer());
        $this->addColumn('{{%profile}}', 'email', $this->string());
        $this->addColumn('{{%profile}}', 'address', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%profile}}', 'inn');
        $this->dropColumn('{{%profile}}', 'email');
        $this->dropColumn('{{%profile}}', 'address');
    }
}
