<?php
namespace VigattinAds\DomainModel\AdsCategory;

use Zend\ServiceManager\ServiceManager;
use VigattinAds\DomainModel\Ads;

class TourismAdsCategoryProvider implements AdsCategoryProviderInterface
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    /**
     * @var \VigattinAds\DomainModel\Ads
     */
    protected $ads;

    /**
     * @param ServiceManager $serviceManager
     * @param Ads $ads
     * @param array $selectedCategory
     */

    protected $selectedCategory;

    public function __construct(ServiceManager $serviceManager, Ads $ads, Array $selectedCategory)
    {
        $this->serviceManager = $serviceManager;
        $this->ads = $ads;
        $this->selectedCategory = implode('', $selectedCategory);
    }


    /**
     * @return \VigattinAds\DomainModel\AdsCategory\AdsCategoryCollection;
     */
    public function getAdsCategory()
    {
        $catCollection = new AdsCategoryCollection();
        if($this->selectedCategory) {
            $catCollection->add(new AdsCategory('(homepage)', 'http://vigattintourism.com#preview', strpos($this->selectedCategory, '(homepage)') ? true : false, 'Homepage'));
            $catCollection->add(new AdsCategory('(destination)', 'http://vigattintourism.com/tourism/destinations#preview', true, 'Destination'));
            $catCollection->add(new AdsCategory('(articles)', 'http://vigattintourism.com/tourism/articles?page=1#preview', true, 'Articles'));
            $catCollection->add(new AdsCategory('(tourist spots)', 'http://vigattintourism.com/tourism/tourist_spots#preview', true, 'Tourist Spots'));
            $catCollection->add(new AdsCategory('(discussion)', 'http://vigattintourism.com/tourism/discussion#preview', true, 'Discussion'));
            $catCollection->add(new AdsCategory('(destinations)', 'http://vigattintourism.com/tourism/destinations/91/directory#preview', true, 'Destinations'));
        } else {
            $catCollection->add(new AdsCategory('(homepage)', 'http://vigattintourism.com#preview', true, 'Homepage'));
            $catCollection->add(new AdsCategory('(destination)', 'http://vigattintourism.com/tourism/destinations#preview', true, 'Destination'));
            $catCollection->add(new AdsCategory('(articles)', 'http://vigattintourism.com/tourism/articles?page=1#preview', true, 'Articles'));
            $catCollection->add(new AdsCategory('(tourist spots)', 'http://vigattintourism.com/tourism/tourist_spots#preview', true, 'Tourist Spots'));
            $catCollection->add(new AdsCategory('(discussion)', 'http://vigattintourism.com/tourism/discussion#preview', true, 'Discussion'));
            $catCollection->add(new AdsCategory('(destinations)', 'http://vigattintourism.com/tourism/destinations/91/directory#preview', true, 'Destinations'));
        }

        return $catCollection;
    }

}