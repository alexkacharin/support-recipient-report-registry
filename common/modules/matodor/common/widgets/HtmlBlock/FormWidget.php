<?php

namespace Matodor\Common\widgets\HtmlBlock;

use Matodor\Common\models\HtmlBlock;

class FormWidget extends BaseWidget
{
    /**
     * @var boolean $asTextInput
     */
    public $asTextInput = false;

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        /** @var HtmlBlock $htmlBlock */
        $htmlBlock = HtmlBlock::findModel(['key' => $this->key], false);

        if ($htmlBlock === null) {
            $htmlBlock = new HtmlBlock(['key' => $this->key]);
            $htmlBlock->save();
        }

        return $this->render('form', [
            'block' => $htmlBlock,
            'asTextInput' => $this->asTextInput,
        ]);
    }
}
