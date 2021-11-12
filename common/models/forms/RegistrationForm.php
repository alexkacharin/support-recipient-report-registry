<?php

namespace common\models\forms;

class RegistrationForm extends \dektrium\user\models\RegistrationForm
{
    /**
     * @var string
     */
    public $captcha;

    public function rules()
    {
        $rules = parent::rules();
        $rules[] = ['captcha', 'required'];
        $rules[] = ['captcha', 'captcha', 'captchaAction' => 'site/captcha', 'caseSensitive' => false];

        return $rules;
    }

    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['captcha'] = 'Проверочный код';

        return $labels;
    }
}
