<?php
declare(strict_types=1);

namespace Amasty\PageSpeedTools\Model;

use Amasty\PageSpeedTools\Lib\MobileDetect;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\PageCache\Model\Config;

class DeviceDetect extends MobileDetect
{
    const DESKTOP = 'desktop';
    const TABLET = 'tablet';
    const MOBILE = 'mobile';

    /**
     * @var string
     */
    private $webPBrowsersString = '/(Edge|Firefox|Chrome|Opera)/i';

    /**
     * @var string
     */
    private $deviceType;

    /**
     * @var bool
     */
    private $isWebpSupport;

    /**
     * @var CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        CookieManagerInterface $cookieManager,
        RequestInterface $request,
        array $headers = null,
        $userAgent = null
    ) {
        parent::__construct($headers, $userAgent);
        $this->cookieManager = $cookieManager;
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
    }

    public function getDeviceParams(): array
    {
        if ($this->deviceType === null && $this->isWebpSupport === null) {
            $webHeader = $this->request->getHeader('X-Amasty-Accept-Webp');
            $deviceHeader = $this->request->getHeader('X-Amasty-Device');

            if ($webHeader && $deviceHeader) {
                $this->deviceType = $deviceHeader;
                $this->isWebpSupport = (bool)$webHeader;
            } elseif ($this->scopeConfig->getValue(Config::XML_PAGECACHE_TYPE) == Config::VARNISH
                && !$this->cookieManager->getCookie(Http::COOKIE_VARY_STRING)
            ) {
                /**
                 * Fallback to default device detect behavior when Varnish is not configured properly
                 * and X-Magento-Vary cookie does not exist.
                 */
                $this->deviceType = \Amasty\PageSpeedTools\Model\DeviceDetect::DESKTOP;
                $this->isWebpSupport = false;
            } else {
                $this->deviceType = $this->detectDevice();
                $this->isWebpSupport = $this->detectIsUseWebp();
            }
        }

        return [$this->deviceType, $this->isWebpSupport];
    }

    public function getDeviceType(): string
    {
        [$deviceType] = $this->getDeviceParams();

        return $deviceType;
    }

    public function isUseWebP(): bool
    {
        [, $isWebpSupport] = $this->getDeviceParams();

        return $isWebpSupport;
    }

    protected function detectDevice(): string
    {
        if ($this->isTablet()) {
            return self::TABLET;
        }
        if ($this->isMobile()) {
            return self::MOBILE;
        }

        return self::DESKTOP;
    }

    protected function detectIsUseWebp(): bool
    {
        $userAgent = $this->getUserAgent() ?? '';
        $range14To99 = '(?:1[4-9]|[2-9][0-9])';
        $range11To99 = '(?:1[1-9]|[2-9][0-9])';
        $iphonePart = "\biPhone OS $range14To99";
        $ipadPart = "\biPad; CPU OS $range14To99";
        $macosPart = "\bMac OS X $range11To99";
        $macintoshPart = "\bMacintosh; Intel Mac OS X $range11To99";
        $appleSafariWebpString = "/($iphonePart|$ipadPart|$macintoshPart|$macosPart).*Version\/$range14To99/i";

        return (bool)preg_match($appleSafariWebpString, $userAgent)
            || (bool)preg_match($this->webPBrowsersString, $userAgent);
    }
}
