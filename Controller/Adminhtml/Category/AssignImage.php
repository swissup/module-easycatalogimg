<?php
namespace Swissup\Easycatalogimg\Controller\Adminhtml\Category;

use Magento\Backend\App\Action\Context;

class AssignImage extends \Magento\Backend\App\Action
{
    const PAGE_SIZE = 20;
    /**
     * Json encoder
     *
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;
    /**
     * Get extension image helper
     * @var \Swissup\Easycatalogimg\Helper\Image
     */
    protected $imageHelper;
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;
    /**
     * @param Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Swissup\Easycatalogimg\Helper\Image $imageHelper
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Swissup\Easycatalogimg\Helper\Image $imageHelper,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        $this->jsonEncoder = $jsonEncoder;
        $this->imageHelper = $imageHelper;
        $this->categoryFactory = $categoryFactory;
        $this->storeManager = $storeManager;
        $this->productFactory = $productFactory;
        parent::__construct($context);
    }
    /**
     * Assign images action
     *
     */
    public function execute()
    {
        $fillThumbnails = $this->getRequest()->getParam('thumbnail');
        if (!$fillThumbnails) {
            return $this->getResponse()->setBody(
                $this->jsonEncoder->encode(array(
                    'error' => __('Please select the checkbox above')
                ))
            );
        }

        $categoryDir = $this->imageHelper->getBaseDir();
        if (!file_exists($categoryDir)) {
            mkdir($categoryDir, 0777, true);
        }
        if (!is_writable($categoryDir)) {
            return $this->getResponse()->setBody(
                $this->jsonEncoder->encode(array(
                    'error' => __('%1 is not writable', $categoryDir)
                ))
            );
        }

        $lastProcessed = $this->getRequest()->getParam('last_processed', 0);
        $pageSize      = $this->getRequest()->getParam('page_size', self::PAGE_SIZE);
        $categories    = $this->categoryFactory->create()->getCollection()
            ->setItemObjectClass('Swissup\Easycatalogimg\Model\Category')
            ->addAttributeToSelect('thumbnail', true)
            ->addAttributeToFilter('entity_id', ['gt' => $lastProcessed])
            ->addAttributeToFilter('level', ['gt' => 0])
            ->addAttributeToFilter('thumbnail', [ ['null' => 1], ['eq' => ''] ])
            ->setOrder('entity_id')
            ->setPageSize($pageSize)
            ->setCurPage(1);

        $storeGroups = $this->storeManager->getGroups(true);
        $searchInChildCategoriesFlag = $this->getRequest()->getParam('search_in_child_categories');
        foreach ($categories as $category) {
            $storeGroup = false;
            if ($searchInChildCategoriesFlag) {
                $category->load($category->getId());
                $pathIds = $category->getPathIds();
                if (count($pathIds) > 1) {
                    foreach ($storeGroups as $group) {
                        if ($group->getRootCategoryId() != $pathIds[1]) { // 0 element - is global root
                            continue;
                        }
                        $storeGroup = $group;
                        break;
                    }
                }
            }
            if ($storeGroup) {
                $products = $this->productFactory->create()
            ->getCollection()
                    ->setStoreId($storeGroup->getDefaultStoreId())
                    ->addCategoryFilter($category);
            } else {
                $products = $category->getProductCollection();
            }
            $products->addAttributeToSelect('image')
                ->addAttributeToFilter('image', ['notnull' => 1])
                ->addAttributeToFilter('image', ['neq' => ''])
                ->addAttributeToFilter('image', ['neq' => 'no_selection'])
                ->setOrder('entity_id', 'asc')
                ->setPage(1, 1);
            $product = $products->getFirstItem();
            if (!$product || !$product->getId()) {
                continue;
            }
            $image       = trim($product->getImage(), '/');
            $source      = $this->imageHelper->getBaseDir('catalog/product/') . $image;
            $destination = $categoryDir . '/' . $image;
            if (file_exists($source) && !file_exists($destination)) {
                $pathinfo = pathinfo($destination);
                if (!is_writable($pathinfo['dirname']) && !mkdir($pathinfo['dirname'], 0777, true)) {
                    continue;
                }
                copy($source, $destination);
            }
            if (file_exists($destination)) {
                $category->setThumbnail($image)->save();
            }
        }

        $processed = $this->getRequest()->getParam('processed', 0) + count($categories);
        $finished  = (int)(count($categories) < $pageSize);
        $this->getResponse()->setBody(
            $this->jsonEncoder->encode(array(
                'finished'  => $finished,
                'processed' => $processed,
                'last_processed' => $categories->getLastItem()->getId()
            ))
        );
    }
}
