<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Block\Adminhtml\Settings;

use Amasty\Base\Model\Serializer;
use Amasty\ImageOptimizer\Model\Image\ImagesExampleProvider;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class JpegOptimizationExample extends Field
{
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var ImagesExampleProvider
     */
    private $imagesExampleProvider;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        Serializer $serializer,
        ImagesExampleProvider $imagesExampleProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->serializer = $serializer;
        $this->imagesExampleProvider = $imagesExampleProvider;
    }

    /**
     * @var string
     */
    protected $_template = 'Amasty_ImageOptimizerUi::jpeg_optimization_example.phtml';

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element): string
    {
        return $this->toHtml();
    }

    protected function _renderScopeLabel(AbstractElement $element): string
    {
        return '';
    }

    public function getImageUrls(): string
    {
        return $this->serializer->serialize($this->imagesExampleProvider->get());
    }
}
