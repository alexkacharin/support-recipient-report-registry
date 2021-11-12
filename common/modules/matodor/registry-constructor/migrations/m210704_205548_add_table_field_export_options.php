<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

use Matodor\Common\components\Migration;

/**
 * Class m210704_205548_add_table_field_export_options
 */
class m210704_205548_add_table_field_export_options extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%registry_tables}}', 'default_page_size',
            $this->integer()->unsigned()->notNull()->defaultValue(20));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%registry_tables}}', 'default_page_size');
    }
}
