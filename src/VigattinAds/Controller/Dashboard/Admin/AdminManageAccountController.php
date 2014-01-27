<?php
namespace VigattinAds\Controller\Dashboard\Admin;

use Zend\View\Model\ViewModel;
use VigattinAds\DomainModel\UserManager;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator;
use VigattinAds\DomainModel\Paginator\ArrayResultAdapter;

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

        // Paginator
        $paginator = new Paginator(new ArrayResultAdapter($this->userManager));
        $paginator->setCurrentPageNumber(intval($this->params('page', 0)));
        $paginator->setItemCountPerPage(10);
        $paginator->setPageRange(7);

        $adminManageAccountView = new ViewModel();
        $adminManageAccountView->setTemplate('vigattinads/view/dashboard/admin/adminManageAccountView');
        $adminManageAccountView->setVariable('paginator', $paginator);
        $actionContent->addChild($adminManageAccountView);
    }
}
