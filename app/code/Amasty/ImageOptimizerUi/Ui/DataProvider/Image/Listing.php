<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizerUi\Ui\DataProvider\Image;

use Amasty\ImageOptimizerUi\Model\Image\ImageSetting;
use Amasty\ImageOptimizerUi\Model\Image\Repository;
use Magento\Ui\DataProvider\AbstractDataProvider;

class Listing extends AbstractDataProvider
{
    /**
     * @var \Amasty\ImageOptimizer\Model\Image\ImageSetting
     */
    private $imageSettingModel;

    public function __construct(
        Repository $repository,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $repository->getImageSettingCollection();
        $this->imageSettingModel = $repository->getEmptyImageSettingModel();

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData(): array
    {
        $data = parent::getData();
        if (!empty($data['totalRecords'])) {
            foreach ($data['items'] as &$item) {
                $item[ImageSetting::FOLDERS] = $this->imageSettingModel
                    ->setData(ImageSetting::FOLDERS, $item[ImageSetting::FOLDERS])
                    ->getFolders();
            }
        }

        return $data;
    }
}
