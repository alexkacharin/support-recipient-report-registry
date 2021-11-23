<?php
namespace backend\controllers;

use Matodor\Common\widgets\HtmlBlock\BaseWidget as HtmlBlockWidget;
use yii\web\Controller;
use yii\filters\AccessControl;

class FrontendSettingsController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin', 'superadmin'],
                    ],
                ],
            ],
        ];
    }

    public function init()
    {
        parent::init();
        $this->viewPath = '@backend/views/settings/frontend';
    }

    public function actionIndex()
    {
        HtmlBlockWidget::saveAll();

        return $this->render('index');
    }
}
