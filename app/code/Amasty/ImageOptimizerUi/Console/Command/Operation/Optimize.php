<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizerUi\Console\Command\Operation;

use Amasty\ImageOptimizer\Api\ImageQueueServiceInterface;
use Amasty\ImageOptimizer\Model\ConfigProvider;
use Amasty\ImageOptimizer\Model\Image\ForceOptimization;
use Amasty\ImageOptimizer\Model\Image\GenerateQueue;
use Amasty\ImageOptimizer\Model\Image\ImageSetting;
use Amasty\ImageOptimizer\Model\ImageProcessor;
use Amasty\ImageOptimizerUi\Console\Command\OptimizeCommand;
use Amasty\ImageOptimizerUi\Model\Image\ResourceModel\CollectionFactory;
use Amasty\PageSpeedTools\Model\JobManagerFactory;
use Magento\Framework\App\ObjectManager;

class Optimize implements CommandOperationInterface
{
    /**
     * @var ForceOptimization
     */
    private $forceOptimization;

    /**
     * @var ImageQueueServiceInterface
     */
    private $queueService;

    /**
     * @var GenerateQueue
     */
    private $generateQueue;

    /**
     * @var JobManagerFactory
     */
    private $jobManagerFactory;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var array
     */
    private $batches = [];

    /**
     * @var ImageProcessor
     */
    private $imageProcessor;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        CollectionFactory $collectionFactory,
        ForceOptimization $forceOptimization,
        ImageQueueServiceInterface $queueService,
        GenerateQueue $generateQueue,
        JobManagerFactory $jobManagerFactory,
        ConfigProvider $configProvider,
        ImageProcessor $imageProcessor
    ) {
        $this->forceOptimization = $forceOptimization;
        $this->queueService = $queueService;
        $this->generateQueue = $generateQueue;
        $this->jobManagerFactory = $jobManagerFactory;
        $this->configProvider = $configProvider;
        $this->imageProcessor = $imageProcessor;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) :?int {
        $output->writeln('<info>Generating Images Queue.</info>');
        $imageSettingCollection = $this->collectionFactory->create();
        $imageSettingCollection->addFilter(ImageSetting::IS_ENABLED, true);
        if ($imageSettingId = (int)$input->getOption(OptimizeCommand::IMAGE_SETTING_ID)) {
            $imageSettingCollection->addFilter(ImageSetting::IMAGE_SETTING_ID, $imageSettingId);
        }
        $queueSize = $this->generateQueue->generateQueue($imageSettingCollection->getItems());
        $counter = 0;

        $maxJobs = $input->getOption(OptimizeCommand::JOBS_AMOUNT) ?: $this->configProvider->getMaxJobsCount();
        $maxJobs = (int)$maxJobs;
        if ($maxJobs > 1) {
            if (!function_exists('pcntl_fork')) {
                $output->writeln(__('Warning: \'pcntl\' php extension is required for parallel image optimization.'));
                $maxJobs = 1;
            }
        }

        $multiProcessMode = $maxJobs > 1;

        /** @var \Symfony\Component\Console\Helper\ProgressBar $progressBar */
        $progressBar = ObjectManager::getInstance()->create(
            \Symfony\Component\Console\Helper\ProgressBar::class,
            [
                'output' => $output,
                'max' => ceil($queueSize/100)
            ]
        );
        $progressBar->setFormat(
            '<info>%message%</info> %current%/%max% [%bar%]'
        );
        $output->writeln('<info>Optimization Process Started.</info>');
        $progressBar->start();
        $progressBar->display();

        if ($multiProcessMode) {
            /** @var \Amasty\PageSpeedTools\Model\JobManager $jobManager */
            $jobManager = $this->jobManagerFactory->create(['maxJobs' => $maxJobs]);
            while (!$this->queueService->isQueueEmpty()) {
                $this->batches[] = $this->queueService->shuffleQueues(100);
            }

            while (!empty($this->batches)) {
                if ($jobManager->waitForFreeSlot()) {
                    $progressBar->advance();
                }
                $imagesQueue = array_shift($this->batches);
                $counter += count($imagesQueue);
                $progressBar->setMessage('Process Images ' . ($counter) . ' from ' . $queueSize . '...');
                $progressBar->display();
                if (!$jobManager->fork()) { // Child process
                    foreach ($imagesQueue as $queue) {
                        $this->imageProcessor->process($queue);
                    }

                    return 0;
                }
            }

            $jobManager->waitForAllJobs();
        } else {
            while (!$this->queueService->isQueueEmpty()) {
                $progressBar->setMessage('Process Images ' . (($counter++) * 100) . ' from ' . $queueSize . '...');
                $progressBar->display();
                $this->forceOptimization->execute(100);
                $progressBar->advance();
            }
        }

        $progressBar->setMessage('Process Images ' . $queueSize . ' from ' . $queueSize . '...');
        $progressBar->display();
        $progressBar->finish();
        $output->writeln('');
        $output->writeln('<info>Images were optimized successfully.</info>');

        return 0;
    }
}
