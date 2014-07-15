<?php
namespace VigattinAds\Controller\Dashboard\Ads\Create\ChooseWebsite;

use VigattinAds\Controller\Dashboard\Ads\AdsController;
use VigattinAds\DomainModel\Tourism\BasicAuthorProvider;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

class ChooseTourismBloggerAuthorController extends AdsController
{
    const USED_BY = ChooseWebsiteController::TOURISMBLOGGER;

    public function indexAction()
    {
        $this->onSelectAuthor();
        $actionContent = new ViewModel();
        $this->mainView->setVariable('title', 'Step 2. select author');
        $actionContent->setTemplate('vigattinads/view/dashboard/ads/create/chooseTourismBloggerAuthorView');
        $actionContent->setVariable('authors', $this->searchAuthor());
        $this->mainView->addChild($actionContent, 'actionContent');
        return $this->mainView;
    }

    protected function onSelectAuthor()
    {
        $authorId = $this->getRequest()->getPost('authorId', '');
        if($authorId) {
            $this->sessionManager->getStorage()->tempAdsAuthorId = $authorId;
            $this->redirect()->toRoute('vigattinads_dashboard_ads_create_choose_website_blogger_import');
        }
    }

    protected function searchAuthor()
    {
        $authorProvider = new BasicAuthorProvider();
        return $authorProvider->searchAuthor();
    }

    public function onDispatch(MvcEvent $e)
    {
        $controller = parent::onDispatch($e);

        if(strtolower($this->sessionManager->getStorage()->tempAdsTemplate['showIn']) != self::USED_BY) {
            return $this->redirect()->toRoute('vigattinads_dashboard_ads_create', array('controller' => 'choose-website'));
        }
        return $controller;
    }
}
