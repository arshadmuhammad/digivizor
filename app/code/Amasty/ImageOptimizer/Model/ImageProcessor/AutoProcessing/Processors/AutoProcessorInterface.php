<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Model\ImageProcessor\AutoProcessing\Processors;

interface AutoProcessorInterface
{
    /**
     * @param string $imgPath
     * @return void
     */
    public function execute(string $imgPath): void;
}
