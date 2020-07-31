<?php

namespace Swissup\Easycatalogimg\Observer\Backend;

class SetCategoryThumbnail implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var string
     */
    private $additionalData = '_additional_data_';


    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $category = $observer->getCategory();
        $thumbnail = $observer->getRequest()->getPostValue('thumbnail');

        if (!$thumbnail) {
            $category->setThumbnail(null);
        } else if (is_array($thumbnail)) {
            if (!empty($thumbnail['delete'])) {
                $category->setThumbnail(null);
            } else {
                if (isset($thumbnail[0]['name'])) {
                    $category->setThumbnail($thumbnail);
                    // code below added to execute method afterSave in class
                    // \Magento\Catalog\Model\Category\Attribute\Backend\Image
                    $category->setData($this->additionalData . 'thumbnail', $thumbnail);
                } else {
                    $category->unsThumbnail();
                }
            }
        }
    }
}
