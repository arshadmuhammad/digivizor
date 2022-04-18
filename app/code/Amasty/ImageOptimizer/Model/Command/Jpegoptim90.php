<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Model\Command;

use Amasty\ImageOptimizer\Api\Data\QueueInterface;

class Jpegoptim90 extends ShellCommand
{
    public function getName(): string
    {
        return (string)__('Jpegoptim 90% quality');
    }

    public function getType(): string
    {
        return 'jpegoptim90';
    }

    protected function getCommand(): string
    {
        return 'jpegoptim --all-progressive --strip-all -m 90 %s';
    }

    protected function getCheckCommand(): ?string
    {
        return 'jpegoptim --help';
    }

    protected function getCheckResult(): ?string
    {
        return 'Usage: jpegoptim';
    }

    protected function prepareArguments(QueueInterface $queue, string $inputFile = '', string $outputFile = ''): array
    {
        return [$this->getMediaDirectory()->getAbsolutePath($inputFile)];
    }
}
