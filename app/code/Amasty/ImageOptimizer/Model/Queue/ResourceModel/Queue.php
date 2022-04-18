<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Model\Queue\ResourceModel;

use Amasty\ImageOptimizer\Model\Queue\Queue as QueueModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Queue extends AbstractDb
{
    const TABLE_NAME = 'amasty_page_speed_optimizer_queue';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, QueueModel::QUEUE_ID);
    }

    public function clear(array $queueTypes): void
    {
        $this->getConnection()->delete($this->getMainTable(), [QueueModel::QUEUE_TYPE . ' IN (?)' => $queueTypes]);
    }

    public function deleteByIds(array $ids = []): void
    {
        $this->getConnection()->delete($this->getMainTable(), [QueueModel::QUEUE_ID . ' in (?) ' => $ids]);
    }

    public function deleteByFilename(string $filename): void
    {
        $this->getConnection()->delete($this->getMainTable(), [QueueModel::FILENAME . ' = ?' => $filename]);
    }
}
