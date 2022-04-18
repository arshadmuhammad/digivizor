<?php

namespace Amasty\ImageOptimizer\Model\Image\Directory;

interface FileSelectorInterface
{
    public function selectFiles(array $files, string $imageDirectory): array;
}
