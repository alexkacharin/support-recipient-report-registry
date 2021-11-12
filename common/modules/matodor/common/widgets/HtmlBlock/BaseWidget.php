<?php

namespace Matodor\Common\widgets\HtmlBlock;

use Matodor\Common\components\Helper;
use Matodor\Common\models\HtmlBlock;
use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;
use yii\widgets\ActiveForm;

class BaseWidget extends Widget
{
    /**
     * @var string
     */
    public $key = '';

    /**
     * @var string
     */
    public $defaultContent = '';

    /**
     * @var HtmlBlock
     */
    public $htmlBlock = null;

    /**
     * @throws Exception
     */
    public function beforeRun()
    {
        $this->setupBlock();
        return parent::beforeRun();
    }

    /**
     * @param $key
     *
     * @return array|HtmlBlock|ActiveRecord|null
     */
    public static function find($key)
    {
        return HtmlBlock::find()
            ->where(['key' => $key])
            ->limit(1)
            ->one();
    }

    public static function encodedValue($key, $defaultValue = '')
    {
        $htmlBlock = static::find($key);
        return Html::encode($htmlBlock !== null
            ? $htmlBlock->content
            : $defaultValue
        );
    }

    /**
     * @throws Exception
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    protected function setupBlock()
    {
        if ($this->htmlBlock !== null) {
            return;
        }

        if (Helper::isEmpty($this->key)) {
            throw new Exception('Missing required `key` parameters');
        }

        $this->htmlBlock = HtmlBlock::findModel([
            'key' => $this->key,
        ], false);

        if ($this->htmlBlock === null) {
            $this->htmlBlock = new HtmlBlock([
                'key' => $this->key,
                'content' => $this->defaultContent,
            ]);
            $this->htmlBlock->save();
        }
    }

    public static function saveAll()
    {
        if (!Yii::$app->request->isPost) {
            return;
        }

        $data = Yii::$app->request->post();
        $data = ArrayHelper::getValue($data, HtmlBlock::instance()->formName());

        if (!is_array($data)) {
            return;
        }

        foreach ($data as $key => $attributes) {
            if (!is_array($attributes)) {
                continue;
            }

            $block = static::find($key);

            if ($block === null) {
                $block = new HtmlBlock([
                    'key' => $key,
                ]);
            }

            if ($block->load($attributes, '')) {
                $block->save();
            }
        }
    }
}
