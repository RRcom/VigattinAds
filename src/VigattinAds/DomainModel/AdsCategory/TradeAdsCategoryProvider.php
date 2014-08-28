<?php
namespace VigattinAds\DomainModel\AdsCategory;

use VigattinAds\DomainModel\Ads;
use Zend\ServiceManager\ServiceManager;
use VigattinAds\DomainModel\VigattinTrade\CatMap;

class TradeAdsCategoryProvider extends GenericAdsCategoryProvider
{
    /*
    protected $categories = array(
        array('keyword' => '(homepage)',    'title' => 'All Category',  'previewLink' => 'http://vigattin.com#preview', 'group' => 'Left Side', 'disable' => false),
        array('keyword' => '(homepage general category)',    'title' => 'General Category',  'previewLink' => 'http://vigattin.com#preview', 'group' => 'Left Side', 'disable' => false),
        array('keyword' => '(homepage general category playthings and collectibles)',    'title' => 'Playthings And Collectibles',  'previewLink' => 'http://vigattin.com#preview', 'group' => 'Left Side', 'disable' => false),

        array('keyword' => '(featured ads vehicles cars, sedans & minivans)',    'title' => 'Cars and Sedans',  'previewLink' => '//www.vigattintrade.com#preview', 'group' => 'Featured Ads', 'disable' => true),
        array('keyword' => '(featured ads real estate)',    'title' => 'Real Estate',  'previewLink' => '//www.vigattintrade.com#preview', 'group' => 'Featured Ads', 'disable' => true),
        array('keyword' => '(featured ads vehicles motorcycles & scooters)',    'title' => 'Motorcycle',  'previewLink' => '//www.vigattintrade.com#preview', 'group' => 'Featured Ads', 'disable' => true),
        array('keyword' => '(featured ads vehicles suvs, crossovers & pickups)',    'title' => 'Suvs, Crossovers & Pickups',  'previewLink' => '//www.vigattintrade.com#preview', 'group' => 'Featured Ads', 'disable' => true),
        array('keyword' => '(featured ads general category)',    'title' => 'General Category',  'previewLink' => '//www.vigattintrade.com#preview', 'group' => 'Featured Ads', 'disable' => false),

        array('keyword' => '(ads listing homepage)',    'title' => 'All Category',  'previewLink' => 'http://vigattin.com#preview', 'group' => 'Ads Listing', 'disable' => false),
        array('keyword' => '(ads listing homepage general category)',    'title' => 'General Category',  'previewLink' => 'http://vigattin.com#preview', 'group' => 'Ads Listing', 'disable' => false),
        array('keyword' => '(ads listing homepage general category playthings and collectibles)',    'title' => 'Playthings And Collectibles',  'previewLink' => 'http://vigattin.com#preview', 'group' => 'Ads Listing', 'disable' => false),
    );
    */

    protected $categories = array();

    public function __construct(ServiceManager $serviceManager, Ads $ads, Array $selectedCategory)
    {
        parent::__construct($serviceManager, $ads, $selectedCategory);
        $this->generateCategories();
    }


    protected function generateCategories()
    {
        $leftSide = array();
        $featuredAds = array();
        $adsListing = array();
        
        $tradeCategory = explode('|', $this->ads->get('category'));
        foreach($tradeCategory as $key => $value) {
            $leftSide[] = array('keyword' => strtolower(CatMap::createAbsoluteCat($tradeCategory, $key)),    'title' => $value,  'previewLink' => CatMap::generatePreviewUrl($tradeCategory, $key), 'group' => 'Left Side', 'disable' => false);
            $adsListing[] = array('keyword' => strtolower(CatMap::createAbsoluteCat($tradeCategory, $key, 'ads listing')),    'title' => $value,  'previewLink' => CatMap::generatePreviewUrl($tradeCategory, $key), 'group' => 'Ads Listing', 'disable' => false);
        }

        $featuredAds[] = array('keyword' => '(featured ads vehicles cars, sedans & minivans)',    'title' => 'Cars and Sedans',  'previewLink' => '//www.vigattintrade.com#preview', 'group' => 'Featured Ads', 'disable' => !CatMap::isCatMatch($this->ads->get('category'), 'cars, sedans & minivans'));
        $featuredAds[] = array('keyword' => '(featured ads real estate)',    'title' => 'Real Estate',  'previewLink' => '//www.vigattintrade.com#preview', 'group' => 'Featured Ads', 'disable' => !CatMap::isCatMatch($this->ads->get('category'), 'real estate'));
        $featuredAds[] = array('keyword' => '(featured ads vehicles motorcycles & scooters)',    'title' => 'Motorcycle',  'previewLink' => '//www.vigattintrade.com#preview', 'group' => 'Featured Ads', 'disable' => !CatMap::isCatMatch($this->ads->get('category'), 'motorcycles & scooters'));
        $featuredAds[] = array('keyword' => '(featured ads vehicles suvs, crossovers & pickups)',    'title' => 'Suvs, Crossovers & Pickups',  'previewLink' => '//www.vigattintrade.com#preview', 'group' => 'Featured Ads', 'disable' => !CatMap::isCatMatch($this->ads->get('category'), 'suvs, crossovers & pickups'));
        $featuredAds[] = array('keyword' => '(featured ads general category)',    'title' => 'General Category',  'previewLink' => '//www.vigattintrade.com#preview', 'group' => 'Featured Ads', 'disable' => !CatMap::isCatMatch($this->ads->get('category'), 'general category'));

        $this->categories = array_merge($this->categories, $leftSide, $featuredAds, $adsListing);
    }
}