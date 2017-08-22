<?php
namespace Swissup\Easycatalogimg\Block\Widget;

class SubcategoriesList extends \Swissup\Easycatalogimg\Block\SubcategoriesList
{
    public function getHideWhenFilterIsUsed()
    {
        return (bool)$this->_getData('hide_when_filter_is_used');
    }
    public function getEnabledForAnchor()
    {
        return true;
    }
    public function getEnabledForDefault()
    {
        return true;
    }
    public function getResizeImage()
    {
        $resizeImage = $this->_getData('resize_image');

        if (isset($resizeImage)) {
            $resize = (bool)$resizeImage;
        } else {
            $resize = parent::getResizeImage();
        }

        return $resize;
    }
}
