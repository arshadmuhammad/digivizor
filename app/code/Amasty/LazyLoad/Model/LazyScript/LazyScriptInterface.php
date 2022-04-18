<?php
declare(strict_types=1);

namespace Amasty\LazyLoad\Model\LazyScript;

interface LazyScriptInterface
{
    public function getName(): string;

    public function getType(): string;

    public function getCode(): string;
}
