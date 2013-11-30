<?php
namespace VigattinAds\ControllerAction\AccountHome;

use VigattinAds\Controller\AccountHomeController;
use Zend\View\Model\ViewModel;

class Admin
{
    /**
     * @var \VigattinAds\Controller\AccountHomeController
     */
    protected $accountHomeCtrl;

    /**
     * @var \Zend\View\Model\ViewModel
     */
    protected $viewModel;

    public function __construct(AccountHomeController $accountHomeCtrl)
    {
        $this->accountHomeCtrl = $accountHomeCtrl;
        $this->viewModel = $accountHomeCtrl->getMainView();
    }

    public function process()
    {
        $actionContent = new ViewModel();
        $actionContent->setTemplate('vigattinads/view/account-home-admin');
        $this->viewModel->addChild($actionContent, 'actionContent');
        return $this->viewModel;
    }
}