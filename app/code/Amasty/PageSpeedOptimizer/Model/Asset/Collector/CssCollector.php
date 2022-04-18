<?php

declare(strict_types=1);

namespace Amasty\PageSpeedOptimizer\Model\Asset\Collector;

class CssCollector extends AbstractAssetCollector
{
    const REGEX = '/<link[^>]*href\s*=\s*["|\'](?<asset_url>[^"\']*\.css[^"\']*)["\']+[^>]*>/is';

    public function getAssetContentType(): string
    {
        return 'style';
    }
}
