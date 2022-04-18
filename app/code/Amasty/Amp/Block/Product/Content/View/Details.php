<?php

namespace Amasty\Amp\Block\Product\Content\View;

class Details extends \Magento\Framework\View\Element\Template
{
    /**
     * @param string $groupName
     * @param string $callback
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getGroupSortedChildNames(string $groupName, string $callback): array
    {
        $group = $this->parentGetGroupSortedChildNames($groupName, $callback);
        $layout = $this->getLayout();
        $result = [];
        foreach ($group as $key => $name) {
            $html = $layout->renderElement($name);
            if (!trim($html)) {
                unset($group[$key]);
                continue;
            }
            $result[] = ['name' => $name, 'html' => $html];
        }

        return $result;
    }

    /**
     * Get sorted child block names. Compatibility with magento 2.3.0
     *
     * @param string $groupName
     * @param string $callback
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @return array
     */
    public function parentGetGroupSortedChildNames(string $groupName, string $callback)
    {
        $groupChildNames = $this->getGroupChildNames($groupName, $callback);
        $layout = $this->getLayout();

        $childNamesSortOrder = [];

        foreach ($groupChildNames as $childName) {
            $alias = $layout->getElementAlias($childName);
            $sortOrder = (int)$this->getChildData($alias, 'sort_order') ?? 0;

            $childNamesSortOrder[$sortOrder] = $childName;
        }

        ksort($childNamesSortOrder, SORT_NUMERIC);

        return $childNamesSortOrder;
    }
}
