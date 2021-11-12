<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

use Matodor\Common\components\Migration;

/**
 * Handles the creation of table `{{%html_blocks}}`.
 */
class m210707_151358_create_html_blocks_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->createTable('{{%html_blocks}}', [
            'id' => $this->primaryKey(),
            'key' => $this->string(191)->notNull(),
            'content' => $this->text(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('dx-html_blocks-key',
            '{{%html_blocks}}', 'key', true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTableIfExist('{{%html_blocks}}');
    }
}
