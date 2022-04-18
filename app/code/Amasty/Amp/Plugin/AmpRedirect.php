<?php

declare(strict_types=1);

namespace Amasty\Amp\Plugin;

use Amasty\Amp\Model\Detection\MobileDetect;
use Amasty\Amp\Model\ConfigProvider;
use Amasty\Amp\Model\UrlConfigProvider;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Controller\AbstractResult;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

abstract class AmpRedirect
{
    /**
     * @var RedirectInterface
     */
    protected $redirect;

    /**
     * @var ConfigProvider
     */
    protected $configProvider;

    /**
     * @var UrlConfigProvider
     */
    protected $urlConfigProvider;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var RedirectFactory
     */
    protected $redirectFactory;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var MobileDetect
     */
    private $mobileDetect;

    public function __construct(
        RedirectInterface $redirect,
        ConfigProvider $configProvider,
        UrlConfigProvider $urlConfigProvider,
        UrlInterface $urlBuilder,
        RedirectFactory $redirectFactory,
        CategoryRepositoryInterface $categoryRepository,
        StoreManagerInterface $storeManager,
        RequestInterface $request,
        MobileDetect $mobileDetect
    ) {
        $this->redirect = $redirect;
        $this->configProvider = $configProvider;
        $this->urlConfigProvider = $urlConfigProvider;
        $this->urlBuilder = $urlBuilder;
        $this->redirectFactory = $redirectFactory;
        $this->categoryRepository = $categoryRepository;
        $this->storeManager = $storeManager;
        $this->request = $request;
        $this->mobileDetect = $mobileDetect;
    }

    /**
     * @param Action $controller
     * @param callable $proceed
     * @return AbstractResult | null
     */
    public function aroundExecute(Action $controller, callable $proceed): ?AbstractResult
    {
        if ($this->isNeedRedirect()) {
            $redirect = $this->redirectFactory->create();
            $result = $redirect->setPath($this->urlConfigProvider->convertToAmpUrl($this->urlBuilder->getCurrentUrl()));
        } else {
            $result = $proceed();
        }

        return $result;
    }

    /**
     * @return bool
     */
    protected function isNeedRedirect(): bool
    {
        return $this->mobileDetect->isMobile()
            && $this->configProvider->isRedirectMobile()
            && !$this->configProvider->isAmpUrl();
    }

    /**
     * @return RequestInterface
     */
    protected function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * @return StoreManagerInterface
     */
    protected function getStoreManager(): StoreManagerInterface
    {
        return $this->storeManager;
    }

    /**
     * @return CategoryRepositoryInterface
     */
    protected function getCategoryRepository(): CategoryRepositoryInterface
    {
        return $this->categoryRepository;
    }
}
