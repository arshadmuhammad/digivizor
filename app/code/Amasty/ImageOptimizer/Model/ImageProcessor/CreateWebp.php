<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Model\ImageProcessor;

use Amasty\ImageOptimizer\Api\Data\ImageSettingInterface;
use Amasty\ImageOptimizer\Api\Data\QueueInterface;
use Amasty\ImageOptimizer\Model\Command\CommandProvider;
use Amasty\PageSpeedTools\Model\OptionSource\Resolutions;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;

class CreateWebp implements ImageProcessorInterface
{
    /**
     * @var CommandProvider
     */
    private $webpCommandProvider;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var WriteInterface
     */
    private $mediaDirectory;

    /**
     * @var array
     */
    private $availableTools = [];

    public function __construct(
        CommandProvider $webpCommandProvider,
        Filesystem $filesystem
    ) {
        $this->webpCommandProvider = $webpCommandProvider;
        $this->filesystem = $filesystem;
    }

    public function process(QueueInterface $queue): void
    {
        if (!$queue->getWebpTool()) {
            return;
        }

        $webpCommand = $this->webpCommandProvider->get($queue->getWebpTool());
        $filename = (string)$queue->getFilename();

        $webpCommand->run(
            $queue,
            $filename,
            $this->getWebpFileName($filename, $queue)
        );
    }

    public function prepareQueue(string $file, ImageSettingInterface $imageSetting, QueueInterface $queue): bool
    {
        if (!$imageSetting->getWebpTool()) {
            return false;
        }
        if (!isset($this->availableTools[$imageSetting->getWebpTool()])) {
            try {
                $this->availableTools[$imageSetting->getWebpTool()] = $this->webpCommandProvider
                    ->get($imageSetting->getWebpTool())
                    ->isAvailable();
            } catch (LocalizedException $e) {
                $this->availableTools[$imageSetting->getWebpTool()] = false;
            }
        }

        if (!$this->availableTools[$imageSetting->getWebpTool()]) {
            $queue->setWebpTool('');

            return false;
        }
        $queue->setWebpTool($imageSetting->getWebpTool());

        return true;
    }

    private function getMediaDirectory(): WriteInterface
    {
        if ($this->mediaDirectory === null) {
            $this->mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        }

        return $this->mediaDirectory;
    }

    private function getWebpFileName(string $imagePath, QueueInterface $queue): string
    {
        $webPPath = str_replace(
            $queue->getFilename(),
            Resolutions::WEBP_DIR . $queue->getFilename(),
            $this->getMediaDirectory()->getAbsolutePath($imagePath)
        );
        if (!$this->getMediaDirectory()->isExist($this->dirname($webPPath))) {
            $this->getMediaDirectory()->create($this->dirname($webPPath));
        }

        return str_replace(
            '.' . $queue->getExtension(),
            '_'. $queue->getExtension() . '.webp',
            $webPPath
        );
    }

    private function dirname(string $file): string
    {
        //phpcs:ignore
        return dirname($file);
    }
}
