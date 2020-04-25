<?php

namespace Boeki\ProductVideoTab\Observer;

use Magento\Framework\Event\ObserverInterface;
//use Boeki\ProductVddeoTab\Helper\Data;
use Magento\Framework\Event\Observer;

class UpdateLayoutObserver implements ObserverInterface
{
    // protected $helper;

    // public function __construct
    // (
    //     Data $data
    // )
    // {
    //     $this->helper = $data;
    // }
    // https://meetanshi.com/blog/update-layout-using-observer-in-magento-2/

    protected $_registry;

    /**
     * @param \Magento\Framework\Registry $registry
     */
    
    public function __construct(
        \Magento\Framework\Registry $registry
    ) {
        $this->_registry = $registry;

    }

    public function getCurrentProduct()
    {        
        return $this->_registry->registry('current_product');
    }    

    public function hasGalleryVideo($gallery) 
    {

        $i = 0;
        foreach ($gallery as $mediaGallery) {
            if($mediaGallery->getMediaType()==='external-video'){
                $i++;
            }
        }
        return $i > 0 ? true : false;
    }

    public function execute(Observer $observer)
    {
        $layout = $observer->getData('layout');

        $currentHandles = $layout->getUpdate()->getHandles();
        $product = $this->getCurrentProduct();
       
        if (!in_array('default', $currentHandles) || !$product ) {
            return $this;
        }
        
        $currentGallery = $product->getMediaGalleryImages();
        $addVideoTab = $this->hasGalleryVideo($currentGallery);

        if( $addVideoTab ) {
            $layout->getUpdate()->addHandle('custom_layout');
        }

        return $this;
    }
}