<?php

namespace Matodor\Common\widgets\HtmlBlock;

class DisplayWidget extends BaseWidget
{
    /**
     * @var bool
     */
    public $encode = false;

    /**
     * @var string|null
     */
    public $tag = null;

    /**
     * @var string[]
     */
    public $containerOptions = [
        'class' => 'html-block',
    ];

    public function run()
    {
        return $this->render('content', [
            'block' => $this->htmlBlock,
            'encode' => $this->encode,
            'containerOptions' => $this->containerOptions,
            'tag' => $this->tag,
        ]);
    }
}
