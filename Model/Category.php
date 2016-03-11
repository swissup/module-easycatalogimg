<?php
namespace Swissup\Easycatalogimg\Model;
/**
 * This class is used during automated image assignment.
 * All callbacks and events are disabled to speedup the thumbnail save process.
 */
class Category extends \Magento\Catalog\Model\Category
{
    const CACHE_TAG             = 'catalog_category_easycatalogimg_disable';
    protected $_eventPrefix     = 'catalog_category_easycatalogimg_disable';
    protected function _construct()
    {
        $this->_init('Magento\Catalog\Model\ResourceModel\Category');
    }
    public function validate()
    {
        return true;
    }
    public function afterCommitCallback()
    {
        return $this;
    }
    protected function _afterSaveCommit()
    {
        return $this;
    }
    protected function _beforeSave()
    {
        return $this;
    }
    protected function _afterSave()
    {
        return $this;
    }
}
