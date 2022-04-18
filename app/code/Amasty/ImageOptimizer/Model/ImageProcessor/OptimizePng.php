<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Model\ImageProcessor;

use Amasty\ImageOptimizer\Api\Data\ImageSettingInterface;
use Amasty\ImageOptimizer\Api\Data\QueueInterface;
use Amasty\ImageOptimizer\Model\Command\CommandProvider;
use Magento\Framework\Exception\LocalizedException;

class OptimizePng implements ImageProcessorInterface
{
    /**
     * @var CommandProvider
     */
    private $pngCommandProvider;

    /**
     * @var array
     */
    private $availableTools = [];

    public function __construct(CommandProvider $pngCommandProvider)
    {
        $this->pngCommandProvider = $pngCommandProvider;
    }

    public function process(QueueInterface $queue): void
    {
        if (!$queue->getTool()) {
            return;
        }
        $this->pngCommandProvider->get($queue->getTool())->run($queue, (string)$queue->getFilename());
    }

    public function prepareQueue(string $file, ImageSettingInterface $imageSetting, QueueInterface $queue): bool
    {
        if (!$imageSetting->getPngTool()) {
            return false;
        }
        if (!isset($this->availableTools[$imageSetting->getPngTool()])) {
            try {
                $this->availableTools[$imageSetting->getPngTool()] = $this->pngCommandProvider
                    ->get($imageSetting->getPngTool())
                    ->isAvailable();
            } catch (LocalizedException $e) {
                $this->availableTools[$imageSetting->getPngTool()] = false;
            }
        }

        if (!$this->availableTools[$imageSetting->getPngTool()]) {
            $queue->setTool('');

            return false;
        }
        $queue->setTool($imageSetting->getPngTool());

        return true;
    }
}
