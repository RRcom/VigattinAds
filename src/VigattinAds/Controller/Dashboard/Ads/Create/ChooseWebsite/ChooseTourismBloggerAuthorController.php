<?php
namespace VigattinAds\Controller\Dashboard\Ads\Create\ChooseWebsite;

use VigattinAds\Controller\Dashboard\Ads\AdsController;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

class ChooseTourismBloggerAuthorController extends AdsController
{
    public function indexAction()
    {
        $actionContent = new ViewModel();
        $this->mainView->setVariable('title', 'Step 2. select author');
        $actionContent->setTemplate('vigattinads/view/dashboard/ads/create/chooseTourismBloggerAuthorView');
        $this->mainView->addChild($actionContent, 'actionContent');
        return $this->mainView;
    }
}
