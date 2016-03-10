<?php
namespace Swissup\Easycatalogimg\Helper;

use \Magento\Framework\UrlInterface;
use \Magento\Framework\App\Filesystem\DirectoryList;
/**
 * Easycatalogimg image helper
 */
class Image extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * media sub folder
     * @var string
     */
    protected $subDir = 'catalog/category/resized/';
    /**
     * Resized image background color
     * @var Array
     */
    protected $backgroundColor = null;
    /**
     * File Uploader factory
     *
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $ioFile;
    /**
     * image factory
     *
     * @var \Magento\Framework\Image\Factory
     */
    protected $imageFactory;
    /**
     * Get extension configuration helper
     * @var \Swissup\Easycatalogimg\Helper\Config
     */
    protected $configHelper;
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $fileSystem;
    /**
     * url builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Filesystem\Io\File $ioFile
     * @param \Magento\Framework\Image\Factory $imageFactory
     * @param \Swissup\Easycatalogimg\Helper\Config $configHelper
     * @param \Magento\Framework\Filesystem $fileSystem
     * @param \Magento\Framework\UrlInterface $urlBuilder
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Filesystem\Io\File $ioFile,
        \Magento\Framework\Image\Factory $imageFactory,
        \Swissup\Easycatalogimg\Helper\Config $configHelper,
        \Magento\Framework\Filesystem $fileSystem,
        UrlInterface $urlBuilder
    ) {
        $this->ioFile = $ioFile;
        $this->imageFactory = $imageFactory;
        $this->configHelper = $configHelper;
        $this->fileSystem = $fileSystem;
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context);
    }
    /**
     * Return URL for resized image
     *
     * @param $imageFile resize image url
     * @param $width resize image width
     * @param $height resize image height
     * @return bool|string
     */
    public function resize($imageFile, $width, $height)
    {
        if (!$imageFile) {
            return false;
        }
        $imageName = substr(strrchr($imageFile, "/"), 1);
        if ('255,255,255' !== $this->getBackgroundColor(true)) {
            $sizeDir = $width . 'x' . $height . '/' . $this->getBackgroundColor(true) . '/';
        } else {
            $sizeDir = $width . 'x' . $height . '/';
        }
        $cacheDir  = $this->getBaseDir($this->subDir . $sizeDir);
        $cacheUrl  = $this->getBaseUrl($this->subDir . $sizeDir);
        $io = $this->ioFile;
        $io->checkAndCreateFolder($cacheDir);
        $io->open(['path' => $cacheDir]);
        if ($io->fileExists($imageName)) {
            return $cacheUrl . $imageName;
        }
        try {
            $image = $this->imageFactory->create($imageFile);
            $image->constrainOnly(true);
            $image->keepAspectRatio(true);
            $image->keepFrame(true);
            $image->keepTransparency(true);
            $image->backgroundColor($this->getBackgroundColor());
            $image->resize($width, $height);
            $image->save($cacheDir . '/' . $imageName);
            return $cacheUrl . $imageName;
        } catch (\Exception $e) {
            return false;
        }
    }
    /**
     * Set resized image background color
     * @param String|Array $rgb background color in rgb format
     */
    public function setBackgroundColor($rgb)
    {
        if (!is_array($rgb)) {
            $rgb = explode(',', $rgb);
            foreach ($rgb as $i => $color) {
                $rgb[$i] = (int)$color;
            }
        }
        $this->backgroundColor = $rgb;
        return $this;
    }
    /**
     * Get resized image background color
     * @param Boolean $toString return as string
     */
    public function getBackgroundColor($toString = false)
    {
        if (null === $this->backgroundColor) {
            $rgb = $this->configHelper->getBackgroundColor();
            $this->setBackgroundColor($rgb);
        }
        if ($toString) {
            return implode(',', $this->backgroundColor);
        }
        return $this->backgroundColor;
    }
    /**
     * get images base url
     *
     * @return string
     */
    public function getBaseUrl(
        $path = 'catalog/category/',
        $type = UrlInterface::URL_TYPE_MEDIA
    )
    {
        return $this->urlBuilder
            ->getBaseUrl(['_type' => $type]) . $path;
    }
    /**
     * get base image dir
     *
     * @return string
     */
    public function getBaseDir(
        $path = 'catalog/category/',
        $directoryCode = DirectoryList::MEDIA
    )
    {
        return $this->fileSystem
            ->getDirectoryWrite($directoryCode)
            ->getAbsolutePath($path);
    }
}
