<?php

namespace Matodor\RegistryConstructor\models;

use Matodor\Common\models\BaseModel;
use Matodor\RegistryConstructor\components\ValueType;
use Matodor\RegistryConstructor\components\ValueTypeDefinition;
use Matodor\RegistryConstructor\models\forms\TableFieldForm;
use Matodor\RegistryConstructor\models\forms\TableFieldSettingsForm;
use Matodor\RegistryConstructor\models\forms\TableForm;
use Matodor\RegistryConstructor\queries\TableFieldQuery;
use Matodor\RegistryConstructor\queries\TableQuery;
use Matodor\RegistryConstructor\traits\HasModulePropertiesTrait;
use Matodor\RegistryConstructor\traits\TableRecordValueFormTrait;
use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

/**
 * @property integer $id
 * @property integer $registry_table_id
 * @property integer $registry_variants_table_id
 * @property integer $sort
 * @property string $name
 * @property string $placeholder
 * @property boolean $required
 * @property integer $type
 * @property string $value_type
 * @property integer $flags
 *
 * @property-read TableFieldSettings|TableFieldSettingsForm|null $settings
 * @property-read bool $hasVariantsTable
 * @property-read bool $hasSettings
 * @property-read string $nameTemplate
 *
 * @property-read ValueTypeDefinition $valueTypeDefinition
 * @property-read string[] $typeHeaders
 * @property-read Table $table
 * @property-read Table $variantsTable
 */
class TableField extends BaseModel
{
    use HasModulePropertiesTrait;

    /**
     * Тип - ввод значения
     */
    public const FIELD_TYPE_INPUT = 0;

    /**
     * Тип - выбор значения
     */
    public const FIELD_TYPE_SELECT = 1;

    /**
     * Тип - выбор значения
     */
    public const FIELD_TYPE_CHILD_RECORD = 2;

    public const FLAGS_EXPORT_FIELD = 1 << 1;
    public const FLAGS_IS_VISIBLE_IN_SEARCH_FORM = 1 << 2;
    public const FLAGS_IS_VISIBLE = 1 << 3;
    public const FLAGS_IMPORT_FIELD = 1 << 4;

    /**
     * @var TableFieldSettings|TableFieldSettingsForm|null
     */
    private $_settings = null;

    public function __construct($config = [])
    {
        $this->flags = 0;
        $this->required = true;
        $this->placeholder = 'Не заполнено';

        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%registry_table_fields}}';
    }

    /**
     * @return TableFieldQuery
     * @throws InvalidConfigException
     */
    public static function find()
    {
        return Yii::createObject(TableFieldQuery::class, [get_called_class()]);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'sort',
                    'flags',
                ],
                'default',
                'value' => 0,
            ],

            [
                [
                    'registry_table_id',
                    'name',
                    'type',
                ],
                'required',
                'except' => [static::SCENARIO_SEARCH],
            ],

            [
                [
                    'registry_table_id',
                    'registry_variants_table_id',
                    'sort',
                    'type',
                    'flags',
                ],
                'integer',
            ],

            ['type', 'in', 'range' => array_keys($this->getTypeHeaders())],
            ['value_type', 'string', 'max' => 32],
            ['value_type', 'in', 'range' => ValueType::instance()->getTypes()],
            ['value_type', 'required', 'enableClientValidation' => false, 'when' => function () {
                return $this->type === static::FIELD_TYPE_INPUT;
            }],

            [['name', 'placeholder'], 'string', 'max' => 255],
            ['placeholder', 'default', 'value' => 'Не заполнено'],
            ['required', 'default', 'value' => true],
            ['required', 'boolean'],
            ['registry_table_id', 'exist', 'targetClass' => Table::class, 'targetAttribute' => 'id'],
            ['registry_variants_table_id', 'exist', 'targetClass' => Table::class, 'targetAttribute' => 'id', 'when' => function () {
                return $this->getHasVariantsTable();
            }],
            ['registry_variants_table_id', 'required', 'enableClientValidation' => false, 'when' => function () {
                return $this->getIsNewRecord() === false
                    && (
                        $this->type === static::FIELD_TYPE_SELECT
                        || $this->type === static::FIELD_TYPE_CHILD_RECORD
                    );
            }],
        ];
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($this->registry_variants_table_id === 0) {
            $this->registry_variants_table_id = null;
        }

        if ($insert) {
            if ($this->type === static::FIELD_TYPE_SELECT) {
                $this->value_type = ValueType::TYPE_SELECT;
            } else if ($this->type === static::FIELD_TYPE_CHILD_RECORD) {
                $this->value_type = ValueType::TYPE_CHILD_RECORD;
            }
        }

        if ($this->getHasSettings()) {
            $this->setAttribute('settings', $this->settings->toJson());
        } else {
            $this->setAttribute('settings', json_encode([], JSON_FORCE_OBJECT));
        }

        return true;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($this->type === static::FIELD_TYPE_SELECT
            || $this->type === static::FIELD_TYPE_CHILD_RECORD
        ) {
            if ($insert
                && $this->getHasVariantsTable() === false
            ) {
                $tableForm = new TableForm();
                $tableForm->name = $this->name;
                $tableForm->type = Table::TABLE_TYPE_AUXILIARY;
                $tableForm->visible_in_menu = false;
                $tableForm->editFields = [
                    new TableFieldForm([
                        'sort' => 0,
                        'name' => 'Значение',
                        'required' => true,
                        'type' => static::FIELD_TYPE_INPUT,
                        'value_type' => ValueType::TYPE_TEXT,
                    ]),
                ];

                if ($tableForm->save()) {
                    $this->updateAttributes([
                        'registry_variants_table_id' => $tableForm->id,
                    ]);
                }
            }
        }
    }

    public function afterValidate()
    {
        parent::afterValidate();

        if (!$this->isNewRecord) {
            foreach ([
                'type',
                'value_type',
                'registry_table_id',
                'registry_variants_table_id',
            ] as $attribute) {
                if ($this->isAttributeChanged($attribute, false)) {
                    $this->addError($attribute, 'После создания таблицы изменение поля запрещено');
                }
            }
        }

        if ($this->registry_variants_table_id !== null
            && $this->registry_variants_table_id === $this->registry_table_id
        ) {
            // TODO implement check for childs recursive

            $this->addError('registry_table_id', 'Рекурсивное добавление справочников запрещено!');
            $this->addError('registry_variants_table_id', 'Рекурсивное добавление справочников запрещено!');
        }

        if ($this->type === static::FIELD_TYPE_SELECT
            && $this->value_type !== ValueType::TYPE_SELECT
            || $this->type === static::FIELD_TYPE_CHILD_RECORD
            && $this->value_type !== ValueType::TYPE_CHILD_RECORD
        ) {
            $this->addError('type', 'Ошибка');
            $this->addError('value_type', 'Ошибка');
        }

        if ($this->getHasSettings()
            && $this->settings->validate() === false
        ) {
            $this->addError('settings', 'Исправьте ошибки при заполнении настроек поля');
        }
    }

    /**
     * @throws InvalidConfigException
     */
    public function afterFind()
    {
        parent::afterFind();

        $this->recreateSettings();
    }

    /**
     * @return string[]
     */
    public function getTypeHeaders()
    {
        return [
            static::FIELD_TYPE_INPUT => 'Ввод значения',
            static::FIELD_TYPE_SELECT => 'Выбор значения из справочника',
            static::FIELD_TYPE_CHILD_RECORD => 'Заполнение строки из справочника',
        ];
    }

    /**
     * {@inheritdoc}
     * @noinspection DuplicatedCode
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Идентификатор',
            'registry_table_id' => 'ID реестра',
            'registry_variants_table_id' => 'Справочник значений',
            'sort' => 'Сортировка',
            'name' => 'Имя поля',
            'required' => 'Поле обязательно для заполнения?',
            'placeholder' => 'Placeholder поля ввода значения',
            'type' => 'Тип',
            'value_type' => 'Тип значения',
            'flags' => 'Опции отображения',
        ];
    }

    /**
     * @return TableQuery
     * @noinspection PhpUnused
     * @noinspection PhpIncompatibleReturnTypeInspection
     * @noinspection PhpReturnDocTypeMismatchInspection
     */
    public function getTable()
    {
        return $this->hasOne(
            $this instanceof TableFieldForm
                ? TableForm::class
                : Table::class,
            ['id' => 'registry_table_id']
        );
    }

    /**
     * @return TableQuery
     * @noinspection PhpUnused
     * @noinspection PhpIncompatibleReturnTypeInspection
     * @noinspection PhpReturnDocTypeMismatchInspection
     */
    public function getVariantsTable()
    {
        return $this->hasOne(
            $this instanceof TableFieldForm
                ? TableForm::class
                : Table::class,
            ['id' => 'registry_variants_table_id']
        );
    }

    /**
     * @param bool $asForm
     * @param bool $setDefaultValues
     *
     * @return TableRecordValue|TableRecordValueFormTrait
     * @throws InvalidConfigException
     * @noinspection PhpIncompatibleReturnTypeInspection
     * @noinspection PhpReturnDocTypeMismatchInspection
     */
    public function instantiateRecordValue(bool $asForm = false, bool $setDefaultValues = true)
    {
        /** @var TableRecordValue|TableRecordValueFormTrait $recordValue */
        $recordValue = Yii::createObject([
            'class' => ValueType::instance()->getRecordValueClass($this->type, $this->value_type, $asForm),
            'registry_table_field_id' => $this->id,
            'field' => $this,
        ]);

        if ($setDefaultValues
            && $asForm
            && $this->getIsNewRecord() === false
            && $this->table->defaultValuesRecord !== null
        ) {
            $defaultValue = $this->table->defaultValuesRecord->getValue($this->id);

            if ($defaultValue !== null) {
                $attrs = $defaultValue->getAttributes($defaultValue::additionalColumns());
                $recordValue->setAttributes($attrs, false);
            }
        }

        return $recordValue;
    }

    /**
     * @return ValueTypeDefinition
     */
    public function getValueTypeDefinition()
    {
        return ValueType::instance()->getDefinitionForField(
            $this->type,
            $this->value_type
        );
    }

    /**
     * @param $flag
     *
     * @return bool
     */
    public function hasFlag($flag)
    {
        return (($this->flags & $flag) !== 0);
    }

    public function load($data, $formName = null)
    {
        if (!parent::load($data, $formName)) {
            return false;
        }

        if ($this->getHasSettings()) {
            $data = ArrayHelper::getValue($data, 'settings');

            if (!$this->settings->load($data, '')) {
                return false;
            }
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function __get($name)
    {
        if ($name === 'settings') {
            return $this->_settings;
        }

        return parent::__get($name);
    }

    /**
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function __set($name, $value)
    {
        if ($name === 'settings') {
            throw new Exception('Settings property is read-only');
        }

        if ($name === 'type') {
            if ((int) $value === static::FIELD_TYPE_SELECT) {
                $this->setAttribute('value_type', ValueType::TYPE_SELECT);
            } else if ((int) $value === static::FIELD_TYPE_CHILD_RECORD) {
                $this->setAttribute('value_type', ValueType::TYPE_CHILD_RECORD);
            }
        }

        if (($name === 'type' || $name === 'value_type')
            && $this->getAttribute($name) != $value
        ) {
            parent::__set($name, $value);
            $this->recreateSettings();
        } else {
            parent::__set($name, $value);
        }
    }

    /**
     * @return bool
     * @noinspection PhpUnused
     */
    public function getHasVariantsTable()
    {
        return $this->registry_variants_table_id !== null
            && $this->registry_variants_table_id !== 0;
    }

    /**
     * @return bool
     */
    public function getHasSettings()
    {
        return $this->_settings !== null;
    }

    /**
     * @throws InvalidConfigException
     */
    protected function recreateSettings()
    {
        if ($this->getAttribute('type') !== null
            && $this->getAttribute('value_type') !== null
        ) {
            $definition = $this->getValueTypeDefinition();

            if ($definition->getHasSettings()) {
                $this->_settings = Yii::createObject([
                    'class' => $definition->settingsClass,
                    'field' => $this,
                ]);

                $this
                    ->settings
                    ->fill($this->getAttribute('settings'));
            } else {
                $this->_settings = null;
            }
        } else {
            $this->_settings = null;
        }
    }

    /**
     * @return string
     */
    public function getNameTemplate()
    {
        return "\${{$this->name}}";
    }
}
