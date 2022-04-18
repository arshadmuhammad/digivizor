<?php

namespace Amasty\Amp\Setup\UpgradeData;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Amasty\Amp\Block\Cms\Home\PredefinedContentFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Cms\Helper\Page;
use Magento\Cms\Model\GetPageByIdentifier;
use Magento\Cms\Model\PageRepository;
use Magento\Framework\App\State;
use Psr\Log\LoggerInterface;
use Magento\Store\Model\App\Emulation;
use Magento\Framework\App\Area;

class AddPredefinedHome
{
    /**
     * @var PredefinedContentFactory
     */
    private $predefinedContentFactory;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var GetPageByIdentifier
     */
    private $pageByIdentifier;

    /**
     * @var PageRepository
     */
    private $pageRepository;

    /**
     * @var State
     */
    private $state;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Emulation
     */
    private $appEmulation;

    public function __construct(
        PredefinedContentFactory $predefinedContentFactory,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        GetPageByIdentifier $pageByIdentifier,
        PageRepository $pageRepository,
        LoggerInterface $logger,
        Emulation $appEmulation,
        State $state
    ) {
        $this->predefinedContentFactory = $predefinedContentFactory;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->pageByIdentifier = $pageByIdentifier;
        $this->pageRepository = $pageRepository;
        $this->state = $state;
        $this->logger = $logger;
        $this->appEmulation = $appEmulation;
    }

    public function execute()
    {
        try {
            foreach ($this->storeManager->getStores() as $store) {
                $storeId = $store->getId();
                $homeIdentifier = $this->scopeConfig->getValue(
                    Page::XML_PATH_HOME_PAGE,
                    ScopeInterface::SCOPE_STORE,
                    $storeId
                );
                $homePage = $this->pageByIdentifier->execute($homeIdentifier, $storeId);

                $html = $this->state->emulateAreaCode(Area::AREA_FRONTEND, [$this, 'getContent'], [$storeId]);
                $this->appEmulation->stopEnvironmentEmulation();
                $homePage->setAmpContent($html);
                $this->pageRepository->save($homePage);
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getContent($storeId)
    {
        $this->appEmulation->startEnvironmentEmulation($storeId, Area::AREA_FRONTEND, true);

        return $this->predefinedContentFactory->create()->toHtml();
    }
}
