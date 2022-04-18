<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Plugin\Image;

use Amasty\ImageOptimizer\Model\ImageProcessor\AutoProcessing\ProcessorsProvider;

class AdapterImage
{
    /**
     * @var string
     */
    private $image;

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
     * @param $subject
     * @param $path
     * @param $newFileName
     */
    public function beforeSave($subject, $path = null, $newFileName = null)
    {
        if ($path !== null) {
            if ($newFileName !== null) {
                $this->image = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $newFileName;
            } else {
                $this->image = $path;
            }
        } else {
            $this->image = false;
        }
    }

    /**
     * @param $subject
     * @param $result
     *
     * @return mixed
     */
    public function afterSave($subject, $result)
    {
        if ($this->image) {
            foreach ($this->processorsProvider->getAll() as $processor) {
                $processor->execute($this->image);
            }
        }

        return $result;
    }
}
