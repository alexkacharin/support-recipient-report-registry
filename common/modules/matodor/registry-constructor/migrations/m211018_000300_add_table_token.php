<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

use Matodor\Common\components\Migration;
use yii\db\Query;

/**
 * Class m211018_000300_add_table_token
 */
class m211018_000300_add_table_token extends Migration
{
    private const TOKEN_LENGTH = 32;

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%registry_tables}}', 'token',
            $this->string(static::TOKEN_LENGTH)->null()->after('name'));

        $query = (new Query())
            ->from('{{%registry_tables}}')
            ->orderBy(['id' => SORT_ASC]);

        foreach ($query->each() as $row) {
            $this->update('{{%registry_tables}}', [
                'token' => Yii::$app->security->generateRandomString(static::TOKEN_LENGTH),
            ], ['id' => $row['id']]);
        }

        $this->alterColumn('{{%registry_tables}}', 'token',
            $this->string(static::TOKEN_LENGTH)->notNull()->unique()->after('name'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%registry_tables}}', 'token');
    }
}
