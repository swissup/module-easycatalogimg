<?php

namespace Swissup\Easycatalogimg\Plugin\Model\Category;

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
            }
        }
        return $result;
    }
}
