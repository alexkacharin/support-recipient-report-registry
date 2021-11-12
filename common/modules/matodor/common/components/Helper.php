<?php

namespace Matodor\Common\components;

use Yii;
use yii\base\Component;
use yii\base\Event;
use yii\base\InvalidArgumentException;
use yii\console\Application as ConsoleApplication;
use yii\db\ActiveRecord;
use yii\grid\ActionColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\UrlManager;
use yii\web\View;

class Helper extends Component
{
    /**
     * @param mixed $value
     *
     * @return boolean
     */
    public static function isEmpty($value)
    {
        return $value === null
            || $value === ''
            || $value === []
            || is_string($value) && trim($value) === '';
    }

    /**
     * @param string|array $route
     * @param string|null $context
     * @param bool|string $scheme
     *
     * @return string
     * @throws \yii\base\InvalidConfigException
     * @noinspection PhpIncludeInspection
     */
    public static function to($route, string $context = null, $scheme = false)
    {
        if ($context === null
            || $context === static::getContext()
        ) {
            return Url::to($route, $scheme);
        }

        $key = "{$context}UrlManager";

        if (Yii::$app->has($key) === false) {
            $path = Yii::getAlias("@{$context}");
            $config = ArrayHelper::merge(
                require $path . '/../common/config/main.php',
                require $path . '/../common/config/main-local.php',
                require $path . '/config/main.php',
                require $path . '/config/main-local.php'
            );

            $urlManagerConfig = ArrayHelper::getValue(
                $config, 'components.urlManager', []);
            $urlManagerConfig['class'] = ArrayHelper::remove(
                $urlManagerConfig, 'class', UrlManager::class);

            /** @var UrlManager $urlManager */
            $urlManager = Yii::createObject($urlManagerConfig);
            $urlManager->setBaseUrl(
                ArrayHelper::getValue($config, 'components.request.baseUrl', '')
            );

            if (Yii::$app instanceof ConsoleApplication) {
                $urlManager->setHostInfo(Yii::$app->urlManager->hostInfo);
            }

            Yii::$app->set($key, $urlManager);
        }

        $tmpManager = Url::$urlManager;
        Url::$urlManager = Yii::$app->get($key);
        $result = Url::to($route, $scheme);
        Url::$urlManager = $tmpManager;

        return $result;
    }

    /**
     * @return string
     */
    public static function getContext()
    {
        return basename(Yii::getAlias('@app'));
    }

    /**
     * @param array $include Array of bundles keys
     *
     * @return void
     */
    public static function disableBundles($include = [])
    {
        Yii::$app->view->on(View::EVENT_END_BODY, function ($event) use ($include) {
            /** @var Event $event */
            /** @var View $view */
            $view = $event->sender;

            foreach (array_keys($view->assetBundles) as $name) {
                if (!array_key_exists($name, $include)) {
                    $view->assetBundles[$name] = false;
                }
            }

            if (!empty($view->jsFiles[View::POS_END])) {
                foreach ($view->jsFiles[View::POS_END] as $i => $file) {
                    if (strpos($file, 'bootstrap-datepicker') !== false) {
                        unset($view->jsFiles[View::POS_END][$i]);
                    }
                }
            }
        });
    }

    /**
     * @param string|null $controller
     * @param array $config
     *
     * @return array
     * @throws \Exception
     */
    public static function gridViewButtons($controller = null, $config = [])
    {
        if (!is_array($config)) {
            throw new InvalidArgumentException();
        }

        if (static::isEmpty($controller)) {
            $controller = Yii::$app->controller->uniqueId;
        }

        $config['buttons'] = ArrayHelper::merge([
            'update' => function ($url, $model) use ($controller) {
                /** @var ActiveRecord $model */
                $url = Url::toRoute(["/{$controller}/edit", 'id' => $model->getPrimaryKey()]);

                return Html::a('<i class="fa fa-edit"></i>', $url, [
                    'class' => 'btn btn-sm btn-primary m-1',
                    'target' => '_blank',
                ]);
            },
            'delete' => function ($url, $model) use ($controller) {
                /** @var ActiveRecord $model */
                $url = Url::toRoute(["/{$controller}/delete", 'id' => $model->getPrimaryKey()]);

                return Html::a('<i class="fa fa-trash"></i>', $url, [
                    'class' => 'btn btn-sm btn-danger m-1',
                    'data' => [
                        'confirm' => 'Вы уверены, что хотите удалить этот элемент?',
                        'method' => 'post',
                    ],
                ]);
            },
        ], ArrayHelper::getValue($config, 'buttons', []));

        $config['template'] = ArrayHelper::getValue($config, 'template', '{update}{delete}');
        $config['template'] = "<div class=\"m-n1\">{$config['template']}</div>";

        $config['header'] = ArrayHelper::getValue($config, 'header', 'Действия');
        $config['visible'] = ArrayHelper::getValue($config, 'visible', true);
        $config['options'] = ArrayHelper::getValue($config, 'options', ['style' => 'width: 95px;']);
        $config['class'] = ArrayHelper::getValue($config, 'class', ActionColumn::class);

        return $config;
    }

    /**
     * @param $classes
     *
     * @return array
     */
    public static function filterCssClasses($classes)
    {
        return is_array($classes)
            ? array_keys(array_filter($classes))
            : $classes;
    }

    /**
     * @param string $html
     *
     * @return mixed|string
     */
    public static function closeTags(string $html) {
        preg_match_all('#<(?!meta|img|br|hr|input\b)\b([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
        $openedTags = $result[1];
        preg_match_all('#</([a-z]+)>#iU', $html, $result);
        $closedTags = $result[1];
        $lengthOpened = count($openedTags);

        if (count($closedTags) == $lengthOpened) {
            return $html;
        }

        $openedTags = array_reverse($openedTags);
        for ($i = 0; $i < $lengthOpened; $i++) {
            if (!in_array($openedTags[$i], $closedTags)) {
                $html .= "</{$openedTags[$i]}>";
            } else {
                unset($closedTags[array_search($openedTags[$i], $closedTags)]);
            }
        }

        return $html;
    }
}
