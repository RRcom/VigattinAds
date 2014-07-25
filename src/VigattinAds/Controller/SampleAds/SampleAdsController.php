<?php
namespace VigattinAds\Controller\SampleAds;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class SampleAdsController extends AbstractActionController
{
    public function indexAction()
    {
        $viewModel = new ViewModel();
        $viewModel->setTemplate('vigattinads/view/sampleads/sample-ads-view');
        return $viewModel;
    }
}
