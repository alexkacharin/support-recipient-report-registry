<?php

namespace console\components;

class Migration extends \yii\db\Migration
{
    public function dropTableIfExist($table)
    {
        if ($this->db->getTableSchema($this->db->tablePrefix . $table, true) !== null) {
            parent::dropTable($table);
        }
    }
}
