<?php
namespace VigattinAds\ControllerAction\AccountHome;

use VigattinAds\Controller\AccountHomeController;
use VigattinAds\DomainModel\AdsUser;
use Zend\View\Model\ViewModel;

class Profile
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

    public function __construct(AccountHomeController $accountHomeCtrl)
    {
        $this->accountHomeCtrl = $accountHomeCtrl;
        $this->adsUser = $this->accountHomeCtrl->getServiceLocator()->get('VigattinAds\DomainModel\UserManager')->getCurrentUser();
        $this->viewModel = $accountHomeCtrl->getMainView();
    }

    public function process()
    {
        $actionContent = new ViewModel();
        $this->setUserInfo($actionContent);
        $actionContent->setTemplate('vigattinads/view/account-home-profile');
        $this->viewModel->addChild($actionContent, 'actionContent');
        return $this->viewModel;
    }

    public function setUserInfo(ViewModel $actionContent)
    {
        $actionContent->setVariable('firstName', $this->adsUser->get('firstName'));
        $actionContent->setVariable('lastName', $this->adsUser->get('lastName'));
        $actionContent->setVariable('credit', $this->adsUser->get('credit'));
    }
}