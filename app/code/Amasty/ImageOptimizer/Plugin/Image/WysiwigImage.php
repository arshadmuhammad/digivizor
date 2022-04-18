<?php

namespace Amasty\ImageOptimizer\Plugin\Image;

use Amasty\ImageOptimizer\Model\Image\ClearGeneratedImageForFile;

class WysiwigImage
{
    /**
     * @var ClearGeneratedImageForFile
     */
    private $clearGeneratedImageForFile;

    public function __construct(ClearGeneratedImageForFile $clearGeneratedImageForFile)
    {
        $this->clearGeneratedImageForFile = $clearGeneratedImageForFile;
    }

    /**
     * @param $subject
     * @param $target
     */
    public function beforeDeleteFile($subject, $target)
    {
        $this->clearGeneratedImageForFile->execute($target);
    }
}
