<?php
namespace Swissup\Easycatalogimg\Block;

class SubcategoriesList extends \Magento\Framework\View\Element\Template implements
    \Magento\Framework\DataObject\IdentityInterface,
    \Magento\Widget\Block\BlockInterface
{
    /**
     * Default template to use for review widget
     */
    const DEFAULT_LIST_TEMPLATE = 'list.phtml';
    /**
     * Get extension configuration helper
     * @var \Swissup\Easycatalogimg\Helper\Config
     */
    public $configHelper;
    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Swissup\Easycatalogimg\Helper\Config $configHelper,
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Swissup\Easycatalogimg\Helper\Config $configHelper,
        array $data = []
    ) {
        $this->configHelper = $configHelper;
        parent::__construct($context, $data);
    }
    public function _construct()
    {
        if (!$this->hasData('template')) {
            $this->setData('template', self::DEFAULT_LIST_TEMPLATE);
        }
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
}
