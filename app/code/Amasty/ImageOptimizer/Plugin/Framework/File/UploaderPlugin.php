<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Plugin\Framework\File;

use Amasty\ImageOptimizer\Model\ImageProcessor\AutoProcessing\ProcessorsProvider;
use Magento\Framework\File\Uploader;

class UploaderPlugin
{
    const ALLOWED_IMAGE_MIME_TYPES = [
        'image/jpeg',
        'image/jpg',
        'image/gif',
        'image/png',
    ];

    /**
     * @var ProcessorsProvider
     */
    private $processorsProvider;

    public function __construct(
        ProcessorsProvider $processorsProvider
    ) {
        $this->processorsProvider = $processorsProvider;
    }

    /**
     * @param Uploader $subject
     * @param bool|array $result
     * @return mixed
     */
    public function afterSave(Uploader $subject, $result)
    {
        if (!isset($result['path'])) {
            return $result;
        }

        if ($this->isImageMimeTypeAllowed($result['type'] ?? '')) {
            foreach ($this->processorsProvider->getAll() as $processor) {
                $processor->execute($result['path'] . DIRECTORY_SEPARATOR . $result['file']);
            }
        }

        return $result;
    }

    private function isImageMimeTypeAllowed(string $mimeType): bool
    {
        return in_array($mimeType, self::ALLOWED_IMAGE_MIME_TYPES);
    }
}
