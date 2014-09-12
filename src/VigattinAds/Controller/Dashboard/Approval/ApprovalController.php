<?php
namespace VigattinAds\Controller\Dashboard\Approval;

use VigattinAds\DomainModel\AdsManager;
use Zend\View\Model\ViewModel;
use VigattinAds\Controller\Dashboard\DashboardController;
use VigattinAds\DomainModel\Ads;
use VigattinAds\DomainModel\AdsUser;
use VigattinAds\DomainModel\Paginator\AdsSearchArrayResultAdapter;
use Zend\Paginator\Paginator;

class ApprovalController extends DashboardController
{
    const PAGINATION_PAGE = 10;

    public function indexAction()
    {
        if(!$this->adsUser->hasPermit(AdsUser::PERMIT_TO_APPROVE_ADS))
        {
            header('Location: /vigattinads');
            exit();
        }

        // generate result
        $resultGenerator = new AdsSearchArrayResultAdapter($this->adsManager);
        $this->setAdsSearchParam($resultGenerator);

        // Paginator
        $paginator = new Paginator($resultGenerator);
        $paginator->setCurrentPageNumber(intval($this->params('page', 1)));
        $paginator->setItemCountPerPage(self::PAGINATION_PAGE);
        $paginator->setPageRange(7);

        $this->mainView->setVariable('title', 'Approval');
        $actionContent = new ViewModel();
        $actionContent->setTemplate('vigattinads/view/dashboard/approval/approval2View');
        if(strtolower($this->getRequest()->getPost('result', '')) == 'approved')
        {
            $this->adsManager->changeAdsStatus($this->getRequest()->getPost('version', ''), Ads::STATUS_APPROVED, $this->getRequest()->getPost('review_reason', ''));
        }
        elseif(strtolower($this->getRequest()->getPost('result', '')) == 'disapproved')
        {
            $this->adsManager->changeAdsStatus($this->getRequest()->getPost('version', ''), Ads::STATUS_DISAPPROVED, $this->getRequest()->getPost('review_reason', ''));
        }
        $actionContent->setVariable('paginator', $paginator);
        $actionContent->setVariable('ads', $this->adsManager->fetchAdsToReview($this->adsUser));
        $actionContent->setVariable('totalPending', $this->adsManager->countPendingAds());
        $this->mainView->addChild($actionContent, 'actionContent');
        return $this->mainView;
    }

    protected function setAdsSearchParam(AdsSearchArrayResultAdapter $resultGenerator)
    {
        $resultGenerator->setSearchFiled(AdsManager::SEARCH_BY_ALL);
        $resultGenerator->setSearchFilter(Ads::STATUS_PENDING);
    }
}
