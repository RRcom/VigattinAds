<?php
namespace VigattinAds\Controller\Dashboard\Admin;

use Zend\View\Model\ViewModel;
use VigattinAds\DomainModel\AdsManager;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator;
use VigattinAds\DomainModel\Paginator\AdsSearchArrayResultAdapter;

class AdminManageAdsController extends AdminController
{
    const PAGINATION_PAGE = 10;

    protected function currentTab(ViewModel $actionContent)
    {
        $currentPage = intval($this->params('page', 1));

        $adminManageAdsView = new ViewModel();

        // if request for delete user
        $this->catchDeleteRequest();

        // generate result
        $resultGenerator = new AdsSearchArrayResultAdapter($this->adsManager);

        // catch refine search request
        $this->catchRefineSearch($resultGenerator, $adminManageAdsView);

        // Paginator
        $paginator = new Paginator($resultGenerator);
        $paginator->setCurrentPageNumber($currentPage);
        $paginator->setItemCountPerPage(self::PAGINATION_PAGE);
        $paginator->setPageRange(7);

        $adminManageAdsView->setTemplate('vigattinads/view/dashboard/admin/adminManageAdsView');
        $adminManageAdsView->setVariable('paginator', $paginator);
        $adminManageAdsView->setVariable('paginationCount', ($currentPage - 1) * self::PAGINATION_PAGE);
        $actionContent->addChild($adminManageAdsView);
    }

    public function catchDeleteRequest()
    {

    }

    function catchRefineSearch(AdsSearchArrayResultAdapter $resultGenerator, ViewModel $adminManageAdsView)
    {
        if($this->getRequest()->getPost('adsSearch', '')) {
            $this->sessionManager->getStorage()->adsSearchCategory = $this->getRequest()->getPost('adsSearchCategory', 'Show All');
            $this->sessionManager->getStorage()->adsSearchValue = $this->getRequest()->getPost('adsSearchValue', '');
            $this->sessionManager->getStorage()->adsSearchFilter = $this->getRequest()->getPost('searchFilter', 100);
        }
        elseif(strtolower($this->params('param1')) == 'reset') {
            $this->sessionManager->getStorage()->adsSearchCategory = $this->getRequest()->getPost('adsSearchCategory', 'Show All');
            $this->sessionManager->getStorage()->adsSearchValue = $this->getRequest()->getPost('adsSearchValue', '');
            $this->sessionManager->getStorage()->adsSearchFilter = $this->getRequest()->getPost('searchFilter', 100);
            return $this->redirect()->toRoute('vigattinads_dashboard_admin_manageads');
        }

        if(!$this->sessionManager->getStorage()->adsSearchCategory) $this->sessionManager->getStorage()->adsSearchCategory = 'Show All';
        if($this->sessionManager->getStorage()->adsSearchFilter === null) $this->sessionManager->getStorage()->adsSearchFilter = 100;

        $categoryMap = array(
            'title' => AdsManager::SEARCH_BY_TITLE,
            'username' => AdsManager::SEARCH_BY_USERNAME,
            'email' => AdsManager::SEARCH_BY_EMAIL,
            'first name' => AdsManager::SEARCH_BY_FIRST_NAME,
            'last name' => AdsManager::SEARCH_BY_LAST_NAME,
            'id' => AdsManager::SEARCH_BY_ID,
            'show all' => AdsManager::SEARCH_BY_ALL,
        );

        $category = empty($categoryMap[strtolower($this->sessionManager->getStorage()->adsSearchCategory)]) ? AdsManager::SEARCH_BY_ALL : $categoryMap[strtolower($this->sessionManager->getStorage()->adsSearchCategory)];

        $resultGenerator->setSearchFiled($category);
        $resultGenerator->setSearchValue($this->sessionManager->getStorage()->adsSearchValue);
        $resultGenerator->setSearchFilter($this->sessionManager->getStorage()->adsSearchFilter);

        $adminManageAdsView->setVariable('adsSearchCategory', $this->sessionManager->getStorage()->adsSearchCategory);
        $adminManageAdsView->setVariable('adsSearchValue', $this->sessionManager->getStorage()->adsSearchValue);
        $adminManageAdsView->setVariable('adsSearchFilter', $this->sessionManager->getStorage()->adsSearchFilter);
        return true;
    }
}
