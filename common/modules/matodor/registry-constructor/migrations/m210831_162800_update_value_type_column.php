<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

use Matodor\Common\components\Migration;
use yii\db\Query;

/**
 * Class m210831_162800_update_value_type_column
 */
class m210831_162800_update_value_type_column extends Migration
{
    public const FIELD_TYPE_INPUT = 0;
    public const FIELD_TYPE_SELECT = 1;

    public const VALUE_TYPE_STRING = 0;
    public const VALUE_TYPE_TEXT = 1;
    public const VALUE_TYPE_NUMBER = 2;
    public const VALUE_TYPE_BOOLEAN = 3;
    public const VALUE_TYPE_DATE = 4;
    public const VALUE_TYPE_DATETIME = 5;
    public const VALUE_TYPE_FLOAT = 6;
    public const VALUE_TYPE_FILE = 7;

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            '{{%registry_table_fields}}',
            'value_type_new',
            $this->string(32)->after('value_type')->notNull()
        );

        foreach ([
            static::VALUE_TYPE_BOOLEAN => 'Boolean',
            static::VALUE_TYPE_DATE => 'Date',
            static::VALUE_TYPE_DATETIME => 'DateTime',
            static::VALUE_TYPE_FILE => 'File',
            static::VALUE_TYPE_FLOAT => 'Float',
            static::VALUE_TYPE_NUMBER => 'Int',
            static::VALUE_TYPE_STRING => 'String',
            static::VALUE_TYPE_TEXT => 'Text',
        ] as $oldType => $newType) {
            $this->update('{{%registry_table_fields}}', [
                'value_type_new' => $newType,
            ], [
                'value_type' => $oldType,
            ]);
        }

        $this->update('{{%registry_table_fields}}', [
            'value_type_new' => 'Select',
        ], [
            'type' => static::FIELD_TYPE_SELECT,
        ]);

        $this->dropColumn('{{%registry_table_fields}}', 'value_type');
        $this->renameColumn('{{%registry_table_fields}}', 'value_type_new', 'value_type');

        $this->createIndex(
            'dx-registry_table_fields-value_type',
            '{{%registry_table_fields}}',
            'value_type'
        );

        // add missing index
        $this->createIndex(
            'dx-registry_table_fields-type',
            '{{%registry_table_fields}}',
            'type'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('dx-registry_table_fields-type', '{{%registry_table_fields}}');
        $this->dropIndex('dx-registry_table_fields-value_type', '{{%registry_table_fields}}');
        $this->dropColumn('{{%registry_table_fields}}', 'value_type');

        $this->addColumn(
            '{{%registry_table_fields}}',
            'value_type',
            $this->tinyInteger()->notNull()->unsigned()->defaultValue(0)
        );
    }
}
