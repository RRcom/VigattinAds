<?php
namespace VigattinAds\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;

class ShowAdsController extends AbstractActionController
{
    public $viewModel;

    public function __construct()
    {
        $this->viewModel = new ViewModel();
        $this->viewModel->setTemplate('vigattinads/view/show-ads');
    }

    public function indexAction()
    {
        $this->viewModel->setVariable('actionContent', 'ads');
        return $this->viewModel;
    }

    public function onDispatch(MvcEvent $e) {
        $this->layout()->setTemplate('vigattinads/layout/ads');
        return parent::onDispatch($e);
    }
}
