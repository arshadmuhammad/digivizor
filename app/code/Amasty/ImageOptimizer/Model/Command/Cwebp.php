<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Model\Command;

use Amasty\ImageOptimizer\Api\Data\QueueInterface;

class Cwebp extends ShellCommand
{
    public function getName(): string
    {
        return (string)__('Cwebp');
    }

    public function getType(): string
    {
        return 'cwebp';
    }

    protected function getCommand(): string
    {
        return 'cwebp %s -o %s';
    }

    protected function getCheckCommand(): ?string
    {
        return 'cwebp -help';
    }

    protected function getCheckResult(): ?string
    {
        return 'cwebp [options]';
    }

    protected function prepareArguments(QueueInterface $queue, string $inputFile = '', string $outputFile = ''): array
    {
        return [
            $this->getMediaDirectory()->getAbsolutePath($inputFile),
            $this->getMediaDirectory()->getAbsolutePath($outputFile)
        ];
    }
}
