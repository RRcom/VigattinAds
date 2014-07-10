<?php
namespace VigattinAds\Controller\Dashboard\Ads\Create\ChooseWebsite;

use VigattinAds\Controller\Dashboard\Ads\AdsController;
use Zend\View\Model\ViewModel;

class ChooseWebsiteController extends AdsController
{
    public function indexAction()
    {
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
                return $this->redirect()->toRoute('vigattinads_dashboard_ads_create_choose_website_import');
                break;
            case 'trade':
                $this->sessionManager->getStorage()->tempAdsTemplate = array('showIn' => 'vigattintrade.com', 'template' => 'home-sidebar-left');
                return $this->redirect()->toRoute('vigattinads_dashboard_ads_create_choose_website_import');
                break;
            case 'tourism':
                $this->sessionManager->getStorage()->tempAdsTemplate = array('showIn' => 'vigattintourism.com', 'template' => 'home-sidebar-right');
                return $this->redirect()->toRoute('vigattinads_dashboard_ads_create_choose_website_import');
                break;
            case 'deals':
                $this->sessionManager->getStorage()->tempAdsTemplate = array('showIn' => 'vigattindeals.com', 'template' => 'home-body');
                return $this->redirect()->toRoute('vigattinads_dashboard_ads_create_choose_website_import');
                break;
            case 'tourism-bloggers':
                $this->sessionManager->getStorage()->tempAdsTemplate = array('showIn' => 'vigattintourism.com', 'template' => 'home-body');
                return $this->redirect()->toRoute('vigattinads_dashboard_ads_create_choose_website_import');
                break;
            default:
                return $this->redirect()->toRoute('vigattinads_dashboard_ads_create_choose_website_import');
                break;
        }
    }
}
