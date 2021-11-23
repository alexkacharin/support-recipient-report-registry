<?php

namespace Matodor\RegistryConstructor\filters;

use Yii;
use yii\base\Action;
use yii\base\ActionFilter;
use yii\web\ForbiddenHttpException;

class FrontendAccessFilter extends ActionFilter
{
    /**
     * @var array
     */
    public $controllers = ['table-constructor'];

    /**
     * @param Action $action
     *
     * @return bool
     * @throws ForbiddenHttpException
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        if (in_array($action->controller->id, $this->controllers)) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        return true;
    }
}
