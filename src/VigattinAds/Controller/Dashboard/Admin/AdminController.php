<?php
namespace VigattinAds\Controller\Dashboard\Admin;

use Zend\View\Model\ViewModel;
use VigattinAds\Controller\Dashboard\DashboardController;

class AdminController extends DashboardController
{
    public function indexAction()
    {
        $this->mainView->setVariable('title', 'Dashboard');
        $actionContent = new ViewModel();
        $actionContent->setTemplate('vigattinads/view/dashboard/admin/adminView');
        $this->mainView->addChild($actionContent, 'actionContent');
        return $this->mainView;
    }
}
