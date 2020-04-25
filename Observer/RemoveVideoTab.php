<?php

namespace Boeki\ProductVideoTab\Observer;
    
    use Magento\Framework\Event\Observer;
    use Magento\Framework\Event\ObserverInterface;

class RemoveVideoTab implements ObserverInterface
{   

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
        return $i > 0 ? false : true;
    }

    public function execute(Observer $observer)
    {
        /** @var \Magento\Framework\View\Layout $layout */
        $blockName = 'videostab';
        $layout = $observer->getLayout();
        $block = $layout->getBlock($blockName);
        if($block && isset($block) && $product = $this->getCurrentProduct() ){
            $currentGallery = $product->getMediaGalleryImages();
            $removeVideoTab = $this->hasGalleryVideo($currentGallery);
                
            if ($block && $removeVideoTab) {
                $layout->unsetElement($blockName);
            }
        }
    }
}