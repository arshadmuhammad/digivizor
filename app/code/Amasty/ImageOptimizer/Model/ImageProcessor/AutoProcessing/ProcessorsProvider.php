<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Model\ImageProcessor\AutoProcessing;

class ProcessorsProvider
{
    /**
     * @var Processors\AutoProcessorInterface[]
     */
    private $processors;

    public function __construct(
        array $processors = []
    ) {
        $this->initializeProcessors($processors);
    }

    public function getAll(): array
    {
        return $this->processors;
    }

    public function getByName(?string $name = ''): ?Processors\AutoProcessorInterface
    {
        return $this->processors[$name] ?? null;
    }

    private function initializeProcessors(array $processors): void
    {
        foreach ($processors as $name => $processor) {
            if (!$processor instanceof Processors\AutoProcessorInterface) {
                throw new \LogicException(
                    sprintf('Processor must implement %s', Processors\AutoProcessorInterface::class)
                );
            }
            $this->processors[$name] = $processor;
        }
    }
}
