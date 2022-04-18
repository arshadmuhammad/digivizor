<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Model\Command;

use Amasty\ImageOptimizer\Api\Data\QueueInterface;

interface CommandInterface
{
    public function getName(): string;

    public function getType(): string;

    public function run(QueueInterface $queue, string $inputFile, string $outputFile = ''): void;

    public function isAvailable(): bool;

    public function getUnavailableReason(): string;
}
