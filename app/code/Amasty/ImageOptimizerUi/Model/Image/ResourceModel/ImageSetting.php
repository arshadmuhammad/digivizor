<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizerUi\Model\Image\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ImageSetting extends AbstractDb
{
    const TABLE_NAME = 'amasty_page_speed_optimizer_image_setting';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, \Amasty\ImageOptimizerUi\Model\Image\ImageSetting::IMAGE_SETTING_ID);
    }
}
