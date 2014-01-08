<?php
namespace VigattinAds\Controller\Dashboard\Admin;

use Zend\View\Model\ViewModel;

class AdminManageAccountController extends AdminController
{
    protected function currentTab(ViewModel $actionContent)
    {
        $adminManageAccountView = new ViewModel();
        $adminManageAccountView->setTemplate('vigattinads/view/dashboard/admin/adminManageAccountView');
        $adminManageAccountView->setVariable('accountList', $this->userManager->getUserList());

        $actionContent->addChild($adminManageAccountView);
    }
}
