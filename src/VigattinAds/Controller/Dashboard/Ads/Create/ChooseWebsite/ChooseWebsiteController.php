<?php
namespace VigattinAds\Controller\Dashboard\Ads\Create\ChooseWebsite;

use VigattinAds\Controller\Dashboard\Ads\AdsController;
use Zend\View\Model\ViewModel;

class ChooseWebsiteController extends AdsController
{
    const VIGATTIN = 'vigattin.com';
    const VIGATTINTRADE = 'vigattintrade.com';
    const VIGATTINTOURISM = 'vigattintourism.com';
    const VIGATTINDEALS = 'vigattindeals.com';
    const TOURISMBLOGGER = 'vigattin-tourism-article';

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
                $this->sessionManager->getStorage()->tempAdsTemplate = array('showIn' => ChooseWebsiteController::VIGATTIN, 'template' => 'home');
                return $this->redirect()->toRoute('vigattinads_dashboard_ads_create_choose_website_import');
                break;
            case 'trade':
                $this->sessionManager->getStorage()->tempAdsTemplate = array('showIn' => ChooseWebsiteController::VIGATTINTRADE, 'template' => 'home-sidebar-left');
                return $this->redirect()->toRoute('vigattinads_dashboard_ads_create_choose_website_import');
                break;
            case 'tourism':
                $this->sessionManager->getStorage()->tempAdsTemplate = array('showIn' => ChooseWebsiteController::VIGATTINTOURISM, 'template' => 'home-sidebar-right');
                return $this->redirect()->toRoute('vigattinads_dashboard_ads_create_choose_website_import');
                break;
            case 'deals':
                $this->sessionManager->getStorage()->tempAdsTemplate = array('showIn' => ChooseWebsiteController::VIGATTINDEALS, 'template' => 'home-body');
                return $this->redirect()->toRoute('vigattinads_dashboard_ads_create_choose_website_import');
                break;
            case 'tourism-bloggers':
                $this->sessionManager->getStorage()->tempAdsTemplate = array('showIn' => ChooseWebsiteController::TOURISMBLOGGER, 'template' => 'article');
                return $this->redirect()->toRoute('vigattinads_dashboard_ads_create_choose_website', array('controller' => 'blogger-author'));
                break;
            default:
                return $this->redirect()->toRoute('vigattinads_dashboard_ads_create_choose_website_import');
                break;
        }
    }
}
