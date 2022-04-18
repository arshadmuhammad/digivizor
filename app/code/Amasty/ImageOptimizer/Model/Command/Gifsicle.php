<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Model\Command;

use Amasty\ImageOptimizer\Api\Data\QueueInterface;

class Gifsicle extends ShellCommand
{
    public function getName(): string
    {
        return (string)__('Gifsicle');
    }

    public function getType(): string
    {
        return 'gifsicle';
    }

    protected function getCommand(): string
    {
        return 'gifsicle %s -o %s';
    }

    protected function getCheckCommand(): ?string
    {
        return 'gifsicle --help';
    }

    protected function getCheckResult(): ?string
    {
        return 'Usage: gifsicle';
    }

    protected function prepareArguments(QueueInterface $queue, string $inputFile = '', string $outputFile = ''): array
    {
        return [
            $this->getMediaDirectory()->getAbsolutePath($inputFile),
            $this->getMediaDirectory()->getAbsolutePath($outputFile)
        ];
    }
}
