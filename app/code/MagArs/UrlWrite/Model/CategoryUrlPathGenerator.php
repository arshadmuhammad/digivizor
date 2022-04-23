<?php

namespace MagArs\UrlWrite\Model;

use Magento\Catalog\Model\Category;
use Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator as Magento_CategoryUrlPathGenerator;

class CategoryUrlPathGenerator extends Magento_CategoryUrlPathGenerator {

    public function getUrlPath($category, $parentCategory = null)
    {
        if (in_array($category->getParentId(), [Category::ROOT_CATEGORY_ID, Category::TREE_ROOT_ID])) {
            return '';
        }
        $path = $category->getUrlPath();
        if ($path !== null && !$category->dataHasChangedFor('url_key') && !$category->dataHasChangedFor('parent_id')) {
            return $path;
        }
        $path = $category->getUrlKey();
        if ($path === false) {
            return $category->getUrlPath();
        }

        return $path;
    }
}
