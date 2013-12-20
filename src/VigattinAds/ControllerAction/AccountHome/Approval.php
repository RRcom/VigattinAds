<?php
namespace VigattinAds\ControllerAction\AccountHome;

use VigattinAds\Controller\AccountHomeController;
use Zend\View\Model\ViewModel;

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
        $this->adsManager = $this->accountHomeCtrl->getServiceLocator()->get('VigattinAds\DomainModel\AdsManager');
        $this->viewModel = $accountHomeCtrl->getMainView();
    }

    public function process()
    {
        $actionContent = new ViewModel();
        $actionContent->setTemplate('vigattinads/view/ads-approval');
        $this->viewModel->addChild($actionContent, 'actionContent');
        return $this->viewModel;
    }
}