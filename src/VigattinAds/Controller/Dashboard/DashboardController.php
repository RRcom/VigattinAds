<?php
namespace VigattinAds\Controller\Dashboard;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;
use VigattinAds\DomainModel\SettingsManager;
use VigattinAds\DomainModel\Navigation;
use VigattinAds\DomainModel\AdsManager;

class DashboardController extends AbstractActionController
{
    /** @var \VigattinAds\DomainModel\SettingsManager */
    protected $settingsManager;

    /** @var \Zend\View\Model\ViewModel */
    protected $mainView;

    /** @var \VigattinAds\DomainModel\UserManager */
    protected $userManager;

    /** @var \VigattinAds\DomainModel\AdsUser */
    protected $adsUser;

    /** @var \VigattinAds\DomainModel\AdsManager */
    protected $adsManager;

    /** @var \Zend\Session\SessionManager */
    protected $sessionManager;

    /** @var \Vigattin\Vauth\Vauth */
    protected $vauth;

    public function indexAction()
    {
        $activeTab = strtolower($this->params('param', 'vigattintrade'));

        $this->mainView->setVariable('title', 'Dashboard');
        $actionContent = new ViewModel();
        $actionContent->setTemplate('vigattinads/view/dashboard/dashboardHomeView');
        $actionContent->setVariable('firstName', $this->adsUser->get('firstName'));
        $actionContent->setVariable('lastName', $this->adsUser->get('lastName'));
        $actionContent->setVariable('credit', $this->adsUser->get('credit'));
        $actionContent->setVariable('approvedAds', $this->adsUser->getApprovedAds());
        $actionContent->setVariable('pendingAds', $this->adsUser->getPendingAds());
        $actionContent->setVariable('disapprovedAds', $this->adsUser->getDisapprovedAds());
        $actionContent->setVariable('activeTab', $activeTab);
        $this->mainView->addChild($actionContent, 'actionContent');
        return $this->mainView;
    }

    public function onDispatch(MvcEvent $e)
    {
        $this->sessionManager = $this->serviceLocator->get('Zend\Session\SessionManager');
        $this->settingsManager = new SettingsManager($this->serviceLocator);
        $this->userManager = $this->serviceLocator->get('VigattinAds\DomainModel\UserManager');
        $this->adsUser = $this->userManager->getCurrentUser();
        $this->adsManager = new AdsManager($this->serviceLocator);
        $this->vauth = $this->serviceLocator->get('Vigattin\Vauth\Vauth');
        $this->adsUser->refresh();

        // Set global layout
        $this->layout()->setTemplate('vigattinads/layout/active');
        $this->layout()->setVariable('js', 'var viewToGoldRate = '.$this->settingsManager->get('viewToGoldRate').';');
        $this->layout()->setVariable('config', $this->serviceLocator->get('config'));

        // set main layout
        $this->mainView = new ViewModel();
        $this->mainView->setTemplate('vigattinads/view/dashboard/dashboardView');
        $this->mainView->setVariable('breadCrumbs', Navigation::breadcrumbs());
        $this->mainView->setVariable('user', $this->userManager->getCurrentUser());
        $this->mainView->setVariable('action', strtolower($this->params('action')));
        $this->mainView->setVariable('controller', strtolower($this->params('controller')));

        // return parent
        return parent::onDispatch($e);
    }
}
