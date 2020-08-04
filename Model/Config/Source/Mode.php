<?php

namespace Swissup\Easycatalogimg\Model\Config\Source;

use Swissup\Easycatalogimg\Block\SubcategoriesList;

class Mode implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => SubcategoriesList::MODE_GRID, 'label' => __('Grid')],
            ['value' => SubcategoriesList::MODE_MASONRY, 'label' => __('Masonry')],
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $result = [];
        foreach ($this->toOptionArray() as $item) {
            $result[$item['value']] = $item['label'];
        }
        return $result;
    }
}
