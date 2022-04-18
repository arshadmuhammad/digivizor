<?php
declare(strict_types=1);

namespace Amasty\PageSpeedOptimizer\Setup\Patch\Data;

use Amasty\ImageOptimizer\Model\Queue\ResourceModel\Queue;
use Amasty\ImageOptimizerUi\Model\Image\ResourceModel\ImageSetting;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class UpdateTo190 implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        ResourceConnection $resourceConnection
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->resourceConnection = $resourceConnection;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function apply(): void
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $connection = $this->resourceConnection->getConnection();
        $coreTable = $this->resourceConnection->getTableName('core_config_data');
        $connection->update(
            $coreTable,
            ['value' => 'jpegoptim100'],
            ['path = ?' => 'amoptimizer/images/jpeg_tool', 'value = ?' => '1']
        );
        $connection->update(
            $coreTable,
            ['value' => 'jpegoptim90'],
            ['path = ?' => 'amoptimizer/images/jpeg_tool', 'value = ?' => '998']
        );
        $connection->update(
            $coreTable,
            ['value' => 'jpegoptim80'],
            ['path = ?' => 'amoptimizer/images/jpeg_tool', 'value = ?' => '999']
        );
        $connection->update(
            $coreTable,
            ['value' => 'optipng'],
            ['path = ?' => 'amoptimizer/images/png_tool', 'value = ?' => '1']
        );
        $connection->update(
            $coreTable,
            ['value' => 'gifsicle'],
            ['path = ?' => 'amoptimizer/images/gif_tool', 'value = ?' => '1']
        );
        $connection->update(
            $coreTable,
            ['value' => 'cwebp'],
            ['path = ?' => 'amoptimizer/images/webp', 'value = ?' => '1']
        );

        $connection->update(
            $coreTable,
            ['value' => 'jquery'],
            ['path = ?' => 'amoptimizer/images/lazy_load_script', 'value = ?' => '0']
        );
        $connection->update(
            $coreTable,
            ['value' => 'native'],
            ['path = ?' => 'amoptimizer/images/lazy_load_script', 'value = ?' => '1']
        );
        foreach (['general', 'home', 'products', 'categories', 'cms'] as $postfix) {
            $connection->update(
                $coreTable,
                ['value' => 'jquery'],
                ['path = ?' => 'amoptimizer/lazy_load_' . $postfix . '/lazy_load_script', 'value = ?' => '0']
            );
            $connection->update(
                $coreTable,
                ['value' => 'native'],
                ['path = ?' => 'amoptimizer/lazy_load_' . $postfix . '/lazy_load_script', 'value = ?' => '1']
            );
        }
        $connection->delete(
            $this->resourceConnection->getTableName(Queue::TABLE_NAME)
        );

        $imageSettingTable = $this->resourceConnection->getTableName(ImageSetting::TABLE_NAME);
        $connection->update($imageSettingTable, ['jpeg_tool' => 'jpegoptim100'], ['jpeg_tool = ?' => '1']);
        $connection->update($imageSettingTable, ['jpeg_tool' => 'jpegoptim90'], ['jpeg_tool = ?' => '998']);
        $connection->update($imageSettingTable, ['jpeg_tool' => 'jpegoptim80'], ['jpeg_tool = ?' => '999']);
        $connection->update($imageSettingTable, ['png_tool' => 'optipng'], ['png_tool = ?' => '1']);
        $connection->update($imageSettingTable, ['gif_tool' => 'gifsicle'], ['gif_tool = ?' => '1']);
        if ($connection->tableColumnExists($imageSettingTable, 'is_create_webp')) {
            $connection->update($imageSettingTable, ['webp_tool' => 'cwebp'], ['is_create_webp = ?' => '1']);
        }

        $optimizationTypesSelect = $connection->select()
            ->from($coreTable)
            ->where('path = ?', 'amoptimizer/images/image_optimization_type');
        $optimizationTypes = $connection->fetchAll($optimizationTypesSelect);

        foreach (['general', 'home', 'products', 'categories', 'cms'] as $postfix) {
            foreach ($optimizationTypes as $optimizationType) {
                if ($optimizationType['value'] ?? '' === '1') {
                    $connection->insert(
                        $coreTable,
                        [
                            'scope' => $optimizationType['scope'],
                            'scope_id' => $optimizationType['scope_id'],
                            'path' => "amoptimizer/replace_images_$postfix/enable_custom_replace",
                            'value' => '1',
                        ]
                    );
                    $connection->insert(
                        $coreTable,
                        [
                            'scope' => $optimizationType['scope'],
                            'scope_id' => $optimizationType['scope_id'],
                            'path' => "amoptimizer/lazy_load_$postfix/enable_custom_lazyload",
                            'value' => '1',
                        ]
                    );
                }

                $connection->delete(
                    $coreTable,
                    ['config_id = ' . (int)$optimizationType['config_id']]
                );
            }

            foreach (['webp_resolutions', 'webp_resolutions_ignore'] as $configPart) {
                $connection->update(
                    $coreTable,
                    ['path' => "amoptimizer/replace_images_$postfix/$configPart"],
                    ['path = ?' => "amoptimizer/lazy_load_$postfix/$configPart"]
                );
            }
        }

        $connection->update(
            $coreTable,
            ['path' => 'amoptimizer/replace_images_cms/webp_resolutions_ignore'],
            ['path = ?' => 'amoptimizer/images/lazy_advanced/lazy_cms/replace_with_webp_ignore']
        );
        $simpleSettings = [
            'lazy_load',
            'lazy_load_script',
            'preload_images',
            'skip_images_count_desktop',
            'skip_images_count_tablet',
            'skip_images_count_mobile',
            'skip_images_count',
            'preload_images_strategy',
            'ignore_list'
        ];
        $connection->delete(
            $coreTable,
            ['path = "amoptimizer/lazy_load_general/ignore_list" AND value IS NULL']
        );

        foreach ($simpleSettings as $configPart) {
            try {
                $connection->update(
                    $coreTable,
                    ['path' => "amoptimizer/lazy_load_general/$configPart"],
                    ['path = ?' => "amoptimizer/images/$configPart"]
                );
            } catch (\Throwable $e) {
                null;
            }
        }

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public function getAliases(): array
    {
        return [];
    }
}
