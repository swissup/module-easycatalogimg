<?php

namespace Swissup\Easycatalogimg\Observer\Backend;

class SetCategoryThumbnail implements \Magento\Framework\Event\ObserverInterface
{
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
                if (isset($thumbnail[0]['name']) && isset($thumbnail[0]['tmp_name'])) {
                    $category->setThumbnail($thumbnail[0]['name']);
                } else {
                    $category->unsThumbnail();
                }
            }
        }
    }
}
