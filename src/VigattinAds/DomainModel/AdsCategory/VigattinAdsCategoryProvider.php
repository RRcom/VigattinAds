<?php
namespace VigattinAds\DomainModel\AdsCategory;

use Zend\ServiceManager\ServiceManager;
use VigattinAds\DomainModel\Ads;

class VigattinAdsCategoryProvider implements AdsCategoryProviderInterface
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
    public function __construct(ServiceManager $serviceManager, Ads $ads, Array $selectedCategory)
    {
        $this->serviceManager = $serviceManager;
        $this->ads = $ads;
    }


    /**
     * @return \VigattinAds\DomainModel\AdsCategory\AdsCategoryCollection;
     */
    public function getAdsCategory()
    {
        $catCollection = new AdsCategoryCollection();
        $homepage = new AdsCategory('(homepage)', 'http://www.vigattin.com#preview', true, 'Homepage');
        $catCollection->add($homepage);
        return $catCollection;
    }

}