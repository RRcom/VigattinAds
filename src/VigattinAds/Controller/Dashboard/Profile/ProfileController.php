<?php
namespace VigattinAds\Controller\Dashboard\Profile;

use Zend\View\Model\ViewModel;
use VigattinAds\Controller\Dashboard\DashboardController;

class ProfileController extends DashboardController
{
    public function indexAction()
    {
        $this->mainView->setVariable('title', 'Profile');
        $actionContent = new ViewModel();
        $actionContent->setTemplate('vigattinads/view/dashboard/profile/profileView');
        $this->mainView->addChild($actionContent, 'actionContent');
        return $this->mainView;
    }
}
