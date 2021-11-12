<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

use Matodor\Common\components\Migration;

/**
 * Class m210831_174900_add_table_field_settings_column
 */
class m210831_174900_add_table_field_settings_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            '{{%registry_table_fields}}',
            'settings',
            $this->text()->after('flags')->notNull()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%registry_table_fields}}', 'settings');
    }
}
