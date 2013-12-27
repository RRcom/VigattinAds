<?php
namespace VigattinAds\ControllerAction\AccountHome;

use VigattinAds\Controller\AccountHomeController;
use Zend\View\Model\ViewModel;
use VigattinAds\DomainModel\AdsManager;
use VigattinAds\DomainModel\Ads;

class Approval
{
    /**
     * @var \VigattinAds\Controller\AccountHomeController
     */
    protected $accountHomeCtrl;

    /**
     * @var \Zend\View\Model\ViewModel
     */
    protected $viewModel;

    /**
     * @var \VigattinAds\DomainModel\AdsUser
     */
    protected $adsUser;

    /**
     * @var \VigattinAds\DomainModel\AdsManager
     */
    protected $adsManager;

    public function __construct(AccountHomeController $accountHomeCtrl)
    {
        $this->accountHomeCtrl = $accountHomeCtrl;
        $this->adsUser = $this->accountHomeCtrl->getServiceLocator()->get('VigattinAds\DomainModel\UserManager')->getCurrentUser();
        $this->adsUser->refresh();
        $this->adsManager = $this->accountHomeCtrl->getServiceLocator()->get('VigattinAds\DomainModel\AdsManager');
        $this->viewModel = $accountHomeCtrl->getMainView();
    }

    public function process()
    {
        $adsManager = new AdsManager($this->accountHomeCtrl->getServiceLocator());
        $actionContent = new ViewModel();
        $actionContent->setTemplate('vigattinads/view/ads-approval');
        if(strtolower($this->accountHomeCtrl->getRequest()->getPost('result', '')) == 'approved')
        {
            $this->adsManager->changeAdsStatus($this->accountHomeCtrl->getRequest()->getPost('version', ''), Ads::STATUS_APPROVED, $this->accountHomeCtrl->getRequest()->getPost('review_reason', ''));
        }
        elseif(strtolower($this->accountHomeCtrl->getRequest()->getPost('result', '')) == 'disapproved')
        {
            $this->adsManager->changeAdsStatus($this->accountHomeCtrl->getRequest()->getPost('version', ''), Ads::STATUS_DISAPPROVED, $this->accountHomeCtrl->getRequest()->getPost('review_reason', ''));
        }
        $actionContent->setVariable('ads', $adsManager->fetchAdsToReview($this->adsUser));
        $this->viewModel->addChild($actionContent, 'actionContent');
        return $this->viewModel;
    }
}