<?php
namespace VigattinAds\Controller\Dashboard\Admin;

use Zend\View\Model\ViewModel;

class AdminManageAccountController extends AdminController
{
    protected function currentTab(ViewModel $actionContent)
    {
        $homeView = new ViewModel();
        $homeView->setTemplate('vigattinads/view/dashboard/admin/adminManageAccountView');
        $actionContent->addChild($homeView);
    }
}
