<?php

/** @noinspection MissedViewInspection */

namespace Matodor\RegistryConstructor\controllers;

use Matodor\Common\components\Helper;
use Matodor\RegistryConstructor\models\forms\TableFieldForm;
use Matodor\RegistryConstructor\models\forms\TableForm;
use Matodor\RegistryConstructor\models\forms\TablePermissionsForm;
use Matodor\RegistryConstructor\models\search\TableSearch;
use Matodor\RegistryConstructor\models\Table;
use Matodor\RegistryConstructor\Module;
use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\bootstrap4\ActiveForm;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

class TableConstructorController extends Controller
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
                        'actions' => ['index'],
                        'allow' => true,
                        'permissions' => [Module::CAN_VIEW_CONSTRUCTOR_TABLES_LIST],
                    ],
                    [
                        'actions' => ['edit'],
                        'allow' => true,
                        'permissions' => [Module::CAN_EDIT_CONSTRUCTOR_TABLE],
                    ],
                    [
                        'actions' => ['create'],
                        'allow' => true,
                        'permissions' => [Module::CAN_CREATE_CONSTRUCTOR_TABLE],
                    ],
                    [
                        'actions' => ['delete', 'confirm-delete'],
                        'allow' => true,
                        'permissions' => [Module::CAN_DELETE_CONSTRUCTOR_TABLE],
                    ],
                    [
                        'actions' => [
                            'get-field-form',
                            'get-permission-form',
                        ],
                        'allow' => true,
                        'permissions' => [
                            Module::CAN_EDIT_CONSTRUCTOR_TABLE,
                            Module::CAN_CREATE_CONSTRUCTOR_TABLE,
                        ],
                    ],
                    [
                        'allow' => false,
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'get-field-form' => ['post'],
                    'get-permission-form' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new TableSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionEdit($id)
    {
        $tableForm = TableForm::findModel($id);

        if ($tableForm->load(Yii::$app->request->post())
            && $tableForm->save()
        ) {
            Yii::$app->session->addFlash('success', 'Таблица успешна обновлена');
            return $this->redirect(['index']);
        }

        return $this->render('edit', [
            'tableForm' => $tableForm,
        ]);
    }

    /**
     * For stress-test only
     *
     * @return string|Response
     * @throws BadRequestHttpException
     * @throws Exception
     * @throws InvalidConfigException
     * @noinspection PhpUnreachableStatementInspection
     */
    public function actionCreateMultipleTables()
    {
        // comment line below for allow run code
        throw new Exception();

        if (!Yii::$app->request->isPost) {
            throw new BadRequestHttpException();
        }

        if (isset(Yii::$app->log->targets['file'])) {
            Yii::$app->log->targets['file']->enabled = false;
        }

        $count = 1000;
        $data = Yii::$app->request->post();

        for ($i = 0; $i < $count; $i++) {
            $tableForm = new TableForm();

            if ($tableForm->load($data)) {
                $tableForm->name .= " #{$i}";

                if (!$tableForm->save()) {
                    return $this->render('create', [
                        'tableForm' => $tableForm,
                    ]);
                }
            }
        }

        return $this->redirect(['index']);
    }

    public function actionCreate()
    {
        $tableForm = new TableForm();

        if (Yii::$app->request->isGet) {
            $tableForm->editFields = [
                new TableFieldForm(),
            ];
        }

        if ($tableForm->load(Yii::$app->request->post())
            && $tableForm->save()
        ) {
            Yii::$app->session->addFlash('success', 'Таблица успешна создана');
            return $this->redirect(['edit', 'id' => $tableForm->id]);
        }

        return $this->render('create', [
            'tableForm' => $tableForm,
        ]);
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     * @noinspection PhpUnused
     */
    public function actionConfirmDelete($id)
    {
        $model = Table::findModel($id);

        return $this->render('confirm-delete', [
            'table' => $model,
        ]);
    }

    /**
     * @throws \Throwable
     * @throws InvalidConfigException
     * @throws StaleObjectException
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $model = Table::findModel($id);
        $model->delete();
        Yii::$app->session->addFlash('success', 'Таблица успешна удалена');
        return $this->redirect(['index']);
    }

    /**
     * @return string
     * @noinspection PhpUnused
     */
    public function actionGetPermissionForm()
    {
        Helper::disableBundles();

        $form = new ActiveForm();
        $form->enableClientScript = false;

        $tableForm = new TableForm();
        $permission = new TablePermissionsForm();
        $permission->populateRelationIfNeeded('table', $tableForm);

        return Html::tag('div', $this->renderAjax('form/_table-permission', [
            'permission' => $permission,
            'form' => $form,
        ]), [
            'class' => 'registry-table__form-permission form-list__item',
            'data-uid' => $permission->uid,
        ]);
    }

    /**
     * @param int|string|null $tableId
     *
     * @return string
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     * @noinspection PhpUnused
     */
    public function actionGetFieldForm($tableId = null)
    {
        Helper::disableBundles();

        $tableForm = null;
        $fieldForm = null;

        $form = new ActiveForm();
        $form->enableClientScript = false;

        if ($tableId !== null) {
            $tableForm = TableForm::findModel($tableId);
        }

        if ($tableForm === null) {
            $tableForm = new TableForm();
        }

        $data = Yii::$app->request->post();
        $data = ArrayHelper::getValue($data, $tableForm->formName(), []);
        $data = ArrayHelper::getValue($data, 'editFields', []);
        $data = reset($data);

        if (is_array($data)) {
            $fieldId = ArrayHelper::getValue($data, TableFieldForm::primaryKey());

            if (!$tableForm->isNewRecord
                && !Helper::isEmpty($fieldId)
            ) {
                $fieldForm = TableFieldForm::findModel($fieldId, false);
            }
        } else {
            $data = [];
        }

        if ($fieldForm === null) {
            $fieldForm = new TableFieldForm();
            $fieldForm->registry_table_id = $tableForm->id;
            $fieldForm->registry_variants_table_id = 0;
        }

        $fieldForm->load($data, '');

        return Html::tag('div', $this->renderAjax('form/_table-field', [
            'tableFieldForm' => $fieldForm,
            'tableForm' => $tableForm,
            'form' => $form,
        ]), [
            'class' => 'registry-table__form-field form-list__item',
            'data-uid' => $fieldForm->uid,
        ]);
    }
}
