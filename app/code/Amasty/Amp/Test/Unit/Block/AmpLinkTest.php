<?php

namespace Amasty\Amp\Test\Unit\Block;

use Amasty\Amp\Test\Unit\Traits;
use Amasty\Amp\Block\AmpLink;

/**
 * Class AmpLinkTest
 *
 * @see AmpLink
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class AmpLinkTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @var AmpLink
     */
    private $block;

    /**
     * @covers AmpLink::getAmpLink
     *
     * @throws \ReflectionException
     */
    public function testGetAmpLink()
    {
        $configProvider = $this->createMock(\Amasty\Amp\Model\ConfigProvider::class);
        $request = $this->createMock(\Magento\Framework\App\Request\Http::class);
        $this->block = $this->getObjectManager()->getObject(AmpLink::class, ['config' => $configProvider]);
        $this->block->setNameInLayout('am_amp_canonical_product');

        $configProvider->expects($this->any())->method('isProductEnabled')->willReturnOnConsecutiveCalls(false, true);
        $configProvider->expects($this->any())->method('isCategoryEnabled')->willReturnOnConsecutiveCalls(false, true);
        $configProvider->expects($this->any())->method('isValidCategory')->willReturnOnConsecutiveCalls(true, true);
        $request->expects($this->any())->method('getOriginalPathInfo')->willReturn('/origPath/');

        $this->setProperty($this->block, '_baseUrl', '/test/', AmpLink::class);
        $this->setProperty($this->block, '_request', $request, AmpLink::class);

        $this->assertEquals('', $this->block->getAmpLink());
        $this->assertEquals('/test/amp/origPath/', $this->block->getAmpLink());

        $this->block->setNameInLayout('am_amp_canonical_category');
        $this->assertEquals('', $this->block->getAmpLink());
        $this->assertEquals('/test/amp/origPath/', $this->block->getAmpLink());
    }
}
