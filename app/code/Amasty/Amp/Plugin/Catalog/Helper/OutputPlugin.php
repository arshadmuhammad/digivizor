<?php

namespace Amasty\Amp\Plugin\Catalog\Helper;

use Magento\Catalog\Helper\Output;

class OutputPlugin
{
    const DESCRIPTION = 'description';
    const SHORT_DESCRIPTION = 'short_description';

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $configProvider;

    /**
     * @var \Amasty\Amp\Model\HtmlValidator
     */
    private $validator;

    public function __construct(
        \Amasty\Amp\Model\ConfigProvider $configProvider,
        \Amasty\Amp\Model\HtmlValidator $validator
    ) {
        $this->configProvider = $configProvider;
        $this->validator = $validator;
    }

    /**
     * @param Output $subject
     * @param string|null $result
     * @param \Magento\Catalog\Model\Product $product
     * @param string $attributeHtml
     * @param string $attributeName
     * @return string|null
     */
    public function afterProductAttribute(
        Output $subject,
        $result,
        $product,
        $attributeHtml,
        $attributeName
    ) {
        $isDescription = in_array($attributeName, [self::DESCRIPTION, self::SHORT_DESCRIPTION]);
        if ($this->configProvider->isAmpProductPage() && $result && $isDescription) {
            $result = $this->validator->getValidHtml($result);
        }

        return $result;
    }

    /**
     * @param Output $subject
     * @param string|null $result
     * @param \Magento\Catalog\Model\Category $product
     * @param string $attributeHtml
     * @param string $attributeName
     * @return string|null
     */
    public function afterCategoryAttribute(
        Output $subject,
        $result,
        $product,
        $attributeHtml,
        $attributeName
    ) {
        $isDescription = $attributeName == self::DESCRIPTION;
        if ($this->configProvider->isAmpCategoryPage() && $result && $isDescription) {
            $result = $this->validator->getValidHtml($result);
        }

        return $result;
    }
}
