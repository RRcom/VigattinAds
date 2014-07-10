<?php
namespace VigattinAds\Controller\Dashboard\Ads\Create;

use VigattinAds\Controller\Dashboard\Ads\AdsController;
use Zend\View\Model\ViewModel;

class CreateController extends AdsController
{
    public function indexAction()
    {
        $actionContent = new ViewModel();
        $this->mainView->setVariable('title', 'Create new ads wizard');
        $actionContent->setTemplate('vigattinads/view/dashboard/ads/create/createView');
        $this->mainView->addChild($actionContent, 'actionContent');
        return $this->mainView;
        //return $this->redirect()->toRoute('vigattinads_dashboard_ads_create', array('controller' => 'choose-website'));
    }
}
