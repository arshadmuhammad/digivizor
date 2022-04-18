<?php

namespace Amasty\PageSpeedOptimizer\Model\HeaderProvider;

class IsSetXFrameOptions
{
    /**
     * @var bool
     */
    private $isSetHeader = false;

    /**
     * @var string
     */
    private $baseUrl = '';

    /**
     * @param $isSetHeader
     *
     * @return $this
     */
    public function setIsSetHeader(bool $isSetHeader): self
    {
        $this->isSetHeader = $isSetHeader;

        return $this;
    }

    public function isSetHeader(): bool
    {
        return $this->isSetHeader;
    }

    /**
     * @param string $baseUrl
     *
     * @return $this
     */
    public function setBaseUrl(string $baseUrl): self
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }
}
