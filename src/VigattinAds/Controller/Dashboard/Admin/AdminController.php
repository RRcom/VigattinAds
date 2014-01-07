<?php
namespace VigattinAds\Controller\Dashboard\Admin;

use Zend\View\Model\ViewModel;
use VigattinAds\Controller\Dashboard\DashboardController;

class AdminController extends DashboardController
{
    protected $adminView;

    public function indexAction()
    {
        $this->mainView->setVariable('title', 'Dashboard');
        $actionContent = new ViewModel();
        $actionContent->setVariable('controller', strtolower($this->params('controller')));
        $actionContent->setTemplate('vigattinads/view/dashboard/admin/adminView');
        $this->currentTab($actionContent);
        $this->mainView->addChild($actionContent, 'actionContent');
        return $this->mainView;
    }

    protected function currentTab(ViewModel $actionContent)
    {
        $homeView = new ViewModel();
        $homeView->setTemplate('vigattinads/view/dashboard/admin/adminHomeView');
        $actionContent->addChild($homeView);
    }
}
