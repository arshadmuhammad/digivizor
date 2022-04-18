<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/

declare(strict_types=1);

namespace Amasty\CheckoutCore\Model\Field\Form;

use Amasty\CheckoutCore\Model\Field\Form\Processor\ProcessorInterface;
use Amasty\CheckoutCore\Model\ResourceModel\Field as FieldResource;

class SaveHandler
{
    /**
     * @var FieldResource
     */
    private $fieldResource;

    /**
     * @var ProcessorInterface[]
     */
    private $processors;

    public function __construct(
        FieldResource $fieldResource,
        array $processors = []
    ) {
        $this->fieldResource = $fieldResource;
        $this->processors = $processors;
    }

    /**
     * @param array<int, array> $fields
     * @param int $storeId
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function execute(array $fields, int $storeId): void
    {
        if (empty($this->processors) || empty($fields)) {
            return;
        }

        $this->validateProcessors();

        try {
            $this->fieldResource->beginTransaction();

            foreach ($this->processors as $processor) {
                $fields = $processor->process($fields, $storeId);
            }

            $this->fieldResource->getConnection()->commit();
        } catch (\Exception $e) {
            $this->fieldResource->rollBack();
            throw $e;
        }
    }

    /**
     * @throws \InvalidArgumentException
     * @SuppressWarnings(PHPMD.MissingImport)
     */
    private function validateProcessors(): void
    {
        foreach ($this->processors as $processor) {
            if (!$processor instanceof ProcessorInterface) {
                throw new \InvalidArgumentException(
                    sprintf('Processor must implement %s', ProcessorInterface::class)
                );
            }
        }
    }
}
