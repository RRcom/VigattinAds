<?php
namespace VigattinAds\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;

class AccountHomeController extends AbstractActionController
{
    public function indexAction()
    {
        return $this->myProfileAction();
    }

    public function myProfileAction()
    {
        $viewModel = new ViewModel();
        $viewModel->setTemplate('vigattinads/view/account-home');
        return $viewModel;
    }

    public function myAdsAction()
    {
        $viewModel = new ViewModel();
        $viewModel->setTemplate('vigattinads/view/account-home');
        return $viewModel;
    }

    public function onDispatch(MvcEvent $e) {
        $this->layout()->setTemplate('vigattinads/layout/active');
        return parent::onDispatch($e);
    }
}
