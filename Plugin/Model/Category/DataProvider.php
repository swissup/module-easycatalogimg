<?php

namespace Swissup\Easycatalogimg\Plugin\Model\Category;

use Magento\Framework\App\ObjectManager;

class DataProvider
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param \Magento\Framework\UrlInterface $urlBuilder
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }

    public function afterPrepareMeta(
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
}
