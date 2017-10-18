<?php
namespace Swissup\Easycatalogimg\Setup;

use Magento\Catalog\Model\Category;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Setup\CategorySetupFactory;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var CategorySetupFactory
     */
    protected $categorySetupFactory;
    /**
     * UpgradeData constructor
     *
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(CategorySetupFactory $categorySetupFactory)
    {
        $this->categorySetupFactory = $categorySetupFactory;
    }
    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        /** @var \Magento\Catalog\Setup\CategorySetup $categorySetup */
        $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
        $entityTypeId = $categorySetup->getEntityTypeId(Category::ENTITY);
        $attribute = $categorySetup->getAttribute($entityTypeId, 'thumbnail');
        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $categorySetup->updateAttribute(
                $entityTypeId,
                $attribute['attribute_id'],
                'backend_model',
                'Magento\Catalog\Model\Category\Attribute\Backend\Image',
                6
            );
        }
        $setup->endSetup();
    }
}
