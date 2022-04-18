<?php
declare(strict_types=1);

namespace Amasty\PageSpeedOptimizer\Api\Data;

interface DiagnosticResultInterface
{
    /**
     * @return ?int
     */
    public function getResultId(): ?int;

    /**
     * @param ?int $resultId
     *
     * @return \Amasty\PageSpeedOptimizer\Api\Data\DiagnosticResultInterface
     */
    public function setResultId(?int $resultId): DiagnosticResultInterface;

    /**
     * @return ?string
     */
    public function getResult(): ?string;

    /**
     * @param ?string $result
     *
     * @return \Amasty\PageSpeedOptimizer\Api\Data\DiagnosticResultInterface
     */
    public function setResult(?string $result): DiagnosticResultInterface;

    /**
     * @return bool
     */
    public function getIsBefore(): bool;

    /**
     * @param bool $isBefore
     *
     * @return \Amasty\PageSpeedOptimizer\Api\Data\DiagnosticResultInterface
     */
    public function setIsBefore(bool $isBefore): DiagnosticResultInterface;

    /**
     * @return string
     */
    public function getVersion(): string;

    /**
     * @param string $version
     *
     * @return \Amasty\PageSpeedOptimizer\Api\Data\DiagnosticResultInterface
     */
    public function setVersion(string $version): DiagnosticResultInterface;
}
