<?php
namespace VigattinAds\Controller\Dashboard\Ads;

use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;
use VigattinAds\Controller\Dashboard\DashboardController;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator;


class AdsController extends DashboardController
{

    public function indexAction()
    {
        /** @var \Doctrine\Common\Collections\ArrayCollection $adsList */
        $adsList = $this->adsUser->get('ads');
        $paginator = new Paginator(new Iterator($adsList->getIterator()));
        $paginator->setCurrentPageNumber($this->params()->fromRoute('page'));
        $paginator->setItemCountPerPage(10);
        $paginator->setPageRange(7);

        $this->mainView->setVariable('title', 'Ads');
        $actionContent = new ViewModel();
        $actionContent->setTemplate('vigattinads/view/dashboard/ads/adsView');
        $actionContent->setVariable('adsList', $adsList);
        $actionContent->setVariable('userManager', $this->userManager);
        $actionContent->setVariable('paginator', $paginator);

        $this->mainView->addChild($actionContent, 'actionContent');
        return $this->mainView;
    }
}
