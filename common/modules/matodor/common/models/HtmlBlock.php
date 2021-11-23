<?php

namespace Matodor\Common\models;

/**
 * @property integer $id
 * @property string $key
 * @property string $content
 */
class HtmlBlock extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%html_blocks}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['key'], 'required', 'except' => ['search']],
            [['key', 'content'], 'string'],
            [['key'], 'string', 'max' => 255],
            [['key'], 'unique'],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[static::SCENARIO_DEFAULT][] = '!key';

        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Идентификатор',
            'key' => 'Ключ',
            'content' => 'Содержимое',
        ];
    }
}
