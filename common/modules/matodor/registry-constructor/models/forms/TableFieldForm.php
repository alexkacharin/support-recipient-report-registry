<?php

namespace Matodor\RegistryConstructor\models\forms;

use Closure;
use Matodor\RegistryConstructor\components\ValueType;
use Matodor\RegistryConstructor\models\Table;
use Matodor\RegistryConstructor\models\TableField;
use yii\helpers\ArrayHelper;

/**
 * @property-read array|string[] $variantsTableSelectData
 * @property-read bool $isVisibleVariantsTableButtons
 */
class TableFieldForm extends TableField
{
    /**
     * @var string
     */
    public $uid;

    /**
     * @var bool
     */
    public $exportField = true;

    /**
     * @var bool
     */
    public $importField = true;

    /**
     * @var bool
     */
    public $isVisibleInSearchForm = true;

    /**
     * @var bool
     */
    public $isVisible = true;

    public function __construct($config = [])
    {
        $this->registry_variants_table_id = null;
        $this->uid = $config['uid'] ?? uniqid('f');
        parent::__construct($config);
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[static::SCENARIO_DEFAULT][] = '!registry_table_id';

        return $scenarios;
    }

    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [
            [
                'exportField',
                'importField',
                'isVisibleInSearchForm',
                'isVisible',
            ],
            'boolean',
        ];

        return $rules;
    }

    /**
     * @param Closure $callback
     */
    public static function handleFlags(Closure $callback)
    {
        $matching = [
            ['exportField', static::FLAGS_EXPORT_FIELD],
            ['importField', static::FLAGS_IMPORT_FIELD],
            ['isVisibleInSearchForm', static::FLAGS_IS_VISIBLE_IN_SEARCH_FORM],
            ['isVisible', static::FLAGS_IS_VISIBLE],
        ];

        foreach ($matching as $item) {
            $callback($item[0], $item[1]);
        }
    }

    public function afterFind()
    {
        parent::afterFind();

        static::handleFlags(function ($attribute, $flag) {
            $this->$attribute = $this->hasFlag($flag);
        });
    }

    public function formName()
    {
        return TableForm::instance()->formName() . "[editFields][{$this->uid}]";
    }

    public function getAvailableFlags()
    {
        $flags = [];

        if ($this->value_type !== ValueType::TYPE_FILE) {
            $flags[] = 'exportField';
        }

        if ($this->value_type !== ValueType::TYPE_FILE) {
            $flags[] = 'importField';
        }

        if ($this->value_type !== ValueType::TYPE_FILE) {
            $flags[] = 'isVisibleInSearchForm';
        }

        $flags[] = 'isVisible';

        return $flags;
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($this->value_type === ValueType::TYPE_FILE) {
            $this->exportField = false;
            $this->importField = false;
            $this->isVisibleInSearchForm = false;
        }

        static::handleFlags(function ($attribute, $flag) {
            if ($this->$attribute) {
                $this->flags |= $flag;
            } else {
                $this->flags &= ~$flag;
            }
        });

        return true;
    }

    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['exportField'] = 'Экспортировать поле?';
        $labels['importField'] = 'Импортировать поле?';
        $labels['isVisibleInSearchForm'] = 'Отображать поле в форме поиска?';
        $labels['isVisible'] = 'Отображать поле в карточке записи / таблице?';

        return $labels;
    }

    /**
     * @return array
     * @noinspection PhpUnused
     */
    public function getVariantsTableSelectData()
    {
        $data = Table::find()
            ->select(['id', 'name'])
            ->andFilterWhere(['<>', 'id', $this->registry_table_id])
            ->asArray()
            ->all();

        return [0 => 'Создать новый справочник'] + ArrayHelper::map($data ,'id', function ($item) {
            return "Справочник - {$item['name']}";
        });
    }

    /**
     * @return bool
     * @noinspection PhpUnused
     */
    public function getIsVisibleVariantsTableButtons()
    {
        return (
                $this->type === TableField::FIELD_TYPE_SELECT
                || $this->type === TableField::FIELD_TYPE_CHILD_RECORD
            )
            && $this->getHasVariantsTable();
    }
}
