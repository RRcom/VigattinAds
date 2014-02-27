<?php
namespace VigattinAds\Controller\Dashboard\Ads\Create;

use VigattinAds\Controller\Dashboard\Ads\AdsController;

class CreateController extends AdsController
{
    public function indexAction()
    {
        return $this->redirect()->toRoute('vigattinads_dashboard_ads_create', array('controller' => 'choose-website'));
    }
}
