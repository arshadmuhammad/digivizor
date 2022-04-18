<?php

declare(strict_types=1);

namespace Amasty\PageSpeedOptimizer\Api;

use Amasty\PageSpeedOptimizer\Api\Data\DiagnosticResultInterface;

interface DiagnosticResultRepositoryInterface
{
    /**
     * @param $diagnosticResultId
     * @return \Amasty\PageSpeedOptimizer\Api\Data\DiagnosticResultInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByResultId($diagnosticResultId): DiagnosticResultInterface;

    /**
     * @param string $version
     * @param bool $isBefore
     * @return \Amasty\PageSpeedOptimizer\Api\Data\DiagnosticResultInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByVersionAndIsBefore(string $version, bool $isBefore): DiagnosticResultInterface;

    /**
     * @return array
     */
    public function getListResults(): array;

    /**
     * @param \Amasty\PageSpeedOptimizer\Api\Data\DiagnosticResultInterface $diagnosticResult
     * @return \Amasty\PageSpeedOptimizer\Api\Data\DiagnosticResultInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(DiagnosticResultInterface $diagnosticResult): DiagnosticResultInterface;

    /**
     * @param string $version
     * @return DiagnosticResultRepositoryInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function clearAfterResultByVersion(string $version): DiagnosticResultRepositoryInterface;
}
