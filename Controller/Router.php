<?php

namespace Swissup\Easycatalogimg\Controller;

class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * Page view helper
     *
     * @var \Swissup\Easycatalogimg\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Swissup\Firecheckout\Helper\Data $pageViewHelper
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Swissup\Easycatalogimg\Helper\Config $helper
    ) {
        $this->actionFactory = $actionFactory;
        $this->helper = $helper;
    }

    /**
     * Match firecheckout page
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $currentPath = trim($request->getPathInfo(), '/');
        if (strpos($currentPath, 'easycatalogimg') === 0) {
            return null; // use standard router to prevent recursion
        }

        $departmentsPath = $this->helper->getDepartmentsUrlPath();
        if ($currentPath !== $departmentsPath) {
            return null;
        }

        $request->setAlias(
            \Magento\Framework\UrlInterface::REWRITE_REQUEST_PATH_ALIAS,
            $currentPath
        );
        $request->setPathInfo('/easycatalogimg/departments/view');

        return $this->actionFactory->create(
            \Magento\Framework\App\Action\Forward::class
        );
    }
}
