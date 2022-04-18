<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Model\Command;

use Amasty\ImageOptimizer\Api\Data\QueueInterface;

class Jpegoptim100 extends ShellCommand
{
    public function getName(): string
    {
        return (string)__('Jpegoptim 100% quality');
    }

    public function getType(): string
    {
        return 'jpegoptim100';
    }

    protected function getCommand(): string
    {
        return 'jpegoptim --all-progressive --strip-xmp --strip-com --strip-exif --strip-iptc %s';
    }

    protected function prepareArguments(QueueInterface $queue, string $inputFile = '', string $outputFile = ''): array
    {
        return [$this->getMediaDirectory()->getAbsolutePath($inputFile)];
    }

    protected function getCheckCommand(): ?string
    {
        return 'jpegoptim --help';
    }

    protected function getCheckResult(): ?string
    {
        return 'Usage: jpegoptim';
    }
}
