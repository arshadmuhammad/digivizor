<?php

declare(strict_types=1);

namespace Amasty\Amp\Plugin\Cms\Controller\Page;

use Amasty\Amp\Plugin\AmpRedirect;

class ViewPlugin extends AmpRedirect
{
    /**
     * @return bool
     */
    protected function isNeedRedirect(): bool
    {
        return parent::isNeedRedirect() && $this->configProvider->isCmsEnabled();
    }
}
