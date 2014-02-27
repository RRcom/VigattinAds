<?php
namespace VigattinAds\Controller\Dashboard\Ads\Create;

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
            case 'tourism':
                $this->sessionManager->getStorage()->tempAdsTemplate = array('showIn' => 'vigattintourism.com', 'template' => '');
                return $this->redirect()->toRoute('vigattinads_dashboard_ads_create', array('controller' => 'choose-directory'));
                break;
            case 'trade':
                $this->sessionManager->getStorage()->tempAdsTemplate = array('showIn' => 'vigattintrade.com', 'template' => '');
                return $this->redirect()->toRoute('vigattinads_dashboard_ads_create', array('controller' => 'choose-category'));
                break;
            case 'vigattin':
                $this->sessionManager->getStorage()->tempAdsTemplate = array('showIn' => 'vigattin.com', 'template' => '');
                return $this->redirect()->toRoute('vigattinads_dashboard_ads_create', array('controller' => 'choose-category'));
                break;
            default:
                return $this->redirect()->toRoute('vigattinads_dashboard_ads_create', array('controller' => 'choose-website'));
                break;
        }
    }
}
