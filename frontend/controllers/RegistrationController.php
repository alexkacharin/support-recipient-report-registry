<?php

namespace frontend\controllers;

use common\models\User;
use dektrium\user\events\FormEvent;
use dektrium\user\models\RegistrationForm;

class RegistrationController extends \dektrium\user\controllers\RegistrationController
{
    protected $event = null;

    public function init()
    {
        parent::init();

        $this->on(static::EVENT_BEFORE_REGISTER, function ($event) {
            /** @var FormEvent $event */
            /** @var RegistrationForm $form */
            $form = $event->getForm();

            do {
                $form->username = static::generateUsername(6);
                $usernameExist = User::find()
                    ->select(['user.username'])
                    ->where(['user.username' => $form->username])
                    ->exists();
            } while ($usernameExist);
        });

        $this->on(static::EVENT_AFTER_CONFIRM, function ($event) {
            $this->event = static::EVENT_AFTER_CONFIRM;
        });

        $this->on(static::EVENT_AFTER_REGISTER, function ($event) {
            $this->event = static::EVENT_AFTER_REGISTER;
        });
    }

    /**
     * @noinspection DuplicatedCode
     * @noinspection PhpLoopCanBeReplacedWithStrRepeatInspection
     */
    public static function generateUsername($length)
    {
        $sets = [
            '23456789',
        ];

        $all = '';
        $username = '';

        foreach ($sets as $set) {
            $username .= $set[array_rand(str_split($set))];
            $all .= $set;
        }

        $all = str_split($all);
        for ($i = 0; $i < $length - count($sets); $i++) {
            $username .= $all[array_rand($all)];
        }

        return 'u' . str_shuffle($username);
    }

    public function render($view, $params = [])
    {
        if ($view === '/message') {
            if ($this->event === static::EVENT_AFTER_CONFIRM) {
                $view = '@app/views/site/index';
            } else if ($this->event === static::EVENT_AFTER_REGISTER) {
                $view = '@app/views/site/welcome';
            }
        }

        return parent::render($view, $params);
    }
}
