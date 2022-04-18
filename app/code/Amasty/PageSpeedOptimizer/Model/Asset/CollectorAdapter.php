<?php

declare(strict_types=1);

namespace Amasty\PageSpeedOptimizer\Model\Asset;

use Amasty\PageSpeedTools\Model\Asset\AssetCollectorInterface;

class CollectorAdapter
{
    /**
     * @var array
     */
    private $collectors;

    public function __construct(array $collectors = [])
    {
        foreach ($collectors as $collector) {
            if (!($collector instanceof AssetCollectorInterface)) {
                throw new \LogicException(
                    sprintf('Asset collector must implement %s', AssetCollectorInterface::class)
                );
            }
        }

        $this->collectors = $collectors;
    }

    public function get(string $type): AssetCollectorInterface
    {
        if (!isset($this->collectors[$type])) {
            throw new \LogicException("Collector for type '{$type}' is not defined.");
        }

        return $this->collectors[$type];
    }

    public function getByTypes(array $types): array
    {
        return array_map(function ($type) {
            return $this->get($type);
        }, $types);
    }
}
