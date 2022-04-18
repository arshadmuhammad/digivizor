<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Model\Command;

use Amasty\ImageOptimizer\Api\Data\QueueInterface;

class Jpegoptim80 extends ShellCommand
{
    public function getName(): string
    {
        return (string)__('Jpegoptim 80% quality');
    }

    public function getType(): string
    {
        return 'jpegoptim80';
    }

    protected function getCommand(): string
    {
        return 'jpegoptim --all-progressive --strip-all -m 80 %s';
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
