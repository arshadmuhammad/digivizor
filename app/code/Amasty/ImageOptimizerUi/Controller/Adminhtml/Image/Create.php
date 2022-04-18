<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizerUi\Controller\Adminhtml\Image;

use Amasty\ImageOptimizerUi\Controller\Adminhtml\AbstractImageSettings;

class Create extends AbstractImageSettings
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
