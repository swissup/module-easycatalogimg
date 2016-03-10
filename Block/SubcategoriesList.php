<?php
namespace Swissup\Easycatalogimg\Block;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use \Magento\Framework\UrlInterface;
use \Magento\Framework\App\Filesystem\DirectoryList;
use \Swissup\Easycatalogimg\Model\Config\Backend\Image\Placeholder;

class SubcategoriesList extends \Magento\Framework\View\Element\Template implements
    \Magento\Framework\DataObject\IdentityInterface,
    \Magento\Widget\Block\BlockInterface
{
    /**
     * Get extension configuration helper
     * @var \Swissup\Easycatalogimg\Helper\Config
     */
    public $configHelper;
    /**
     * Get extension image helper
     * @var \Swissup\Easycatalogimg\Helper\Image
     */
    public $imageHelper;
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;
    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $catalogLayer;
    /**
     * @var \Magento\Framework\File\Mime
     */
    private $mime;
    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Swissup\Easycatalogimg\Helper\Config $configHelper
     * @param \Magento\Framework\Registry $registry
     * @param CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Swissup\Easycatalogimg\Helper\Image $imageHelper
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Magento\Framework\File\Mime $mime
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Swissup\Easycatalogimg\Helper\Config $configHelper,
        \Magento\Framework\Registry $registry,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Swissup\Easycatalogimg\Helper\Image $imageHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Framework\File\Mime $mime,
        array $data = []
    ) {
        $this->configHelper = $configHelper;
        $this->coreRegistry = $registry;
        $this->categoryRepository = $categoryRepository;
        $this->storeManager = $storeManager;
        $this->imageHelper = $imageHelper;
        $this->catalogLayer = $layerResolver->get();
        $this->mime = $mime;
        parent::__construct($context, $data);
    }
    public function _construct()
    {
        if ($this->getUseDataFromConfig()) {
            $this->setDataFromConfig();
        }
        return parent::_construct();
    }
    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return ['easycatalogimg_subcategories_list'];
    }
    /**
     * Set config from Stores > Configuration
     */
    private function setDataFromConfig()
    {
        $config = $this->configHelper->getBlockConfig();
        foreach ($config as $key => $value) {
             $this->setData($key, $value);
        }
    }
    /**
    * Opimized method, to get all categories to show
    *
    * @return array
    * <pre>
    * [
    *  \Magento\Catalog\Model\Category => {
    *      children => [
    *          \Magento\Catalog\Model\Category => {...}
    *      ]
    *  }
    *  \Magento\Catalog\Model\Category => {
    *      children => []
    *  }
    *  ...
    * ]
    * </pre>
    */
    public function getCategories()
    {
        if ($category = $this->getCurrentCategory()) {
            $currentLevel = $category->getLevel();
        } else {
            $category = $this->categoryRepository->get($this->getCurrentStore()->getRootCategoryId());
            $currentLevel = 1;
        }
        $collection = $category->getCollection();
        if ($category->getId()) {
            $collection->addPathsFilter($category->getPath() . '/');
        }
        $collection
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('image')
            ->addAttributeToSelect('thumbnail')
            ->addAttributeToSelect('is_anchor')
            ->addAttributeToFilter('is_active', 1)
            ->addUrlRewriteToResult()
            ->addFieldToFilter('level', ['lteq' => $currentLevel + 2])
            ->addFieldToFilter('level', ['gt'   => $currentLevel])
            ->setOrder('level', \Magento\Framework\Data\Collection::SORT_ORDER_ASC)
            ->setOrder('position', \Magento\Framework\Data\Collection::SORT_ORDER_ASC)
            ->load();
        // the next loops is working for two levels only
        if ($categoriesToShow = $this->getCategoryToShow()) {
            $categoriesToShow = explode(',', $categoriesToShow);
        } else {
            $categoriesToShow = [];
        }
        if ($categoriesToHide = $this->getCategoryToHide()) {
            $categoriesToHide = explode(',', $categoriesToHide);
        } else {
            $categoriesToHide = [];
        }
        $result        = [];
        $subcategories = [];
        foreach ($collection as $category) {
            if (in_array($category->getId(), $categoriesToHide)) {
                continue;
            }
            if ($categoriesToShow
                && !in_array($category->getId(), $categoriesToShow)
                && !in_array($category->getParentId(), $categoriesToShow)) {
                continue;
            }
            if ($category->getLevel() == ($currentLevel + 1)) {
                $result[$category->getId()] = $category;
            } else {
                $subcategories[$category->getParentId()][] = $category;
            }
        }
        foreach ($subcategories as $parentId => $_subcategories) {
            if (!isset($result[$parentId])) { // inactive parent category
                continue;
            }
            $parent = $result[$parentId];
            $parent->setSubcategories($_subcategories);
        }
        return $result;
    }
    /**
     * Retrieve current category model object
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function getCurrentCategory()
    {
        if ($categoryId = $this->getCategoryId()) {
            return $this->categoryRepository->get($categoryId);
        }
        if (!$this->hasData('current_category')) {
            $this->setData('current_category', $this->coreRegistry->registry('current_category'));
        }
        return $this->getData('current_category');
    }
    /**
     * @return int
     */
    public function getCategoryId()
    {
        $id = $this->_getData('category_id');
        if (null !== $id && strstr($id, 'category/')) { // category id from widget
            $id = str_replace('category/', '', $id);
        }
        return $id;
    }
    /**
     * Retrieve current store model
     *
     * @return \Magento\Store\Model\Store
     */
    public function getCurrentStore()
    {
        return $this->storeManager->getStore();
    }
    /**
     * Get category thumbnail, image or placeholder url
     * @param String $type get url or path
     * @param \Magento\Catalog\Model\Category $category
     * @return String image url
     */
    public function getImage($category, $type)
    {
        $url = false;
        if ($type == 'url') {
            $prefix = $this->imageHelper->getBaseUrl();
        } elseif ($type == 'path') {
            $prefix = $this->imageHelper->getBaseDir();
        }
        if ($image = $category->getThumbnail()) {
            $url = $prefix . $image;
        } elseif ($this->getUseImageAttribute() && $image = $category->getImage()) {
            $url = $prefix . $image;
        } else {
            $url = $this->getImagePlaceholder($type);
        }
        return $url;
    }
    /**
     * Get category image placeholder
     * @param String $type get url or path
     * @return String image url
     */
    public function getImagePlaceholder($type)
    {
        if ($type == 'url') {
            $prefix = $this->imageHelper
            ->getBaseUrl(Placeholder::UPLOAD_DIR);
        } elseif ($type == 'path') {
            $prefix = $this->imageHelper
            ->getBaseDir(Placeholder::UPLOAD_DIR);
        }
        $url = $this->configHelper->getPlaceholderImage();
        if ($url) {
            $url = $prefix . '/' . $url;
        } else {
            $url = $this->getViewFileUrl('Swissup_Easycatalogimg::images/placeholder.svg');
            if ($type == 'path') {
                $staticUrl = $this->imageHelper->getBaseUrl('', UrlInterface::URL_TYPE_STATIC);
                $staticDir = $this->imageHelper->getBaseDir('', DirectoryList::STATIC_VIEW);
                $url = str_replace($staticUrl, $staticDir, $url);
            }
        }
        return $url;
    }
     /**
     * Get relevant path to template
     * don't show the block:
     * if pagination is used
     * if filter is applied
     *
     * @return string
     */
    public function getTemplate()
    {
        $page = (int) $this->getRequest()->getParam('p', 1);
        if ($this->getHideWhenFilterIsUsed() &&
            ($page > 1 || count($this->catalogLayer->getState()->getFilters()))
        ) {
            return '';
        }
        $category = $this->getCurrentCategory();
        if ($category && $category->getLevel() > 1) {
            $isAnchor          = $category->getIsAnchor();
            $enabledForAnchor  = $this->getEnabledForAnchor();
            $enabledForDefault = $this->getEnabledForDefault();
            if (($isAnchor && !$enabledForAnchor)
                || (!$isAnchor && !$enabledForDefault)) {
                return '';
            }
        }
        $template = parent::getTemplate();
        if (!$template) {
            $template = $this->_getData('template');
        }
        return $template;
    }
    /**
     * Check if image is svg
     * @param  String $filepath path to file
     * @return Boolean
     */
    public function isSvg($filepath)
    {
        return $this->mime->getMimeType($filepath) == "image/svg+xml";
    }
}
