<?php

/** @noinspection PhpHierarchyChecksInspection */

namespace Matodor\RegistryConstructor\models\data\Date;

use Carbon\Carbon;
use Matodor\RegistryConstructor\models\data\HasTemplatesSettings;
use Matodor\RegistryConstructor\models\forms\TableFieldSettingsForm;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * @property-read string[] $formats
 * @property-read mixed $formatsSelectData
 */
class Settings extends TableFieldSettingsForm
{
    use HasTemplatesSettings;

    /**
     * @var string|null
     */
    public $format = null;

    public function rules()
    {
        $rules = parent::rules();
        $rules['formatDefault'] = ['format', 'default', 'value' => null];
        $rules['formatString'] = ['format', 'string'];
        $rules['formatIn'] = ['format', 'in', 'range' => $this->getFormats()];

        $this->appendRules($rules);

        return $rules;
    }

    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['format'] = 'Формат даты';

        $this->appendLabels($labels);

        return $labels;
    }

    public function getFormatsSelectData()
    {
        return ArrayHelper::map($this->getFormats(), function ($format) {
            return $format;
        }, function ($format) {
            return Carbon::now()->locale(Yii::$app->formatter->locale)->translatedFormat($format);
        });
    }

    public function getFormats()
    {
        return [
            'Y-m-d',
            'd/m/Y',
            'd.m.Y',
            'D, M, j',
            'F j, Y',
            'l, j F Y',
        ];
    }
}
