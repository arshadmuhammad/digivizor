<?php

namespace Amasty\PageSpeedTools\Model\Image;

interface ReplacerInterface
{
    public function execute(string $image, string $imagePath): string;
}
