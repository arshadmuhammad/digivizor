<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Model\ImageProcessor;

use Amasty\ImageOptimizer\Api\Data\ImageSettingInterface;
use Amasty\ImageOptimizer\Api\Data\QueueInterface;
use Amasty\ImageOptimizer\Model\Command\CommandProvider;
use Magento\Framework\Exception\LocalizedException;

class OptimizeJpeg implements ImageProcessorInterface
{
    /**
     * @var CommandProvider
     */
    private $jpegCommandProvider;

    /**
     * @var array
     */
    private $availableTools = [];

    public function __construct(CommandProvider $jpegCommandProvider)
    {
        $this->jpegCommandProvider = $jpegCommandProvider;
    }

    public function process(QueueInterface $queue): void
    {
        if (!$queue->getTool()) {
            return;
        }
        $this->jpegCommandProvider->get($queue->getTool())->run($queue, (string)$queue->getFilename());
    }

    public function prepareQueue(string $file, ImageSettingInterface $imageSetting, QueueInterface $queue): bool
    {
        if (!$imageSetting->getJpegTool()) {
            return false;
        }
        if (!isset($this->availableTools[$imageSetting->getJpegTool()])) {
            try {
                $this->availableTools[$imageSetting->getJpegTool()] = $this->jpegCommandProvider
                    ->get($imageSetting->getJpegTool())
                    ->isAvailable();
            } catch (LocalizedException $e) {
                $this->availableTools[$imageSetting->getJpegTool()] = false;
            }
        }

        if (!$this->availableTools[$imageSetting->getJpegTool()]) {
            $queue->setTool('');

            return false;
        }
        $queue->setTool($imageSetting->getJpegTool());

        return true;
    }
}
