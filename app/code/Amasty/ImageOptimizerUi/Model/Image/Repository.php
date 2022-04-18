<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizerUi\Model\Image;

use Amasty\ImageOptimizer\Api\Data\ImageSettingInterface;
use Amasty\ImageOptimizerUi\Api\ImageSettingRepositoryInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class Repository implements ImageSettingRepositoryInterface
{
    /**
     * @var ImageSettingFactory
     */
    private $imageSettingFactory;

    /**
     * @var ResourceModel\ImageSetting
     */
    private $imageSettingResource;

    /**
     * @var ResourceModel\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var ImageSettingInterface[]
     */
    private $imageSettings;

    public function __construct(
        ImageSettingFactory $imageSettingFactory,
        ResourceModel\ImageSetting $imageSettingResource,
        ResourceModel\CollectionFactory $collectionFactory
    ) {
        $this->imageSettingFactory = $imageSettingFactory;
        $this->imageSettingResource = $imageSettingResource;
        $this->collectionFactory = $collectionFactory;
    }

    public function getById(int $imageSettingId): ImageSettingInterface
    {
        if (!isset($this->imageSettings[$imageSettingId])) {
            $imageSetting = $this->getEmptyImageSettingModel();
            $this->imageSettingResource->load($imageSetting, $imageSettingId);
            if (!$imageSetting->getImageSettingId()) {
                throw new NoSuchEntityException(
                    __('Image Settings with specified ID "%1" not found.', $imageSettingId)
                );
            }
            $this->imageSettings[$imageSettingId] = $imageSetting;
        }

        return $this->imageSettings[$imageSettingId];
    }

    public function save(ImageSettingInterface $imageSetting): ImageSettingInterface
    {
        try {
            if ($imageSetting->getImageSettingId()) {
                $imageSetting = $this->getById($imageSetting->getImageSettingId())->addData($imageSetting->getData());
            }
            $this->imageSettingResource->save($imageSetting);
            unset($this->imageSettings[$imageSetting->getImageSettingId()]);
        } catch (\Exception $e) {
            if ($imageSetting->getImageSettingId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save image setting with ID %1. Error: %2',
                        [$imageSetting->getImageSettingId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new image settings. Error: %1', $e->getMessage()));
        }

        return $imageSetting;
    }

    public function delete(ImageSettingInterface $imageSetting): bool
    {
        try {
            $this->imageSettingResource->delete($imageSetting);
            unset($this->imageSettings[$imageSetting->getImageSettingId()]);
        } catch (\Exception $e) {
            if ($imageSetting->getImageSettingId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove image settings with ID %1. Error: %2',
                        [$imageSetting->getImageSettingId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove image settings. Error: %1', $e->getMessage()));
        }

        return true;
    }

    public function deleteById(int $imageSettingId): bool
    {
        $this->delete($this->getById($imageSettingId));

        return true;
    }

    public function getEmptyImageSettingModel(): ImageSettingInterface
    {
        return $this->imageSettingFactory->create();
    }

    public function getImageSettingCollection(): ResourceModel\Collection
    {
        return $this->collectionFactory->create();
    }
}
