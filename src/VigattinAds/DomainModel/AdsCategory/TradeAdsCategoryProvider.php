<?php
namespace VigattinAds\DomainModel\AdsCategory;

use VigattinAds\DomainModel\Ads;
use Zend\ServiceManager\ServiceManager;
use VigattinAds\DomainModel\VigattinTrade\CatMap;
use Zend\View\Model\ViewModel;

class TradeAdsCategoryProvider extends GenericAdsCategoryProvider
{
    protected $categories = array();

    public function __construct(ServiceManager $serviceManager, Ads $ads, Array $selectedCategory)
    {
        parent::__construct($serviceManager, $ads, $selectedCategory);
        $this->processChangeCategory();
        $this->generateCategories();
    }

    protected function generateCategories()
    {
        $leftSide = array();
        $featuredAds = array();
        $adsListing = array();
        
        $tradeCategory = explode('|', $this->ads->get('category'));
        $tradeCategory = CatMap::cutCategoryIfNotValid($tradeCategory);
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

    public function changeCategoryMenu()
    {
        $menu = new ViewModel();
        $menu->setTemplate('vigattinads/view/dashboard/ads/edit/category/tradeChangeCategoryView');
        $menu->setVariable('categories', CatMap::$categoryMap);
        return $menu;
    }

    public function processChangeCategory()
    {
        /** @var \Zend\Http\PhpEnvironment\Request $request */
        $request = $this->serviceManager->get('Request');
        $cat = $request->getPost('tradeStringCategory', false);
        if($cat === false) return;
        $this->ads->set('category', $cat);
        $this->ads->persistSelf();
        $this->ads->flush();
    }
}