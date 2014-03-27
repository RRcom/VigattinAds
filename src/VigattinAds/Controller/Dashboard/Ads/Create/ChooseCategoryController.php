<?php
namespace VigattinAds\Controller\Dashboard\Ads\Create;

use VigattinAds\Controller\Dashboard\Ads\AdsController;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

class ChooseCategoryController extends AdsController
{
    const USED_BY = 'vigattintrade.com';

    public function indexAction()
    {
        $categories = explode('|', $this->sessionManager->getStorage()->tempAdsKeyword);

        // Add new ads position
        array_unshift($categories, 'Ads Listing');
        array_unshift($categories, 'Featured Ads');

        $actionContent = new ViewModel();
        $this->mainView->setVariable('title', 'Step 2. Show allowed category');
        $actionContent->setTemplate('vigattinads/view/dashboard/ads/create/chooseCategoryView');
        $actionContent->setVariable('website', $this->sessionManager->getStorage()->tempAdsTemplate['showIn']);
        $actionContent->setVariable('categories', $categories);
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
