<?php
namespace VigattinAds\DomainModel\AdsCategory;

use VigattinAds\DomainModel\Ads;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Model\ViewModel;
use VigattinAds\DomainModel\Tourism\BasicArticleCategoryProvider;
use VigattinAds\DomainModel\Tourism\BasicAuthorProvider;

class TourismArticleAdsCategoryProvider extends GenericAdsCategoryProvider
{

    /**
     * @var \VigattinAds\DomainModel\Tourism\BasicArticleCategoryProvider
     */
    protected $authorCategoryProvider;

    /**
     * @var \VigattinAds\DomainModel\Tourism\BasicAuthorProvider
     */
    protected $authorProvider;

    protected $categories;

    /**
     * @var array ids of selected author by user
     */
    protected $ids;

    /**
     * @var array positions selected by user
     */
    protected $position;

    public function __construct(ServiceManager $serviceManager, Ads $ads, Array $selectedCategory)
    {
        parent::__construct($serviceManager, $ads, $selectedCategory);
        $this->authorCategoryProvider = new BasicArticleCategoryProvider($this->serviceManager);
        $this->authorProvider = new BasicAuthorProvider($this->serviceManager);
        $this->extractKeywordValue();
        $this->generateCheckboxValue();
    }

    public function getAuthorSelectMenu()
    {
        $menu = new ViewModel();
        $menu->setTemplate('vigattinads/view/dashboard/ads/edit/category/articleAuthorSelectView');
        $menu->setVariable('categories', $this->authorCategoryProvider->getCategories(0, 50));
        return $menu;
    }

    protected function extractKeywordValue()
    {
        $this->ids = array();
        $this->position = array();
        $keywords = $this->ads->get('keywords');
        foreach(explode(')', $keywords) as $keyword ) {
            $id = filter_var($keyword, FILTER_SANITIZE_NUMBER_INT);
            if(is_numeric($id)) $this->ids[$id] = $id;
            if(strpos($keyword, 'header') !== false) $this->position['header'] = 'header';
            if(strpos($keyword, 'rightside') !== false) $this->position['rightside'] = 'rightside';
            if(strpos($keyword, 'footer') !== false) $this->position['footer'] = 'footer';
        }
    }

    protected function generateCheckboxValue()
    {
        $this->categories = array();
        $header = '';
        $rightside = '';
        $footer = '';

        foreach($this->ids as $id) {
            $header .= "(tourism article header $id)";
            $rightside .= "(tourism article rightside $id)";
            $footer .= "(tourism article footer $id)";
        }

        $this->categories[] = array('keyword' => $header, 'title' => 'Head Banner',  'previewLink' => 'http://vigattintourism.com/tourism/articles#preview', 'group' => '', 'disable' => false);
        $this->categories[] = array('keyword' => $rightside, 'title' => 'Side Bar',  'previewLink' => 'http://vigattintourism.com/tourism/articles#preview', 'group' => '', 'disable' => false);
        $this->categories[] = array('keyword' => $footer, 'title' => 'Foot Banner',  'previewLink' => 'http://vigattintourism.com/tourism/articles#preview', 'group' => '', 'disable' => false);
    }
}