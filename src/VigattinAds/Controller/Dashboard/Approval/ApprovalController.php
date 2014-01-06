<?php
namespace VigattinAds\Controller\Dashboard\Approval;

use Zend\View\Model\ViewModel;
use VigattinAds\Controller\Dashboard\DashboardController;
use VigattinAds\DomainModel\Ads;

class ApprovalController extends DashboardController
{
    public function indexAction()
    {
        $this->mainView->setVariable('title', 'Approval');
        $actionContent = new ViewModel();
        $actionContent->setTemplate('vigattinads/view/dashboard/approval/approvalView');
        if(strtolower($this->getRequest()->getPost('result', '')) == 'approved')
        {
            $this->adsManager->changeAdsStatus($this->getRequest()->getPost('version', ''), Ads::STATUS_APPROVED, $this->getRequest()->getPost('review_reason', ''));
        }
        elseif(strtolower($this->getRequest()->getPost('result', '')) == 'disapproved')
        {
            $this->adsManager->changeAdsStatus($this->getRequest()->getPost('version', ''), Ads::STATUS_DISAPPROVED, $this->getRequest()->getPost('review_reason', ''));
        }
        $actionContent->setVariable('ads', $this->adsManager->fetchAdsToReview($this->adsUser));
        $this->mainView->addChild($actionContent, 'actionContent');
        return $this->mainView;
    }
}
