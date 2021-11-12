<?php

/** @noinspection MissedViewInspection */

namespace Matodor\RegistryConstructor\controllers;

use Matodor\RegistryConstructor\components\RecordsExporter;
use Matodor\RegistryConstructor\components\RecordsImporter;
use Matodor\RegistryConstructor\models\data\File\Value as FileValue;
use Matodor\RegistryConstructor\models\forms\TableRecordForm;
use Matodor\RegistryConstructor\models\forms\TableRecordsImportForm;
use Matodor\RegistryConstructor\models\search\TableRecordSearch;
use Matodor\RegistryConstructor\models\Table;
use Matodor\RegistryConstructor\models\TablePermissions;
use Matodor\RegistryConstructor\models\TableRecord;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\filters\HttpCache;
use yii\helpers\Html;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

class RecordsController extends Controller
{
    public $_downloadFileInfo = null;

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
                        'allow' => true,
                        'roles' => ['@', '?'],
                    ],
                ],
            ],
            [
                'class' => HttpCache::class,
                'only' => ['download-file'],
                'cacheControlHeader' => 'public, max-age=31536000',
                'etagSeed' => function ($action, $params) {
                    $info = $this->getDownloadFileInfo(Yii::$app->request->get('token'));
                    return $info['etag'];
                },
                'lastModified' => function ($action, $params) {
                    $info = $this->getDownloadFileInfo(Yii::$app->request->get('token'));
                    return $info['lastModified'];
                },
            ],
        ];
    }

    /**
     * @param string $token
     *
     * @return null|array
     * @throws ForbiddenHttpException
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    protected function getDownloadFileInfo(string $token)
    {
        if (!is_string($token)) {
            throw new ForbiddenHttpException();
        }

        if (!isset($this->_downloadFileInfo[$token])) {
            FileValue::$checkFieldRelation = false;
            $model = FileValue::findModel(['file_token' => $token]);
            FileValue::$checkFieldRelation = true;

            if (!$model->getIsValueSet()) {
                throw new ServerErrorHttpException();
            }

            if (!$model->record->canView()) {
                throw new ForbiddenHttpException();
            }

            $path = $model->filePath;
            $this->_downloadFileInfo[$token] = [
                'model' => $model,
                'path' => $path,
                'lastModified' => filemtime($path),
                'etag' => md5_file($path),
            ];
        }

        return $this->_downloadFileInfo[$token];
    }

    /**
     * @param string $token
     * @param bool $inline
     *
     * @throws ForbiddenHttpException
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     * @noinspection PhpUnused
     */
    public function actionDownloadFile(string $token, $inline = false)
    {
        $info = $this->getDownloadFileInfo($token);

        Yii::$app
            ->response
            ->sendFile($info['path'], $info['model']->originalFileName, [
                'inline' => (bool) $inline,
            ]);
    }

    /**
     * @param int $tableId
     * @param string $v
     *
     * @return string
     * @throws ForbiddenHttpException
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     * @noinspection PhpMissingParamTypeInspection
     */
    public function actionIndex($tableId, $v = 'grid')
    {
        $table = Table::findModel($tableId);
        $table->throwIfNoAccess(null, TablePermissions::CAN_VIEW_ALL_RECORDS);
        $table->throwIfNoAccess(null, TablePermissions::CAN_VIEW_SELF_RECORDS);

        /** @noinspection PhpExpressionResultUnusedInspection */
        /** Boostrap table fields once */
        $table->fields;

        /** @var TableRecordSearch $searchModel */
        $searchModel = Yii::createObject([
            'class' => TableRecordSearch::class,
            'table' => $table,
        ]);

        return $this->render('index', [
            'table' => $table,
            'searchModel' => $searchModel,
            'dataProvider' => $searchModel->search(Yii::$app->request->queryParams),
            'viewType' => $v,
        ]);
    }

    public function actionImport($tableId)
    {
        $table = Table::findModel($tableId);
        $table->throwIfNoAccess(null, TablePermissions::CAN_ADD_RECORDS);

        if (Yii::$app->request->isGet) {
            if (Yii::$app->session->has('__importResult')) {
                return $this->render('import-result', [
                    'table' => $table,
                    'importResult' => Yii::$app->session->remove('__importResult'),
                ]);
            }
        } else if (Yii::$app->request->isPost) {
            $importForm = new TableRecordsImportForm();

            if ($importForm->load(Yii::$app->request->post())
                && $importForm->upload()
            ) {
                $importer = new RecordsImporter();
                $importer->table = $table;

                Yii::$app->session->set('__importResult',
                    $importer->import($importForm->uploadedFile->tempName));

                return $this->redirect(['import', 'tableId' => $table->id]);
            }
        }

        throw new BadRequestHttpException();
    }

    public function actionImportExample($tableId)
    {
        $table = Table::findModel($tableId);
        $table->throwIfNoAccess(null, TablePermissions::CAN_VIEW_ALL_RECORDS);
        $table->throwIfNoAccess(null, TablePermissions::CAN_VIEW_SELF_RECORDS);

        $importer = new RecordsImporter();
        $importer->table = $table;
        $importer->downloadExample();
    }

    /**
     * @param $tableId
     * @param string $type
     *
     * @throws ForbiddenHttpException
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function actionExport($tableId, $type = 'xlsx')
    {
        $table = Table::findModel($tableId);
        $table->throwIfNoAccess(null, TablePermissions::CAN_VIEW_ALL_RECORDS);
        $table->throwIfNoAccess(null, TablePermissions::CAN_VIEW_SELF_RECORDS);
        $searchModel = new TableRecordSearch([
            'table' => $table,
        ]);

        $searchModel->load(Yii::$app->request->queryParams);
        $query = $searchModel->table->getRecords();
        $query = $searchModel->setupQuery($query);

        $exporter = new RecordsExporter();
        $exporter->query = $query;
        $exporter->table = $table;
        $exporter->export()->responseAsFile(null, $type);
    }

    /**
     * Edit table record
     *
     * @param $tableId
     * @param $recordId
     *
     * @return string|Response
     * @throws BadRequestHttpException
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function actionEdit($tableId, $recordId)
    {
        $table = Table::findModel($tableId);
        $tableRecord = TableRecordForm::findModel($recordId);

        if ($tableRecord->canUpdate() === false) {
            throw new ForbiddenHttpException();
        }

        if ($tableRecord->registry_table_id !== $table->id) {
            throw new BadRequestHttpException();
        }

        $tableRecord->populateRelation('table', $table);
        $tableRecord->editValues = [];

        foreach ($table->fields as $field) {
            $tableRecord->editValues[$field->id] =
                $tableRecord->values[$field->id] ?? $field->instantiateRecordValue(true);
            $tableRecord->editValues[$field->id]->parentModel = $tableRecord;
            $tableRecord->editValues[$field->id]->parentModelAttribute = 'editValues';
        }

        if ($tableRecord->load(Yii::$app->request->post())
            && $tableRecord->save()
        ) {
            Yii::$app->session->addFlash('success', 'Запись успешна отредактирована');
            return $this->redirect(['index', 'tableId' => $table->id]);
        }

        return $this->render('edit', [
            'table' => $table,
            'tableRecord' => $tableRecord,
        ]);
    }

    /**
     * Create table record
     *
     * @param int $tableId
     *
     * @return string|Response
     * @throws ForbiddenHttpException
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function actionCreate($tableId)
    {
        $table = Table::findModel($tableId);
        $table->throwIfNoAccess(null, TablePermissions::CAN_ADD_RECORDS);
        $tableRecord = new TableRecordForm();
        $tableRecord->registry_table_id = $table->id;
        $tableRecord->populateRelation('table', $table);

        foreach ($table->fields as $field) {
            $tableRecord->editValues[$field->id] = $field->instantiateRecordValue(true);
            $tableRecord->editValues[$field->id]->parentModel = $tableRecord;
            $tableRecord->editValues[$field->id]->parentModelAttribute = 'editValues';
        }

        if ($tableRecord->load(Yii::$app->request->post())
            && $tableRecord->save()
        ) {
            Yii::$app->session->addFlash('success', 'Запись успешна создана');
            return $this->redirect(['index', 'tableId' => $table->id]);
        }

        return $this->render('create', [
            'table' => $table,
            'tableRecord' => $tableRecord,
        ]);
    }

    /**
     * @param int $tableId
     * @param int $recordId
     *
     * @return Response
     *
     * @throws ForbiddenHttpException
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($tableId, $recordId)
    {
        $table = Table::findModel($tableId);
        $tableRecord = TableRecord::findModel($recordId);
        $tableRecord->populateRelationIfNeeded('table', $table);

        if ($tableRecord->canDelete()) {
            $tableRecord->delete();
            Yii::$app->session->addFlash('success', 'Запись успешна удалена');
            return $this->redirect(['index', 'tableId' => $table->id]);
        } else {
            throw new ForbiddenHttpException();
        }
    }
}
