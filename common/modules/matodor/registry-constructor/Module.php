<?php

namespace Matodor\RegistryConstructor;

use Matodor\RegistryConstructor\widgets\RecordsSearch\Widget as RecordsSearch;
use Matodor\RegistryConstructor\widgets\RecordsViewer\Widget as RecordsViewer;
use Matodor\RegistryConstructor\widgets\RecordsViewerToolbar\Widget as RecordsViewerToolbar;
use Yii;
use yii\base\Exception;
use yii\base\Module as BaseModule;
use yii\caching\Cache;
use yii\caching\DummyCache;
use yii\di\Instance;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\helpers\Url;

/**
 * @property-read string $routePrefix
 * @property-read string $moduleName
 */
class Module extends BaseModule
{
    public const MODULE_NAME = 'registry-constructor';

    public const CAN_VIEW_CONSTRUCTOR_TABLES_LIST = 'can_view_constructor_tables_list';
    public const CAN_EDIT_CONSTRUCTOR_TABLE = 'can_edit_constructor_table';
    public const CAN_DELETE_CONSTRUCTOR_TABLE = 'can_delete_constructor_table';
    public const CAN_CREATE_CONSTRUCTOR_TABLE = 'can_create_constructor_table';

    /**
     * @var Cache|null
     */
    public $cache = null;

    /**
     * @var string
     *
     * @see GroupUrlRule::$prefix
     */
    public $urlPrefix = null;

    /**
     * @var array|null
     *
     * @see GroupUrlRule::$ruleConfig
     */
    public $ruleConfig = null;

    /**
     * @var string
     */
    public $storagePath = null;

    /**
     * @var string|null
     */
    public $migrationsPath = null;

    /**
     * @var array The rules to be used in URL management.
     */
    public $urlRules = [
        '<controller:[\w-]+>' => '<controller>/index',
        '<controller:[\w-]+>/<id:\d+>' => '<controller>/view',
        '<controller:[\w-]+>/<action:[\w-]+>/<id:\d+>' => '<controller>/<action>',
        '<controller:[\w-]+>/<action:[\w-]+>' => '<controller>/<action>',
    ];

    public function init()
    {
        parent::init();

        if ($this->cache !== null) {
            $this->cache = Instance::ensure($this->cache, Cache::class);
        } else {
            $this->cache = Instance::ensure(['class' => DummyCache::class], Cache::class);
        }
    }

    /**
     * @return string
     * @noinspection PhpUnused
     */
    public function getModuleName()
    {
        return static::MODULE_NAME;
    }

    /**
     * @return string
     * @noinspection PhpUnused
     */
    public function getRoutePrefix()
    {
        return '/' . static::MODULE_NAME . '/';
    }

    /**
     * @return Module|BaseModule|null
     * @noinspection PhpReturnDocTypeMismatchInspection
     */
    public static function getInstance()
    {
        return Yii::$app->getModule(static::MODULE_NAME);
    }

    /**
     * @param BaseModule|null $instance
     */
    public static function setInstance($instance)
    {
        Yii::$app->setModule(static::MODULE_NAME, $instance);
    }

    /**
     * @param string|array $route
     * @param bool|string $scheme
     *
     * @see Url::toRoute()
     */
    public function toRoute($route, $scheme = false)
    {
        $prefix = $this->routePrefix;

        if (is_string($route)) {
            if (!StringHelper::startsWith($route, $prefix)) {
                $route = $prefix . $route;
            }
        } else if (is_array($route)) {
            if (!StringHelper::startsWith($route[0], $prefix)) {
                $route[0] = $prefix . $route[0];
            }
        }

        return Url::toRoute($route, $scheme);
    }
}
