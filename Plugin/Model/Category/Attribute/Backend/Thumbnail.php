<?php
namespace Swissup\Easycatalogimg\Plugin\Model\Category\Attribute\Backend;

class Thumbnail
{
    /**
     * Fix to correctly delete custom category thumbnail attribute value
     *
     * @param \Magento\Catalog\Model\Category\Attribute\Backend\Image $subject
     * @param \Magento\Framework\DataObject $object
     * @return \Magento\Catalog\Model\Category\Attribute\Backend\Image
     */
    public function beforeAfterSave(
        \Magento\Catalog\Model\Category\Attribute\Backend\Image $subject,
        $object
    )
    {
        $attrName = $subject->getAttribute()->getName();
        if ($attrName == 'thumbnail') {
            $value = $object->getData($attrName);
            $object->setData($attrName . '_additional_data', $value);
        }

        return [$object];
    }
}
