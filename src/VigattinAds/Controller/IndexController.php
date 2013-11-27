<?php
namespace VigattinAds\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        return $this->loginPage();
    }

    protected function loginPage()
    {
        $viewModel = new ViewModel();
        $viewModel->setTemplate('vigattinads/view/login');

        return $viewModel;
    }

    public function onDispatch(MvcEvent $e) {
        $this->layout()->setTemplate('vigattinads/layout/default');
        return parent::onDispatch($e);
    }
}
