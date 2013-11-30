<?php
namespace VigattinAds\ControllerAction\AccountHome;

use VigattinAds\Controller\AccountHomeController;
use Zend\View\Model\ViewModel;

class Ads
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
     * @var $user \VigattinAds\Model\User\User
     */
    protected $userModel;

    public function __construct(AccountHomeController $accountHomeCtrl)
    {
        $this->accountHomeCtrl = $accountHomeCtrl;
        $this->viewModel = $accountHomeCtrl->getMainView();
        $this->userModel = $this->accountHomeCtrl->getServiceLocator()->get('VigattinAds\Model\User\User');
    }

    public function process()
    {
        $actionContent = new ViewModel();
        $actionContent->setTemplate('vigattinads/view/account-home-ads');
        $actionContent->setVariable('adsList', $this->getAdsList());
        $actionContent->setVariable('userModel', $this->userModel);
        $this->viewModel->addChild($actionContent, 'actionContent');
        return $this->viewModel;
    }

    public function getAdsList()
    {
        $adsList = $this->userModel->getAds()->listAds();
        return $adsList;
    }
}