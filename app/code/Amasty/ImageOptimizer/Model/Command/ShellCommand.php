<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Model\Command;

use Amasty\ImageOptimizer\Api\Data\QueueInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Shell;
use Psr\Log\LoggerInterface;

abstract class ShellCommand implements CommandInterface
{
    /**
     * @var Shell
     */
    private $shell;

    /**
     * @var WriteInterface
     */
    private $mediaDirectory;

    /**
     * @var bool
     */
    private $toolAvailable;

    /**
     * @var string
     */
    private $unavailableReason;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Shell $shell,
        Filesystem $filesystem,
        LoggerInterface $logger
    ) {
        $this->shell = $shell;
        $this->filesystem = $filesystem;
        $this->logger = $logger;
    }

    public function run(QueueInterface $queue, string $inputFile, string $outputFile = ''): void
    {
        if (null === $this->toolAvailable) {
            $this->toolAvailable = $this->isAvailable();
        }
        if ($this->toolAvailable) {
            try {
                $this->shell->execute($this->getCommand(), $this->prepareArguments($queue, $inputFile, $outputFile));
            } catch (\Exception $e) {
                $this->logger->error($e);
            }
        }
    }

    public function isAvailable(): bool
    {
        if (!$this->getCheckCommand()) {
            return true;
        }

        try {
            $output = $this->shell->execute($this->getCheckCommand() . ' 2>&1');
        } catch (LocalizedException $e) {
            if (!$e->getPrevious()) {
                $this->setUnavailableReason((string)__('exec function is disabled.'));

                return false;
            }
        }

        if (!empty($output) && false !== strpos($output, $this->getCheckResult())) {
            return true;
        }

        $this->setUnavailableReason((string)__('Image Optimization Tool "%1" is not installed', $this->getName()));

        return false;
    }

    public function getUnavailableReason(): string
    {
        return (string)$this->unavailableReason;
    }

    protected function setUnavailableReason(string $reason): void
    {
        $this->unavailableReason = $reason;
    }

    protected function getMediaDirectory(): WriteInterface
    {
        if ($this->mediaDirectory === null) {
            $this->mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        }

        return $this->mediaDirectory;
    }

    abstract protected function getCommand(): string;

    abstract protected function getCheckCommand(): ?string;

    abstract protected function getCheckResult(): ?string;

    abstract protected function prepareArguments(
        QueueInterface $queue,
        string $inputFile = '',
        string $outputFile = ''
    ): array;
}
