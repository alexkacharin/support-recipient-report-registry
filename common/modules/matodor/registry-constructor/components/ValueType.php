<?php

namespace Matodor\RegistryConstructor\components;

use Matodor\RegistryConstructor\models\TableField;
use Matodor\RegistryConstructor\models\TableRecordValue;
use ReflectionClass;
use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\base\StaticInstanceTrait;
use yii\base\UnknownClassException;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

/**
 * @property-read string[] $types
 * @property-read null|ValueTypeDefinition[] $definitions
 * @property-read string[]|TableRecordValue[] $recordValueClasses
 * @property-read array $valueTypesSelectData
 */
class ValueType extends Component
{
    use StaticInstanceTrait;

    /**
     * Тип значения - Строка (до 255 символов)
     */
    public const TYPE_STRING = 'String';

    /**
     * Тип значения - Текст
     */
    public const TYPE_TEXT = 'Text';

    /**
     * Тип значения - Целочисленное число
     */
    public const TYPE_INT = 'Int';

    /**
     * Тип значения - Денежный формат
     */
    public const TYPE_DECIMAL = 'Decimal';

    /**
     * Тип значения - Дробное число
     */
    public const TYPE_FLOAT = 'Float';

    /**
     * Тип значения - Логическое
     */
    public const TYPE_BOOLEAN = 'Boolean';

    /**
     * Тип значения - Дата
     */
    public const TYPE_DATE = 'Date';

    /**
     * Тип значения - Дата время
     */
    public const TYPE_DATETIME = 'DateTime';

    /**
     * Тип значения - Файл
     */
    public const TYPE_FILE = 'File';

    /**
     * Тип значения - Select
     */
    public const TYPE_SELECT = 'Select';

    /**
     * Тип значения - Select
     */
    public const TYPE_CHILD_RECORD = 'ChildRecord';

    /**
     * Тип значения - E-mail
     */
    public const TYPE_EMAIL = 'Email';

    /**
     * @var ValueTypeDefinition[]
     */
    private $_definitions = null;

    /**
     * @var string[]
     */
    private $_types = [];

    public function getTypes()
    {
        if (empty($this->_types)) {
            $this->getDefinitions();
        }

        return $this->_types;
    }

    /**
     * @return ValueTypeDefinition[]
     */
    public function getDefinitions()
    {
        if ($this->_definitions !== null) {
            return $this->_definitions;
        }

        $reflectionClass = new ReflectionClass(__CLASS__);
        $this->_types = array_values($reflectionClass->getConstants());
        $this->_definitions = ArrayHelper::map($this->_types, function ($type) {
            return $type;
        }, function ($type) {
            $namespace = $this->getNamespaceByType($type);
            $valueClass = "$namespace\\Value";
            $valueFormClass = "$namespace\\ValueForm";
            $settingsClass = "$namespace\\Settings";

            if (!class_exists($valueClass)) {
                throw new UnknownClassException("RecordValue class not found for `{$type}` value type");
            }

            if (!class_exists($valueFormClass)) {
                throw new UnknownClassException("RecordValueForm class not found for `{$type}` value type");
            }

            if (!class_exists($settingsClass)) {
                $settingsClass = null;
            }

            return Yii::createObject([
                'class' => ValueTypeDefinition::class,
                'type' => $type,
                'valueClass' => $valueClass,
                'valueFormClass' => $valueFormClass,
                'valueTypeUnderscored' => Inflector::underscore($type),
                'settingsClass' => $settingsClass,
            ]);
        });

        return $this->_definitions;
    }

    /**
     * @return ValueTypeDefinition
     */
    public function getDefinition($type)
    {
        return $this->_definitions[$type];
    }

    /**
     * @return ValueTypeDefinition
     */
    public function getDefinitionForField(int $fieldType, string $type)
    {
        switch ($fieldType) {
            case TableField::FIELD_TYPE_SELECT:
                return $this->getDefinition(static::TYPE_SELECT);

            case TableField::FIELD_TYPE_CHILD_RECORD:
                return $this->getDefinition(static::TYPE_CHILD_RECORD);

            default:
                return $this->getDefinition($type);
        }
    }

    /**
     * @param string $type
     *
     * @return string
     */
    public function getNamespaceByType(string $type)
    {
        return "Matodor\\RegistryConstructor\\models\\data\\$type";
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getValueTypesSelectData()
    {
        $data = [];

        foreach ($this->getDefinitions() as $type => $definition) {
            if ($type === static::TYPE_SELECT) {
                continue;
            }

            $data[$type] = $definition->valueClass::getSelectTitle();
        }

        return $data;
    }

    /**
     * @return string[]|TableRecordValue[]
     */
    public function getRecordValueClasses()
    {
        return ArrayHelper::getColumn(ValueType::instance()->getDefinitions(), function ($definition) {
            /** @var ValueTypeDefinition $definition */
            return $definition->valueClass;
        });
    }

    /**
     * @param int $fieldType
     * @param string $type
     * @param bool $asForm
     *
     * @return string
     */
    public function getRecordValueClass(int $fieldType, string $type, bool $asForm)
    {
        return $asForm
            ? $this->getDefinitionForField($fieldType, $type)->valueFormClass
            : $this->getDefinitionForField($fieldType, $type)->valueClass;
    }
}
