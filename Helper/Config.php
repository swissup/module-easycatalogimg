<?php
namespace Swissup\Easycatalogimg\Helper;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    /**
     * Path to store config use category image, when thumbnail is not available
     *
     * @var string
     */
    const XML_PATH_USE_IMAGE_ATTRIBUTE = 'easycatalogimg/general/use_image_attribute';

    /**
     * Path to store config use image helper
     *
     * @var string
     */
    const XML_PATH_USE_RESIZE_HELPER = 'easycatalogimg/general/resize_image';

    /**
     * Path to store config background color
     *
     * @var string
     */
    const XML_PATH_BACKGROUND_COLOR = 'easycatalogimg/general/background';

    /**
     * Path to store config image placeholder
     *
     * @var string
     */
    const XML_PATH_PLACEHOLDER = 'easycatalogimg/general/placeholder';

    /**
     * Path to store config enable for default categories
     *
     * @var string
     */
    const XML_PATH_ENABLED_FOR_DEFAULT = 'easycatalogimg/category/enabled_for_default';

    /**
     * Path to store config enabled for anchor categories
     *
     * @var string
     */
    const XML_PATH_ENABLED_FOR_ANCHOR = 'easycatalogimg/category/enabled_for_anchor';

    /**
     * Path to store config hide when filter is used
     *
     * @var string
     */
    const XML_PATH_HIDE_WHEN_FILTER_IS_USED = 'easycatalogimg/category/hide_when_filter_is_used';

    /**
     * Path to store config category count
     *
     * @var string
     */
    const XML_PATH_CATEGORY_COUNT = 'easycatalogimg/category/category_count';

    /**
     * Path to store config columns count
     *
     * @var string
     */
    const XML_PATH_COLUMN_COUNT = 'easycatalogimg/category/column_count';


    /**
     * Path to store config show image
     *
     * @var string
     */
    const XML_PATH_SHOW_IMAGE = 'easycatalogimg/category/show_image';

    /**
     * Path to store config image width
     *
     * @var string
     */
    const XML_PATH_IMAGE_WIDTH = 'easycatalogimg/category/image_width';

    /**
     * Path to store config image height
     *
     * @var string
     */
    const XML_PATH_IMAGE_HEIGHT = 'easycatalogimg/category/image_height';

    /**
     * Path to store config subcategory count
     *
     * @var string
     */
    const XML_PATH_SUBCATEGORY_COUNT = 'easycatalogimg/category/subcategory_count';

    /**
     * Path to full category config section
     *
     * @var string
     */
    const XML_PATH_CATEGORY_CONFIG = 'easycatalogimg/category';

    /**
     * Departments page url
     *
     * @var string
     */
    const XML_PATH_DEPARTMENTS_URL = 'easycatalogimg/departments/url_path';

    /**
     * Departments page url
     *
     * @var string
     */
    const XML_PATH_DEPARTMENTS_TITLE = 'easycatalogimg/departments/page_title';

    /**
     * Get store config value
     *
     * @param  string $key
     * @return mixed
     */
    protected function _getConfig($key)
    {
        return $this->scopeConfig->getValue($key, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return boolean
     */
    public function useImageAttribute()
    {
        return (bool)$this->_getConfig(self::XML_PATH_USE_IMAGE_ATTRIBUTE);
    }

    /**
     * @return boolean
     */
    public function useImageResizeHelper()
    {
        return (bool)$this->_getConfig(self::XML_PATH_USE_RESIZE_HELPER);
    }

    /**
     * @return string
     */
    public function getBackgroundColor()
    {
        return (string)$this->_getConfig(self::XML_PATH_BACKGROUND_COLOR);
    }

    /**
     * @return string
     */
    public function getPlaceholderImage()
    {
        return (string)$this->_getConfig(self::XML_PATH_PLACEHOLDER);
    }

    /**
     * @return boolean
     */
    public function isEnabledForDefault()
    {
        return (bool)$this->_getConfig(self::XML_PATH_ENABLED_FOR_DEFAULT);
    }

    /**
     * @return boolean
     */
    public function isEnabledForAnchor()
    {
        return (bool)$this->_getConfig(self::XML_PATH_ENABLED_FOR_ANCHOR);
    }

    /**
     * @return boolean
     */
    public function hideWhenFilterUsed()
    {
        return (bool)$this->_getConfig(self::XML_PATH_HIDE_WHEN_FILTER_IS_USED);
    }

    /**
     * @return int
     */
    public function getCategoryCount()
    {
        return (int)$this->_getConfig(self::XML_PATH_CATEGORY_COUNT);
    }

    /**
     * @return int
     */
    public function getColumnCount()
    {
        return (int)$this->_getConfig(self::XML_PATH_COLUMN_COUNT);
    }

    /**
     * @return boolean
     */
    public function getShowImages()
    {
        return (bool)$this->_getConfig(self::XML_PATH_SHOW_IMAGE);
    }

    /**
     * @return int
     */
    public function getImageWidth()
    {
        return (int)$this->_getConfig(self::XML_PATH_IMAGE_WIDTH);
    }

    /**
     * @return int
     */
    public function getImageHeight()
    {
        return (int)$this->_getConfig(self::XML_PATH_IMAGE_HEIGHT);
    }

    /**
     * @return int
     */
    public function getSubcategoryCount()
    {
        return (int)$this->_getConfig(self::XML_PATH_SUBCATEGORY_COUNT);
    }

    /**
     * @return array
     */
    public function getBlockConfig($configPath)
    {
        if (!$configPath) {
            $configPath = self::XML_PATH_CATEGORY_CONFIG;
        }

        $config = $this->_getConfig($configPath);
        $config['use_image_attribute'] = $this->useImageAttribute();
        $config['resize_image'] = $this->useImageResizeHelper();

        return $config;
    }

    /**
     * Get URL to the departmetns page
     *
     * @return string
     */
    public function getDepartmentsUrlPath()
    {
        return (string)$this->_getConfig(self::XML_PATH_DEPARTMENTS_URL);
    }

    /**
     * Get departments page title
     *
     * @return string
     */
    public function getDepartmentsTitle()
    {
        return (string)$this->_getConfig(self::XML_PATH_DEPARTMENTS_TITLE);
    }
}
