<?php
declare(strict_types=1);

namespace Amasty\PageSpeedOptimizer\Model\Bundle;

use Magento\Framework\Model\AbstractModel;

class Bundle extends AbstractModel
{
    const BUNDLE_FILE_ID = 'filename_id';
    const FILENAME = 'filename';
    const AREA = 'area';
    const THEME = 'theme';
    const LOCALE = 'locale';

    public function _construct()
    {
        parent::_construct();
        $this->_init(\Amasty\PageSpeedOptimizer\Model\Bundle\ResourceModel\Bundle::class);
        $this->setIdFieldName(self::BUNDLE_FILE_ID);
    }

    public function getFilenameId(): ?int
    {
        return $this->hasData(self::BUNDLE_FILE_ID) ? (int)$this->_getData(self::BUNDLE_FILE_ID) : null;
    }

    public function setFilenameId(?int $filenameId): self
    {
        return $this->setData(self::BUNDLE_FILE_ID, $filenameId);
    }

    public function getFilename(): ?string
    {
        return $this->hasData(self::FILENAME) ? (string)$this->_getData(self::FILENAME) : null;
    }

    public function setFilename(?string $filename): self
    {
        return $this->setData(self::FILENAME, $filename);
    }

    public function getArea(): ?string
    {
        return $this->hasData(self::AREA) ? $this->_getData(self::AREA) : null;
    }

    public function setArea(?string $area): self
    {
        return $this->setData(self::AREA, $area);
    }

    public function getTheme(): ?string
    {
        return $this->hasData(self::THEME) ? $this->_getData(self::THEME) : null;
    }

    public function setTheme(?string $theme): self
    {
        return $this->setData(self::THEME, $theme);
    }

    public function getLocale(): ?string
    {
        return $this->_getData(self::LOCALE);
    }

    public function setLocale($locale): self
    {
        return $this->setData(self::LOCALE, $locale);
    }
}
