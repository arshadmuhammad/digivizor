<?php
declare(strict_types=1);

namespace Amasty\PageSpeedTools\Model\Output;

class OutputChain implements OutputChainInterface
{
    /**
     * @var AmpRequestChecker
     */
    private $ampRequestChecker;

    /**
     * @var OutputProcessorInterface[]
     */
    private $ampProcessors;

    /**
     * @var OutputProcessorInterface[]
     */
    private $processors;

    public function __construct(
        AmpRequestChecker $ampRequestChecker,
        $ampProcessors,
        $processors
    ) {
        $this->ampRequestChecker = $ampRequestChecker;
        $this->ampProcessors = $ampProcessors;
        $this->processors = $processors;
    }

    public function process(string &$output): bool
    {
        $result = true;
        if ($pageProcessors = $this->getSortedPageProcessors()) {
            foreach ($pageProcessors as $processor) {
                if (!$processor->process($output)) {
                    $result = false;
                    break;
                }
            }
        } else {
            $result = false;
        }

        return $result;
    }

    public function getPageProcessors(): array
    {
        return $this->ampRequestChecker->check() ? $this->ampProcessors : $this->processors;
    }

    public function getSortedPageProcessors(): array
    {
        $pageProcessors = $this->getPageProcessors();
        if (empty($pageProcessors)) {
            return [];
        }

        $result = [];
        foreach ($pageProcessors as $pageProcessorCode => $pageProcessor) {
            if (!isset($pageProcessor['sortOrder'])) {
                new \LogicException('"sortOrder" is not specified for page processor "' . $pageProcessorCode . '"');
            }

            $sortOrder = (int)$pageProcessor['sortOrder'];
            if (!isset($result[$sortOrder])) {
                $result[$sortOrder] = [];
            }

            $result[$sortOrder][$pageProcessorCode] = $pageProcessor['processor'];
        }

        if (empty($result)) {
            return [];
        }

        ksort($result);

        return array_merge(...$result);
    }
}
