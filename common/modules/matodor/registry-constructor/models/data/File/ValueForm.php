<?php

/** @noinspection PhpHierarchyChecksInspection */

namespace Matodor\RegistryConstructor\models\data\File;

use Matodor\RegistryConstructor\traits\TableRecordValueFormTrait;
use Yii;
use yii\base\Exception;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

/**
 * @mixin TableRecordValueFormTrait
 */
class ValueForm extends Value
{
    use TableRecordValueFormTrait;

    /**
     * @var UploadedFile|null
     */
    public $uploadedFile;

    /**
     * @var boolean
     */
    public $removeFile = false;

    /**
     * @var string|null
     */
    public $removeFilePath = null;

    public function rules()
    {
        $rules = parent::rules();
        $rules['uploadedFileFile'] = ['uploadedFile', 'file'];
        $rules['removeFileBoolean'] = ['removeFile', 'boolean'];

        return $rules;
    }

    /**
     * {@inheritdoc}
     * @noinspection DuplicatedCode
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['removeFile'] = 'Удалить сохраненный файл?';

        return $labels;
    }

    public function beforeValidate()
    {
        if ($this->removeFile) {
            $this->uploadedFile = null;
        } else {
            $this->uploadedFile = UploadedFile::getInstance($this, 'uploadedFile');

            if ($this->uploadedFile instanceof UploadedFile) {
                if ($this->getIsValueSet()) {
                    $this->removeFilePath = $this->getFilePath();
                }

                $this->setFileInfo();

                if (!$this->saveFile(false)) {
                    $this->addError('uploadedFile', 'Ошибка при сохранении файла');
                    $this->resetFileInfo();
                }
            }
        }

        return parent::beforeValidate();
    }

    /**
     * @throws Exception
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($this->removeFile) {
            $this->uploadedFile = null;
            $this->removeFilePath = $this->getFilePath();
            $this->resetFileInfo();
        } else if ($this->uploadedFile instanceof UploadedFile) {
            if (!$this->saveFile()) {
                $this->addError('uploadedFile', 'Ошибка при сохранении файла');
                $this->resetFileInfo();
                return false;
            }
        }

        return true;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($this->removeFilePath !== null) {
            FileHelper::unlink($this->removeFilePath);
        }
    }

    protected function setFileInfo()
    {
        $this->file_token = Yii::$app->security->generateRandomString(32);
        $this->file_name = $this->uploadedFile->baseName;
        $this->file_ext = $this->uploadedFile->extension;
    }

    /**
     * @param bool $deleteTempFile
     *
     * @return bool
     * @throws Exception
     */
    protected function saveFile(bool $deleteTempFile = true)
    {
        $savePath = $this->getFilePath();
        FileHelper::createDirectory(dirname($savePath));

        return $this->uploadedFile->saveAs($savePath, $deleteTempFile);
    }
}
