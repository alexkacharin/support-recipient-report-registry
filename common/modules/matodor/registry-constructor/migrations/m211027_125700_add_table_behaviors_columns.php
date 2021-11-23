<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

use Matodor\Common\components\Migration;
use yii\db\Query;

/**
 * Class m211027_125700_add_table_behaviors_columns
 */
class m211027_125700_add_table_behaviors_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%registry_tables}}', 'table_behaviors',
            $this->text());

        $this->addColumn('{{%registry_tables}}', 'records_behaviors',
            $this->text());

        $this->addColumn('{{%registry_tables}}', 'records_assets',
            $this->text());

        $query = (new Query())
            ->from('{{%registry_tables}}')
            ->orderBy(['id' => SORT_ASC]);

        foreach ($query->each() as $row) {
            $this->update('{{%registry_tables}}', [
                'table_behaviors' => '[]',
                'records_behaviors' => '[]',
                'records_assets' => '[]',
            ], ['id' => $row['id']]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%registry_tables}}', 'records_assets');
        $this->dropColumn('{{%registry_tables}}', 'records_behaviors');
        $this->dropColumn('{{%registry_tables}}', 'table_behaviors');
    }
}
