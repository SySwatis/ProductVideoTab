<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Simple product data view
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Boeki\ProductVideoTab\Block\Product\View;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Gallery\ImagesConfigFactoryInterface;
use Magento\Catalog\Model\Product\Image\UrlBuilder;
use Magento\Framework\Data\Collection;
use Magento\Framework\DataObject;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Stdlib\ArrayUtils;

/**
 * Product gallery block
 *
 * @api
 * @since 100.0.2
 */
class Gallery extends \Magento\Catalog\Block\Product\View\Gallery
{


    /**
     * Retrieve product images in JSON format
     *
     * @return string
     */
    
    public function getIframeAttributes() 
    {
       return  array(
            'vimeo.com'=>array(
                'src'=>'https://player.vimeo.com/video/%s?autoplay=1',
                'separator'=>'/',
                'attribute'=>'webkitallowfullscreen mozallowfullscreen allowfullscreen',
                'slice'=>'path'
            ),
            'www.youtube.com'=>array(
                'src'=>'https://www.youtube.com/embed/%s/?autoplay=1&showinfo=0',
                'separator'=>'=',
                'attribute'=>'webkitallowfullscreen mozallowfullscreen allowfullscreen',
                'slice'=>'query'
            ),
            'www.dailymotion.com'=>array(
                'src'=>'https://www.dailymotion.com/embed/video/%s?autoplay=1',
                'separator'=>'/',
                'attribute'=>'webkitallowfullscreen mozallowfullscreen allowfullscreen',
                'slice'=>'path'
            )
        );
    }

    public function getGalleryImagesJson()
    {
        $imagesItems = [];
        /** @var DataObject $image */
        foreach ($this->getGalleryImages() as $image) {
        	if($image->getMediaType()!=='external-video'){
                $imageItem = new DataObject([
                    'thumb' => $image->getData('small_image_url'),
                    'img' => $image->getData('medium_image_url'),
                    'full' => $image->getData('large_image_url'),
                    'caption' => ($image->getLabel() ?: $this->getProduct()->getName()),
                    'position' => $image->getData('position'),
                    'isMain'   => $this->isMainImage($image),
                    'type' => str_replace('external-', '', $image->getMediaType()),
                    'videoUrl' => $image->getVideoUrl(),
                ]);
                foreach ($this->getGalleryImagesConfig()->getItems() as $imageConfig) {
                    $imageItem->setData(
                        $imageConfig->getData('json_object_key'),
                        $image->getData($imageConfig->getData('data_object_key'))
                    );
                }
	            $imagesItems[] = $imageItem->toArray();
            }
        }
        if (empty($imagesItems)) {
            $imagesItems[] = [
                'thumb' => $this->_imageHelper->getDefaultPlaceholderUrl('thumbnail'),
                'img' => $this->_imageHelper->getDefaultPlaceholderUrl('image'),
                'full' => $this->_imageHelper->getDefaultPlaceholderUrl('image'),
                'caption' => '',
                'position' => '0',
                'isMain' => true,
                'type' => 'image',
                'videoUrl' => null,
            ];
        }
        return json_encode($imagesItems);
    }
    
}
