<?php
namespace VigattinAds\Controller\Dashboard\Admin;

use Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator;
use VigattinAds\DomainModel\Paginator\HistoryLogArrayResultAdapter;

class AdminHistoryLogController extends AdminController
{
    const PAGINATION_PAGE = 10;

    protected function currentTab(ViewModel $actionContent)
    {
        $currentPage = intval($this->params('page', 1));

        // generate result
        $resultGenerator = new HistoryLogArrayResultAdapter($this->serviceLocator->get('VigattinAds\DomainModel\LogManager'));

        // Paginator
        $paginator = new Paginator($resultGenerator);
        $paginator->setCurrentPageNumber($currentPage);
        $paginator->setItemCountPerPage(self::PAGINATION_PAGE);
        $paginator->setPageRange(7);

        $adminHistoryLogAdsView = new ViewModel();
        $adminHistoryLogAdsView->setTemplate('vigattinads/view/dashboard/admin/adminHistoryLogView');
        $adminHistoryLogAdsView->setVariable('paginator', $paginator);
        $adminHistoryLogAdsView->setVariable('paginationCount', ($currentPage - 1) * self::PAGINATION_PAGE);
        $actionContent->addChild($adminHistoryLogAdsView);
    }
}
