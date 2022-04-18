<?php

namespace Amasty\Amp\Plugin\Config\Model\Config\Backend;

class ImagePlugin
{
    /**
     * @var \Magento\Framework\File\Mime
     */
    private $fileMime;

    public function __construct(
        \Magento\Framework\File\Mime $fileMime
    ) {
        $this->fileMime = $fileMime;
    }

    /**
     * improve magento security
     * @param $uploader
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeBeforeSave($uploader)
    {
        $value = $uploader->getValue();
        $tmpName = $value['tmp_name'] ?? '';
        if ($tmpName) {
            $isValidType = in_array(
                $this->fileMime->getMimeType($tmpName),
                ['image/jpg', 'image/jpeg', 'image/gif', 'image/png']
            );

            if (!$isValidType) {
                throw new \Magento\Framework\Exception\LocalizedException(__('File validation failed.'));
            }
        }
    }
}
