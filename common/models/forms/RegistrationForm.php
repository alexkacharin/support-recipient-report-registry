<?php

namespace common\models\forms;

use common\models\User;

class RegistrationForm extends \dektrium\user\models\RegistrationForm
{
    /**
     * @var string
     */
    public $captcha;
    public $inn;
    public $name;
    public $location;
    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [['captcha', 'inn'], 'required'];
        $rules[] = [['name', 'location'], 'required'];
        $rules[] = ['captcha', 'captcha', 'captchaAction' => 'site/captcha', 'caseSensitive' => false];

        return $rules;
    }

    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['captcha'] = 'Проверочный код';
        $labels['inn'] = 'ИНН';

        return $labels;
    }

}
