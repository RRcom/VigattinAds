<?php
namespace VigattinAds\Controller\Dashboard\Ads\Create\ChooseWebsite;

use VigattinAds\Controller\Dashboard\Ads\AdsController;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

class ChooseVigDirectoryController extends AdsController
{
    const USED_BY = 'vigattin.com';

    public function indexAction()
    {
        $actionContent = new ViewModel();
        $this->mainView->setVariable('title', 'Step 2. Show allowed category');
        $actionContent->setTemplate('vigattinads/view/dashboard/ads/create/chooseVigDirectoryView');
        $actionContent->setVariable('website', $this->sessionManager->getStorage()->tempAdsTemplate['showIn']);
        $actionContent->setVariable('categories', explode('|', $this->sessionManager->getStorage()->tempAdsKeyword));
        $this->mainView->addChild($actionContent, 'actionContent');
        return $this->mainView;
    }

    public function onDispatch(MvcEvent $e)
    {
        $controller = parent::onDispatch($e);
        $this->website = $this->sessionManager->getStorage()->tempAdsTemplate['showIn'];
        if(strtolower($this->sessionManager->getStorage()->tempAdsTemplate['showIn']) != self::USED_BY) {
            return $this->redirect()->toRoute('vigattinads_dashboard_ads_create', array('controller' => 'choose-website'));
        }
        return $controller;
    }
}
