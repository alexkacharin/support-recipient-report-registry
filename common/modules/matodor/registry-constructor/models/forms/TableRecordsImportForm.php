<?php

namespace Matodor\RegistryConstructor\models\forms;

use Matodor\RegistryConstructor\models\Table;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 *
 */
class TableRecordsImportForm extends Model
{
    /**
     * @var Table
     */
    public $table;

    /**
     * @var UploadedFile|null
     */
    public $uploadedFile;

    public function rules()
    {
        return [
            ['uploadedFile', 'required'],
            ['uploadedFile', 'file', 'extensions' => 'xlsx', 'skipOnEmpty' => false],
        ];
    }

    public function attributeLabels()
    {
        return [
            'uploadedFile' => 'Файл для импорта',
        ];
    }

    public function upload()
    {
        $this->uploadedFile = UploadedFile::getInstance($this, 'uploadedFile');

        return $this->uploadedFile instanceof UploadedFile;
    }
}
