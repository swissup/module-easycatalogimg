<?php

namespace Swissup\Easycatalogimg\Controller\Departments;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Swissup\Easycatalogimg\Helper\Config;

class View extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Config
     */
    protected $configHelper;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Config $configHelper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Config $configHelper
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->configHelper = $configHelper;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(
            __($this->configHelper->getDepartmentsTitle())
        );
        return $resultPage;
    }
}
