<?php

namespace Swissup\Easycatalogimg\Plugin\Model\Category;

use Magento\Framework\App\ObjectManager;
use Magento\Catalog\Model\Category\FileInfo;

class DataProvider
{
    /**
     * @var \Swissup\Easycatalogimg\Helper\Image
     */
    protected $helper;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var FileInfo|null
     */
    protected $fileInfo;

    /**
     * @param \Swissup\Easycatalogimg\Helper\Image $helper
     */
    public function __construct(
        \Swissup\Easycatalogimg\Helper\Image $helper,
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->helper = $helper;
        $this->urlBuilder = $urlBuilder;
    }

    public function afterGetMeta(
        \Magento\Catalog\Model\Category\DataProvider $subject,
        $result
    ) {
        $result['content']['children']['thumbnail']['arguments']['data']['config'] = [
            'componentType' => 'field',
            'dataType'      => 'string',
            'source'        => 'category',
            'label'         => __('Category Thumbnail'),
            'visible'       => true,
            'formElement'   => 'fileUploader',
            'elementTmpl'   => 'ui/form/element/uploader/uploader',
            'previewTmpl'   => 'Magento_Catalog/image-preview',
            'required'      => false,
            'sortOrder'     => 39, // insert before image
            'uploaderConfig' => [
                'url' => $this->urlBuilder->getUrl('easycatalogimg/category_thumbnail/upload')
            ]
        ];
        return $result;
    }

    /**
     * Prepare thumbnail data for uploader component
     *
     * @param  \Magento\Catalog\Model\Category\DataProvider $subject [description]
     * @param  [type]                                       $result  [description]
     * @return [type]                                                [description]
     */
    public function afterGetData(
        \Magento\Catalog\Model\Category\DataProvider $subject,
        $result
    ) {
        $category = $subject->getCurrentCategory();
        if ($category) {
            $id = $category->getId();
            if (isset($result[$id]['thumbnail'])) {
                unset($result[$id]['thumbnail']);
                $thumbnail = $category->getData('thumbnail');
                $url = false;
                if ($thumbnail && is_string($thumbnail)) {
                    $url = $this->helper->getBaseUrl() . $thumbnail;
                }

                $result[$id]['thumbnail'][0]['name'] = $thumbnail;
                $result[$id]['thumbnail'][0]['url'] = $url;
                if (null !== $this->getFileInfo()) {
                    $stat = $this->getFileInfo()->getStat($thumbnail);
                    $mime = $this->getFileInfo()->getMimeType($thumbnail);
                    $result[$id]['thumbnail'][0]['size'] = isset($stat) ? $stat['size'] : 0;
                    $result[$id]['thumbnail'][0]['type'] = $mime;
                }
            }

            // unset image data if there are no name and url
            if (isset($result[$id]['image'])) {
                if (!isset($result[$id]['image'][0]['name'])
                    || empty($result[$id]['image'][0]['name'])
                ) {
                    unset($result[$id]['image']);
                }
            }
        }

        return $result;
    }

    /**
     * Get FileInfo instance
     *
     * @return null|FileInfo
     */
    private function getFileInfo()
    {
        if ($this->fileInfo === null && class_exists(FileInfo::class)) {
            // Magento less than 2.2.0 does not have class FileInfo
            // that is why we have to use object manager
            $this->fileInfo = ObjectManager::getInstance()->get(FileInfo::class);
        }
        return $this->fileInfo;
    }
}
