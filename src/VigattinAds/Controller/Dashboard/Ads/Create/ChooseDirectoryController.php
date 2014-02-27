<?php
namespace VigattinAds\Controller\Dashboard\Ads\Create;

use VigattinAds\Controller\Dashboard\Ads\AdsController;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

class ChooseDirectoryController extends AdsController
{
    const USED_BY = 'vigattintourism.com';

    public function indexAction()
    {
        $actionContent = new ViewModel();
        $this->mainView->setVariable('title', 'Step 2. Choose directory');
        $actionContent->setTemplate('vigattinads/view/dashboard/ads/create/chooseDirectoryView');
        $actionContent->setVariable('website', $this->sessionManager->getStorage()->tempAdsTemplate['showIn']);
        $this->mainView->addChild($actionContent, 'actionContent');
        return $this->mainView;
    }

    public function nextAction()
    {
        $allAbsoluteKeywords = '';
        $directories = $this->getRequest()->getPost('directory', array());
        if(!count($directories)) return $this->redirect()->toRoute('vigattinads_dashboard_ads_create', array('controller' => 'choose-directory'));
        foreach($directories as $directory) {
            $allAbsoluteKeywords .= trim($directory).'|';
        }
        $allAbsoluteKeywords = trim($allAbsoluteKeywords, '|');
        $this->sessionManager->getStorage()->tempAdsTemplate['template'] = 'home-sidebar-right';
        $this->sessionManager->getStorage()->tempAdsKeyword = $allAbsoluteKeywords;
        return $this->redirect()->toRoute('vigattinads_dashboard_ads_info');
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
