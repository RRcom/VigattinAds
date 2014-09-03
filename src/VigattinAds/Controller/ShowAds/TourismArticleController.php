<?php
namespace VigattinAds\Controller\ShowAds;

use VigattinAds\Controller\ShowAds\ShowAdsController;
use VigattinAds\DomainModel\AdsManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;

class TourismArticleController extends ShowAdsController
{
    public function indexAction()
    {
        // default action
        return $this->headAction();
    }

    public function headAction()
    {
        // use this view to show the ads
        $this->viewModel->setTemplate('vigattinads/view/show-tourism-ads-header');
        $this->viewModel->setVariable('ads', $this->generateAds());
        return $this->viewModel;
    }

    public function sideAction()
    {
        // use this view to show the ads
        $this->viewModel->setTemplate('vigattinads/view/show-ads-sidebar-fbstyle');
        $this->viewModel->setVariable('ads', $this->generateAds());
        return $this->viewModel;
    }

    public function sideLargeAction()
    {
        // use this view to show the ads
        $this->viewModel->setTemplate('vigattinads/view/show-ads-side-large');
        $this->viewModel->setVariable('ads', $this->generateAds());
        return $this->viewModel;
    }

    public function onDispatch(MvcEvent $e)
    {
        $parent = parent::onDispatch($e);
        $this->layout()->setTemplate('vigattinads/layout/ads-no-padding');
        return $parent;
    }


}
