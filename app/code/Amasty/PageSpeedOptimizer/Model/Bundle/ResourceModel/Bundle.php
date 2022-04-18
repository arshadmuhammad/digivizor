<?php
declare(strict_types=1);

namespace Amasty\PageSpeedOptimizer\Model\Bundle\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Bundle extends AbstractDb
{
    const TABLE_NAME = 'amasty_page_speed_optimizer_bundle';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, \Amasty\PageSpeedOptimizer\Model\Bundle\Bundle::BUNDLE_FILE_ID);
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function clear(): void
    {
        $this->getConnection()->truncateTable($this->getMainTable());
    }
}
