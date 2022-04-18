<?php

declare(strict_types=1);

namespace Amasty\PageSpeedTools\Model\Image;

class ReplacerComposite implements ReplacerCompositeInterface
{
    /**
     * @var ReplacerInterface[]
     */
    private $imageReplacers;

    public function __construct(array $imageReplacers = [])
    {
        foreach ($imageReplacers as $replacer) {
            if (!($replacer instanceof ReplacerInterface)) {
                throw new \LogicException(
                    sprintf('Image replacer must implement %s', ReplacerInterface::class)
                );
            }
        }

        $this->imageReplacers = $imageReplacers;
    }

    public function replace(string $algorithm, string $image, string $imagePath): string
    {
        if (!isset($this->imageReplacers[$algorithm])) {
            throw new \LogicException("Image replacer for algorithm '{$algorithm}' is not defined.");
        }

        return $this->imageReplacers[$algorithm]->execute($image, $imagePath);
    }
}
