<?php

namespace Matodor\RegistryConstructor\widgets\RecordsSearch;

use Yii;
use yii\base\Exception;
use Matodor\Common\components\Helper;
use Matodor\RegistryConstructor\models\search\TableRecordSearch;
use Matodor\RegistryConstructor\widgets\RecordsSearch\assets\WidgetAssets;

class Widget extends \yii\base\Widget
{
    /**
     * @var string|array
     */
    public $actionUrl = '';

    /**
     * @var string|array
     */
    public $resetUrl = '';

    /**
     * @var TableRecordSearch
     */
    public $searchModel;

    /**
     * @noinspection MissedViewInspection
     */
    public function run()
    {
        parent::run();

        WidgetAssets::register($this->view);

        if (Helper::isEmpty($this->actionUrl)) {
            throw new Exception('Empty `actionUrl` property');
        }

        if ($this->resetUrl === '') {
            $this->resetUrl = $this->actionUrl;
        }

        return $this->render('form', [
            'widget' => $this,
            'searchModel' => $this->searchModel,
            'actionUrl' => $this->actionUrl,
            'resetUrl' => $this->resetUrl,
        ]);
    }
}
