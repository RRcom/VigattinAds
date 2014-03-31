<?php
namespace VigattinAds\Controller\Dashboard\Ads\Create;

use VigattinAds\Controller\Dashboard\Ads\AdsController;
use Zend\View\Model\ViewModel;

class ChooseWebsiteController extends AdsController
{
    public function indexAction()
    {
        $this->saveImportedAdsToSession();
        $actionContent = new ViewModel();
        $this->mainView->setVariable('title', 'Step 1. Choose a website');
        $actionContent->setTemplate('vigattinads/view/dashboard/ads/create/chooseWebsiteView');
        $this->mainView->addChild($actionContent, 'actionContent');
        return $this->mainView;
    }

    public function chooseAction() {
        $param = $this->params()->fromRoute('param', '');
        switch(strtolower($param)) {
            case 'vigattin':
                $this->sessionManager->getStorage()->tempAdsTemplate = array('showIn' => 'vigattin.com', 'template' => 'home');
                return $this->redirect()->toRoute('vigattinads_dashboard_ads_create', array('controller' => 'choose-vig-directory'));
                break;
            case 'trade':
                $this->sessionManager->getStorage()->tempAdsTemplate = array('showIn' => 'vigattintrade.com', 'template' => 'home-sidebar-left');
                return $this->redirect()->toRoute('vigattinads_dashboard_ads_create', array('controller' => 'choose-category'));
                break;
            case 'tourism':
                $this->sessionManager->getStorage()->tempAdsTemplate = array('showIn' => 'vigattintourism.com', 'template' => 'home-sidebar-right');
                return $this->redirect()->toRoute('vigattinads_dashboard_ads_create', array('controller' => 'choose-directory'));
                break;
            case 'deals':
                $this->sessionManager->getStorage()->tempAdsTemplate = array('showIn' => 'vigattindeals.com', 'template' => 'home-body');
                return $this->redirect()->toRoute('vigattinads_dashboard_ads_create', array('controller' => 'choose-website'));
                break;
            default:
                return $this->redirect()->toRoute('vigattinads_dashboard_ads_create', array('controller' => 'choose-website'));
                break;
        }
    }

    public function saveImportedAdsToSession()
    {
        if($this->getRequest()->getPost('action', '') == 'save-session') {
            $this->sessionManager->getStorage()->tempAdsTitle = $this->getRequest()->getPost('ads-title', '');
            $this->sessionManager->getStorage()->origAdsTitle = $this->getRequest()->getPost('ads-title', '');
            $this->sessionManager->getStorage()->tempAdsUrl = $this->getRequest()->getPost('ads-url', '');
            $this->sessionManager->getStorage()->origAdsUrl = $this->getRequest()->getPost('ads-url', '');
            $this->sessionManager->getStorage()->tempAdsKeyword = $this->getRequest()->getPost('ads-keyword', '');
            $this->sessionManager->getStorage()->tempAdsDescription = $this->getRequest()->getPost('ads-description', '');
            $this->sessionManager->getStorage()->origAdsDescription = $this->getRequest()->getPost('ads-description', '');
            $this->sessionManager->getStorage()->tempAdsImageDataUrl = $this->getRequest()->getPost('ads-image-data-url', '');
            $this->sessionManager->getStorage()->tempAdsPrice = $this->getRequest()->getPost('ads-price', '');
            $this->sessionManager->getStorage()->tempAdsDate = $this->getRequest()->getPost('ads-date', '');
        }
    }
}
