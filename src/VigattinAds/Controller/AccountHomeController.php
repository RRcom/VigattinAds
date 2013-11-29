<?php
namespace VigattinAds\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;
use Zend\View\ViewEvent;
use VigattinAds\ControllerAction\AccountHome;

class AccountHomeController extends AbstractActionController
{
    protected $mainView;

    public function __construct()
    {
        $this->mainView = new ViewModel();
        $this->mainView->setTemplate('vigattinads/view/account-home');
    }

    public function indexAction()
    {
        $this->mainView->setVariable('title', 'Dashboard');
        $dashBoard = new AccountHome\Dashboard($this);
        return $dashBoard->process();
    }

    public function profileAction()
    {
        $this->mainView->setVariable('title', 'My Profile');
        $dashBoard = new AccountHome\Profile($this);
        return $dashBoard->process();
    }

    public function adsAction()
    {
        $this->mainView->setVariable('title', 'My Ads');
        $dashBoard = new AccountHome\Ads($this);
        return $dashBoard->process();
    }

    public function adminAction()
    {
        $this->mainView->setVariable('title', 'Admin');
        $dashBoard = new AccountHome\Admin($this);
        return $dashBoard->process();
    }

    public function onDispatch(MvcEvent $e) {
        $this->layout()->setTemplate('vigattinads/layout/active');
        $this->mainView->setVariable('action', strtolower($this->params('action')));
        return parent::onDispatch($e);
    }

    /**
     * @return ViewModel
     */

    public function getMainView()
    {
        return $this->mainView;
    }
}
