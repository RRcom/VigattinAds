<?php
namespace VigattinAds\Controller\Dashboard\Admin;

use Zend\View\Model\ViewModel;
use VigattinAds\DomainModel\UserManager;

class AdminManageAccountController extends AdminController
{
    protected function currentTab(ViewModel $actionContent)
    {
        if($this->params('param1') == 'delete') {
            $user = $this->userManager->getUser($this->params('param2'));
            if($user instanceof \VigattinAds\DomainModel\AdsUser) {
                // check if not same user as the current login user
                if($user->get('id') != $this->adsUser->get('id')) {
                    $this->userManager->deleteUser($user);
                    $this->userManager->flush();
                }
            }
        }
        $adminManageAccountView = new ViewModel();
        $adminManageAccountView->setTemplate('vigattinads/view/dashboard/admin/adminManageAccountView');
        $adminManageAccountView->setVariable('accountList', $this->userManager->getUserList(UserManager::SORT_BY_ID, UserManager::SORT_DIRECTION_ASC));
        $actionContent->addChild($adminManageAccountView);
    }
}
