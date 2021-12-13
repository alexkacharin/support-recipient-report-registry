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
    public $companyName;
    public $location;
    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [['captcha', 'inn'], 'required'];
        $rules[] = [['companyName', 'location'], 'required'];
        $rules[] = ['captcha', 'captcha', 'captchaAction' => 'site/captcha', 'caseSensitive' => false];

        return $rules;
    }

    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['captcha'] = 'Проверочный код';
        $labels['inn'] = 'ИНН';
        $labels['companyName'] = 'Название компании';
        $labels['location'] = 'Местоположение';

        return $labels;
    }

}
