<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Model\Command;

use Amasty\ImageOptimizer\Api\Data\QueueInterface;

class Optipng extends ShellCommand
{
    public function getName(): string
    {
        return (string)__('Optipng');
    }

    public function getType(): string
    {
        return 'optipng';
    }

    protected function getCommand(): string
    {
        return 'optipng %s';
    }

    protected function getCheckCommand(): ?string
    {
        return 'optipng --help';
    }

    protected function getCheckResult(): ?string
    {
        return 'optipng [options] files';
    }

    protected function prepareArguments(QueueInterface $queue, string $inputFile = '', string $outputFile = ''): array
    {
        return [$this->getMediaDirectory()->getAbsolutePath($inputFile)];
    }
}
