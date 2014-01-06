<?php
namespace VigattinAds\Controller\Dashboard\Ads;

use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;
use VigattinAds\Controller\Dashboard\DashboardController;

class AdsController extends DashboardController
{

    public function indexAction()
    {
        $this->mainView->setVariable('title', 'Ads');
        $actionContent = new ViewModel();
        $actionContent->setTemplate('vigattinads/view/dashboard/ads/adsView');
        $actionContent->setVariable('adsList', $this->adsUser->get('ads'));
        $actionContent->setVariable('userManager', $this->userManager);
        $actionContent->setVariable('adsUser', $this->adsUser);
        $this->mainView->addChild($actionContent, 'actionContent');
        return $this->mainView;
    }
}
