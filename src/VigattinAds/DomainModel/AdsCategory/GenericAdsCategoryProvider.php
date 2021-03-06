<?php
namespace VigattinAds\DomainModel\AdsCategory;

use Zend\ServiceManager\ServiceManager;
use VigattinAds\DomainModel\Ads;

class GenericAdsCategoryProvider implements AdsCategoryProviderInterface
{
    /**
     * This will be your checkboxes. alter this code to generate custom checkboxes.
     * @var array
     */
    protected $categories = array(
        // add more array like this to generate more checkboxes
        array('keyword' => '(homepage)',    'title' => 'Homepage',  'previewLink' => 'http://vigattin.com#preview', 'group' => '', 'disable' => false),
    );

    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    /**
     * @var \VigattinAds\DomainModel\Ads
     */
    protected $ads;

    /**
     * @var string joint value of checkboxes ex. value "(homepage)(sidebar)(footer)"
     */
    protected $selectedCategory;

    /**
     * @param ServiceManager $serviceManager
     * @param Ads $ads
     * @param array $selectedCategory
     */
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
        $checkedCount = 0;
        if($this->selectedCategory) {
            foreach($this->categories as $category) {
                $checked = is_int(@strpos($this->selectedCategory, $category['keyword'])) ? true : false;
                $catCollection->add(new AdsCategory($category['keyword'], $category['previewLink'], $checked, $category['title'], $category['group'], $category['disable']));
                if($checked) $checkedCount++;
            }
        } else {
            foreach($this->categories as $category) {
                $checked = is_int(@strpos($this->ads->get('keywords'), $category['keyword'])) ? true : false;
                $catCollection->add(new AdsCategory($category['keyword'], $category['previewLink'], $checked, $category['title'], $category['group'], $category['disable']));
                if($checked) $checkedCount++;
            }
        }

        if(iterator_count($catCollection) && !$checkedCount) {
            $catCollection->rewind();
            $catCollection->current()->setSelected(true);
        }

        return $catCollection;
    }

}