<?php

/** @noinspection MissedViewInspection */

namespace Matodor\RegistryConstructor\controllers;

use Matodor\Common\components\Helper;
use Matodor\RegistryConstructor\models\Table;
use Matodor\RegistryConstructor\Module;
use yii\base\InvalidConfigException;
use yii\console\Exception;
use yii\console\ExitCode;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Console;
use yii\helpers\FileHelper;
use yii\helpers\Inflector;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

class TableMigrationsController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => [
                            'generate-table-structure',
                            'generate-table-data',
                        ],
                        'allow' => true,
                        'permissions' => ['superadmin'],
                    ],
                    [
                        'allow' => false,
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                ],
            ],
        ];
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundHttpException|ServerErrorHttpException
     */
    public function actionGenerateTableStructure($id)
    {
        $model = Table::findModel($id);

        if (Helper::isEmpty(Module::getInstance()->migrationsPath)) {
            throw new ServerErrorHttpException('`migrationsPath` property not setup');
        }

        return $this->render('confirm-delete', [
            'table' => $model,
        ]);
    }

    protected function getTableBaseInfo(Table $table)
    {
        // "name":"\u0420\u0435\u0435\u0441\u0442\u0440 \u0442\u0435\u043d\u0434\u0435\u0440\u043e\u0432",
        // "type":"1",
        // "default_page_size":"10",
        // "visible_in_menu":"1",
        // "widget_class_viewer":"Matodor\\RegistryConstructor\\widgets\\RecordsViewer\\Widget",
        // "widget_class_toolbar":"Matodor\\RegistryConstructor\\widgets\\RecordsViewerToolbar\\Widget",
        // "widget_class_search":"Matodor\\RegistryConstructor\\widgets\\RecordsSearch\\Widget",
        // "table_behaviors":"",
        // "records_behaviors":{
        //   "1":"\\common\\behaviors\\TenderRecordBehavior"
        // },
        // "records_assets":{
        //   "1":"\\common\\assets\\TenderCartFormAssets"
        // }
    }

    /**
     *
     */
    public function actionGenerateTableData($id)
    {
        //
    }

    public function actionCreate($name)
    {
        // if (!preg_match('/^[\w\\\\]+$/', $name)) {
        //     throw new Exception('The migration name should contain letters, digits, underscore and/or backslash characters only.');
        // }
        //
        // [$namespace, $className] = $this->generateClassName($name);
        // // Abort if name is too long
        // $nameLimit = $this->getMigrationNameLimit();
        // if ($nameLimit !== null && strlen($className) > $nameLimit) {
        //     throw new Exception('The migration name is too long.');
        // }
        //
        // $migrationPath = $this->findMigrationPath($namespace);
        //
        // $file = $migrationPath . DIRECTORY_SEPARATOR . $className . '.php';
        // if ($this->confirm("Create new migration '$file'?")) {
        //     $content = $this->generateMigrationSourceCode([
        //         'name' => $name,
        //         'className' => $className,
        //         'namespace' => $namespace,
        //     ]);
        //     FileHelper::createDirectory($migrationPath);
        //     if (file_put_contents($file, $content, LOCK_EX) === false) {
        //         $this->stdout("Failed to create new migration.\n", Console::FG_RED);
        //
        //         return ExitCode::IOERR;
        //     }
        //
        //     $this->stdout("New migration created successfully.\n", Console::FG_GREEN);
        // }
        //
        // return ExitCode::OK;
    }

    protected function generateClassName($name)
    {
        return 'm' . gmdate('ymd_His') . '_' . $name;
    }
}
