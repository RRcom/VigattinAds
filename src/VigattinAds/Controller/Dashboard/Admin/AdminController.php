<?php
namespace VigattinAds\Controller\Dashboard\Admin;

use Zend\View\Model\ViewModel;
use VigattinAds\Controller\Dashboard\DashboardController;
use VigattinAds\DomainModel\AdsUser;

class AdminController extends DashboardController
{
    protected $adminView;

    public function indexAction()
    {
        if(!$this->adsUser->hasPermit(AdsUser::PERMIT_ADMIN_ACCESS))
        {
            header('Location: /vigattinads');
            exit();
        }
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
