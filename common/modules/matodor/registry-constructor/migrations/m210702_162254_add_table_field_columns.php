<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

use Matodor\Common\components\Migration;

/**
 * Class m210702_162254_add_table_field_columns
 */
class m210702_162254_add_table_field_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%registry_table_fields}}',
            'registry_variants_table_id', $this->integer()->null()->after('registry_table_id'));

        $this->addForeignKey(
            'fk-registry_table_fields-v_table_id',
            '{{%registry_table_fields}}',
            'registry_variants_table_id',
            '{{%registry_tables}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("SET foreign_key_checks=0")->execute();
        $this->dropForeignKey('fk-registry_table_fields-v_table_id', '{{%registry_table_fields}}');
        $this->dropColumn('{{%registry_table_fields}}', 'registry_variants_table_id');
        Yii::$app->db->createCommand("SET foreign_key_checks=1")->execute();
    }
}
