<?php

namespace Matodor\RegistryConstructor\models\data\File;

use Matodor\RegistryConstructor\models\TableRecordValue;
use Matodor\RegistryConstructor\Module;
use Yii;
use yii\helpers\FileHelper;

/**
 * @property string|null $file_token
 * @property string|null $file_ext
 * @property string|null $file_name
 *
 * @property-read string $filePath
 * @property-read string $formattedFileSize
 * @property-read int $fileSize
 * @property-read string $originalFileName
 * @property-read array $downloadRoute
 * @property-read array $inlineRoute
 */
class Value extends TableRecordValue
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%registry_table_records_data_file}}';
    }

    public static function additionalColumns()
    {
        return [
            'file_token',
            'file_ext',
            'file_name',
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[static::SCENARIO_DEFAULT][] = '!file_token';
        $scenarios[static::SCENARIO_DEFAULT][] = '!file_name';
        $scenarios[static::SCENARIO_DEFAULT][] = '!file_ext';

        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules['fileTokenString'] = ['file_token', 'string', 'max' => 32];
        $rules['fileNameString'] = ['file_name', 'string', 'max' => 255];
        $rules['fileExtString'] = ['file_ext', 'string', 'max' => 8];

        return $rules;
    }

    /**
     * {@inheritdoc}
     * @noinspection DuplicatedCode
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['file_token'] = 'Токен доступа';
        $labels['file_ext'] = 'Расширение файла';
        $labels['file_name'] = 'Имя файла';

        return $labels;
    }

    /**
     * @return string
     * @noinspection PhpUnused
     */
    public function getOriginalFileName()
    {
        return "{$this->file_name}.{$this->file_ext}";
    }

    public function getRecordsQuery()
    {
        return parent::getRecordsQuery()
            ->andFilterWhere(['file_name' => $this->file_name])
            ->andFilterWhere(['file_ext' => $this->file_ext])
            ->andFilterWhere(['file_token' => $this->file_token]);
    }

    public function getIsValueSet()
    {
        return $this->file_token !== null
            && $this->file_ext !== null
            && file_exists($this->getFilePath());
    }

    protected function resetFileInfo()
    {
        $this->file_token = null;
        $this->file_name = null;
        $this->file_ext = null;
    }

    /**
     * @return string
     * @noinspection PhpUnused
     */
    public function getFormattedFileSize()
    {
        return Yii::$app->formatter->asShortSize($this->fileSize, 1);
    }

    /**
     * @return false|int
     */
    public function getFileSize()
    {
        if ($this->getIsValueSet()) {
            return filesize($this->filePath);
        } else {
            return -1;
        }
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        $prefix = [];
        $prefix[] = Yii::getAlias($this->module->storagePath);
        $prefix[] = mb_strtolower(mb_substr($this->file_token, 0, 2));
        $prefix[] = mb_strtolower(mb_substr($this->file_token, -2));
        $prefix[] = "{$this->file_token}.{$this->file_ext}";

        return FileHelper::normalizePath(join(DIRECTORY_SEPARATOR, $prefix));
    }

    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        if ($this->getIsValueSet()) {
            FileHelper::unlink($this->filePath);
        }

        return true;
    }

    /**
     * @return array
     */
    public function getDownloadRoute()
    {
        return [
            'records/download-file',
            'token' => $this->file_token,
            'tableId' => $this->field->table->id,
        ];
    }

    /**
     * @return array
     */
    public function getInlineRoute()
    {
        return [
            'records/download-file',
            'token' => $this->file_token,
            'tableId' => $this->field->table->id,
            'inline' => true,
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getSelectTitle()
    {
        return 'Файл';
    }

    /**
     * @return string
     */
    public function getRawValue()
    {
        return "{$this->getOriginalFileName()} ({$this->getFormattedFileSize()})";
    }
}
