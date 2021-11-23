<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

use Matodor\Common\components\Migration;
use yii\db\Query;

/**
 * Class m211014_153900_add_table_widget_classes
 */
class m211014_153900_add_table_widget_classes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%registry_tables}}', 'widget_class_viewer',
            $this->string()->notNull()->defaultValue(''));

        $this->addColumn('{{%registry_tables}}', 'widget_class_toolbar',
            $this->string()->notNull()->defaultValue(''));

        $this->addColumn('{{%registry_tables}}', 'widget_class_search',
            $this->string()->notNull()->defaultValue(''));

        $this->update('{{%registry_tables}}', [
            'widget_class_viewer' => Matodor\RegistryConstructor\widgets\RecordsViewer\Widget::class,
            'widget_class_toolbar' => Matodor\RegistryConstructor\widgets\RecordsViewerToolbar\Widget::class,
            'widget_class_search' => Matodor\RegistryConstructor\widgets\RecordsSearch\Widget::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%registry_tables}}', 'widget_class_search');
        $this->dropColumn('{{%registry_tables}}', 'widget_class_toolbar');
        $this->dropColumn('{{%registry_tables}}', 'widget_class_viewer');
    }
}
