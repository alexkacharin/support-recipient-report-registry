<?php

namespace Matodor\RegistryConstructor;

use Matodor\Common\components\Helper;
use Matodor\RegistryConstructor\traits\HasModulePropertiesTrait;
use Yii;
use yii\base\Application;
use yii\base\BaseObject;
use yii\base\BootstrapInterface;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\i18n\I18N;
use yii\i18n\PhpMessageSource;
use yii\web\GroupUrlRule;

class Bootstrap extends BaseObject implements BootstrapInterface
{
    use HasModulePropertiesTrait;

    /**
     * @param Application $app
     *
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function bootstrap($app)
    {
        if (Helper::isEmpty($this->module->storagePath)) {
            throw new Exception('Setup `storagePath` variable');
        }

        if ($app instanceof \yii\web\Application) {
            $groupRuleConfig = [
                'class' => GroupUrlRule::class,
                'rules' => $this->module->urlRules,
                'prefix' => $this->module->urlPrefix,
                'routePrefix' => $this->module->routePrefix,
            ];

            if ($this->module->ruleConfig !== null) {
                $groupRuleConfig['ruleConfig'] = $this->module->ruleConfig;
            }

            /** @var GroupUrlRule $groupRule */
            $groupRule = Yii::createObject($groupRuleConfig);
            $app->urlManager->addRules([$groupRule], false);
        }

        /** @var I18N $i18n */
        $i18n = $app->get('i18n');

        if (!isset($i18n->translations['registryConstructor'])
            && !isset($i18n->translations['registryConstructor*'])
        ) {
            $i18n->translations['registryConstructor*'] = [
                'class' => PhpMessageSource::class,
                'basePath' => __DIR__ . '/messages',
                'sourceLanguage' => 'ru-RU',
            ];
        }
    }
}
