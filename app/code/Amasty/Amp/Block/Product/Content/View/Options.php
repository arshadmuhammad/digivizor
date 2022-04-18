<?php

namespace Amasty\Amp\Block\Product\Content\View;

use Magento\Catalog\Api\Data\ProductCustomOptionInterface;

class Options extends \Magento\Catalog\Block\Product\View\Options
{
    protected $isNotAllOptionsAvailable = false;

    /**
     * @param \Magento\Catalog\Model\Product\Option $option
     * @return string
     */
    public function getOptionHtml(\Magento\Catalog\Model\Product\Option $option)
    {
        $html = '';
        $forbiddenTypes = [
            ProductCustomOptionInterface::OPTION_GROUP_FILE,
            ProductCustomOptionInterface::OPTION_GROUP_DATE,
            ProductCustomOptionInterface::OPTION_TYPE_DATE_TIME,
            ProductCustomOptionInterface::OPTION_TYPE_TIME,
        ];
        if (!in_array($option->getType(), $forbiddenTypes)) {
            $html = parent::getOptionHtml($option);
        } else {
            $this->setIsNotAllOptionsAvailable(true);
        }

        return $html;
    }

    /**
     * @return bool
     */
    public function getIsNotAllOptionsAvailable(): bool
    {
        return $this->isNotAllOptionsAvailable;
    }

    /**
     * @param bool $isNotAllOptionsAvailable
     */
    public function setIsNotAllOptionsAvailable(bool $isNotAllOptionsAvailable): void
    {
        $this->isNotAllOptionsAvailable = $isNotAllOptionsAvailable;
    }
}
