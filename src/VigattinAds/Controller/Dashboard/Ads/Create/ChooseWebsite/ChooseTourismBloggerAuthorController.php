<?php
namespace VigattinAds\Controller\Dashboard\Ads\Create\ChooseWebsite;

use VigattinAds\Controller\Dashboard\Ads\AdsController;
use VigattinAds\DomainModel\Tourism\AuthorCollection;
use VigattinAds\DomainModel\Tourism\BasicArticleCategoryProvider;
use VigattinAds\DomainModel\Tourism\BasicAuthorProvider;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

class ChooseTourismBloggerAuthorController extends AdsController
{
    const USED_BY = ChooseWebsiteController::TOURISMBLOGGER;
    /**
     * set to true if fetching is done by php false if using ajax, this will disable fetching author list from server
     * @var bool
     */
    protected $autoFetchAuthor = false;

    public function indexAction()
    {
        $this->onSelectAuthor();
        $actionContent = new ViewModel();
        $this->mainView->setVariable('title', 'Step 2. select author');
        $actionContent->setTemplate('vigattinads/view/dashboard/ads/create/chooseTourismBloggerAuthorView');
        $actionContent->setVariable('categories', $this->getCategories());
        $actionContent->setVariable('authors', $this->searchAuthor());
        $actionContent->setVariable('searchString', $this->getRequest()->getPost('searchString', ''));
        $this->mainView->addChild($actionContent, 'actionContent');
        return $this->mainView;
    }

    protected function onSelectAuthor()
    {
        $authorId = $this->getRequest()->getPost('authorId', '');
        $authorFirstName = $this->getRequest()->getPost('authorFirstName', '');
        $authorLastName = $this->getRequest()->getPost('authorLastName', '');
        if($authorId) {
            $this->sessionManager->getStorage()->tempAdsAuthorId = $authorId;
            $this->sessionManager->getStorage()->tempAdsAuthorName = $authorFirstName.' '.$authorLastName;
            $this->redirect()->toRoute('vigattinads_dashboard_ads_create_choose_website_blogger_import');
        }
    }

    protected function searchAuthor()
    {
        if(!$this->autoFetchAuthor) return new AuthorCollection();
        $searchString = $this->getRequest()->getPost('searchString', '');
        $filter = $this->getRequest()->getPost('filter', array());
        $offset = $this->getRequest()->getPost('offset', 0);
        $limit = $this->getRequest()->getPost('limit', 10);
        $authorProvider = new BasicAuthorProvider($this->serviceLocator);
        return $authorProvider->searchAuthor($searchString, $filter, $offset, $limit);
    }

    protected function getCategories()
    {
        $offset = $this->getRequest()->getPost('offset', 0);
        $limit = $this->getRequest()->getPost('limit', 30);
        $articleCategoryProvider = new BasicArticleCategoryProvider($this->serviceLocator);
        return $articleCategoryProvider->getCategories($offset, $limit);
    }

    public function onDispatch(MvcEvent $e)
    {
        $controller = parent::onDispatch($e);
        if(strtolower($this->sessionManager->getStorage()->tempAdsTemplate['showIn']) != strtolower(self::USED_BY)) {
            return $this->redirect()->toRoute('vigattinads_dashboard_ads_create', array('controller' => 'choose-website'));
        }
        return $controller;
    }
}
