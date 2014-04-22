<?php
namespace VigattinAds\Controller\Dashboard\Admin;

use Zend\View\Model\ViewModel;
use VigattinAds\DomainModel\UserManager;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator;
use VigattinAds\DomainModel\Paginator\ArrayResultAdapter;
use VigattinAds\DomainModel\VauthAccountLocator;

class AdminManageAccountController extends AdminController
{
    const PAGINATION_PAGE = 10;

    protected function currentTab(ViewModel $actionContent)
    {
        $currentPage = intval($this->params('page', 1));

        $adminManageAccountView = new ViewModel();

        // if request for delete user
        $this->catchDeleteRequest();

        // generate result
        $resultGenerator = new ArrayResultAdapter($this->userManager);

        // catch refine search request
        $this->catchRefineSearch($resultGenerator, $adminManageAccountView);

        // Paginator
        $paginator = new Paginator($resultGenerator);
        $paginator->setCurrentPageNumber($currentPage);
        $paginator->setItemCountPerPage(self::PAGINATION_PAGE);
        $paginator->setPageRange(7);

        $adminManageAccountView->setTemplate('vigattinads/view/dashboard/admin/adminManageAccountView');
        $adminManageAccountView->setVariable('paginator', $paginator);
        $adminManageAccountView->setVariable('paginationCount', ($currentPage - 1) * self::PAGINATION_PAGE);
        $actionContent->addChild($adminManageAccountView);
    }

    public function catchDeleteRequest()
    {
        if($this->params('param1') == 'delete') {
            $user = $this->userManager->getUser($this->params('param2'));
            if($user instanceof \VigattinAds\DomainModel\AdsUser) {
                // check if not same user as the current login user
                if($user->get('id') != $this->adsUser->get('id')) {
                    $vauthAccountLocator = new VauthAccountLocator();
                    $vauthAccountLocator->set('serviceManager', $this->serviceLocator);
                    $vauthAccountLocator->removeAccount($user->get('id'));
                    $this->userManager->deleteUser($user);
                    $this->userManager->flush();
                }
            }
        }
    }

    function catchRefineSearch(ArrayResultAdapter $resultGenerator, ViewModel $adminManageAccountView)
    {
        if($this->getRequest()->getPost('userSearch', '')) {
            $this->sessionManager->getStorage()->userSearchCategory = $this->getRequest()->getPost('userSearchCategory', 'Show All');
            $this->sessionManager->getStorage()->userSearchValue = $this->getRequest()->getPost('userSearchValue', '');
        }
        elseif(strtolower($this->params('param1')) == 'reset') {
            $this->sessionManager->getStorage()->userSearchCategory = $this->getRequest()->getPost('userSearchCategory', 'Show All');
            $this->sessionManager->getStorage()->userSearchValue = $this->getRequest()->getPost('userSearchValue', '');
            return $this->redirect()->toRoute('vigattinads_dashboard_admin_manageaccount');
        }

        if(!$this->sessionManager->getStorage()->userSearchCategory) $this->sessionManager->getStorage()->userSearchCategory = 'Show All';

        $categoryMap = array(
            'username' => UserManager::SEARCH_BY_USERNAME,
            'email' => UserManager::SEARCH_BY_EMAIL,
            'first name' => UserManager::SEARCH_BY_FIRST_NAME,
            'last name' => UserManager::SEARCH_BY_LAST_NAME,
            'id' => UserManager::SEARCH_BY_ID,
            'show all' => UserManager::SEARCH_BY_ALL,
        );

        $category = empty($categoryMap[strtolower($this->sessionManager->getStorage()->userSearchCategory)]) ? UserManager::SEARCH_BY_ALL : $categoryMap[strtolower($this->sessionManager->getStorage()->userSearchCategory)];

        $resultGenerator->setSearchFiled($category);
        $resultGenerator->setSearchValue($this->sessionManager->getStorage()->userSearchValue);

        $adminManageAccountView->setVariable('userSearchCategory', $this->sessionManager->getStorage()->userSearchCategory);
        $adminManageAccountView->setVariable('userSearchValue', $this->sessionManager->getStorage()->userSearchValue);
        return true;
    }
}
