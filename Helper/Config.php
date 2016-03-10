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
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        parent::__construct($context);
        $this->_scopeConfig = $scopeConfig;
    }
    protected function _getConfig($key)
    {
        return $this->_scopeConfig->getValue($key, ScopeInterface::SCOPE_STORE);
    }
    public function useImageAttribute()
    {
        return (bool)$this->_getConfig(self::XML_PATH_USE_IMAGE_ATTRIBUTE);
    }
    public function useImageResizeHelper()
    {
        return (bool)$this->_getConfig(self::XML_PATH_USE_RESIZE_HELPER);
    }
    public function getBackgroundColor()
    {
        return (String)$this->_getConfig(self::XML_PATH_BACKGROUND_COLOR);
    }
    public function getPlaceholderImage()
    {
        return (String)$this->_getConfig(self::XML_PATH_PLACEHOLDER);
    }
    public function isEnabledForDefault()
    {
        return (bool)$this->_getConfig(self::XML_PATH_ENABLED_FOR_DEFAULT);
    }
    public function isEnabledForAnchor()
    {
        return (bool)$this->_getConfig(self::XML_PATH_ENABLED_FOR_ANCHOR);
    }
    public function hideWhenFilterUsed()
    {
        return (bool)$this->_getConfig(self::XML_PATH_HIDE_WHEN_FILTER_IS_USED);
    }
    public function getCategoryCount()
    {
        return (int)$this->_getConfig(self::XML_PATH_CATEGORY_COUNT);
    }
    public function getColumnCount()
    {
        return (int)$this->_getConfig(self::XML_PATH_COLUMN_COUNT);
    }
    public function getShowImages()
    {
        return (bool)$this->_getConfig(self::XML_PATH_SHOW_IMAGE);
    }
    public function getImageWidth()
    {
        return (int)$this->_getConfig(self::XML_PATH_IMAGE_WIDTH);
    }
    public function getImageHeight()
    {
        return (int)$this->_getConfig(self::XML_PATH_IMAGE_HEIGHT);
    }
    public function getSubcategoryCount()
    {
        return (int)$this->_getConfig(self::XML_PATH_SUBCATEGORY_COUNT);
    }
    public function getBlockConfig()
    {
        $config = $this->_getConfig(self::XML_PATH_CATEGORY_CONFIG);
        $config['use_image_attribute'] = $this->useImageAttribute();
        $config['resize_image'] = $this->useImageResizeHelper();
        return $config;
    }
}
