<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizerUi\Model\Image\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init(
            \Amasty\ImageOptimizerUi\Model\Image\ImageSetting::class,
            \Amasty\ImageOptimizerUi\Model\Image\ResourceModel\ImageSetting::class
        );
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }
}
