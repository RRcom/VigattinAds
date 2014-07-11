<?php
namespace VigattinAds\Controller\Dashboard\Ads\Create\ChooseWebsite;

use VigattinAds\Controller\Dashboard\Ads\AdsController;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

class ChooseTourismBloggerAuthorController extends AdsController
{
    const USED_BY = ChooseWebsiteController::TOURISMBLOGGER;

    public function indexAction()
    {
        $actionContent = new ViewModel();
        $this->mainView->setVariable('title', 'Step 2. select author');
        $actionContent->setTemplate('vigattinads/view/dashboard/ads/create/chooseTourismBloggerAuthorView');
        $this->mainView->addChild($actionContent, 'actionContent');
        return $this->mainView;
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
