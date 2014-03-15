<?php
namespace VigattinAds\Controller\Dashboard\Admin;

use Zend\View\Model\ViewModel;
use VigattinAds\DomainModel\AdsManager;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator;
use VigattinAds\DomainModel\Paginator\AdsSearchArrayResultAdapter;

class AdminManageAdsController extends AdminController
{
    protected function currentTab(ViewModel $actionContent)
    {
        $adminManageAdsView = new ViewModel();

        // if request for delete user
        $this->catchDeleteRequest();

        // generate result
        $resultGenerator = new AdsSearchArrayResultAdapter($this->adsManager);

        // catch refine search request
        $this->catchRefineSearch($resultGenerator, $adminManageAdsView);

        // Paginator
        $paginator = new Paginator($resultGenerator);
        $paginator->setCurrentPageNumber(intval($this->params('page', 0)));
        $paginator->setItemCountPerPage(10);
        $paginator->setPageRange(7);

        $adminManageAdsView->setTemplate('vigattinads/view/dashboard/admin/adminManageAdsView');
        $adminManageAdsView->setVariable('paginator', $paginator);
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
        }
        elseif(strtolower($this->params('param1')) == 'reset') {
            $this->sessionManager->getStorage()->adsSearchCategory = $this->getRequest()->getPost('adsSearchCategory', 'Show All');
            $this->sessionManager->getStorage()->adsSearchValue = $this->getRequest()->getPost('adsSearchValue', '');
            return $this->redirect()->toRoute('vigattinads_dashboard_admin_manageads');
        }

        if(!$this->sessionManager->getStorage()->adsSearchCategory) $this->sessionManager->getStorage()->adsSearchCategory = 'Show All';

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

        $adminManageAdsView->setVariable('adsSearchCategory', $this->sessionManager->getStorage()->adsSearchCategory);
        $adminManageAdsView->setVariable('adsSearchValue', $this->sessionManager->getStorage()->adsSearchValue);
        return true;
    }
}