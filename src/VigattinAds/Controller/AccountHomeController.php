<?php
namespace VigattinAds\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;
use Zend\View\ViewEvent;
use VigattinAds\ControllerAction\AccountHome;
use VigattinAds\Model\Helper\Navigation;

class AccountHomeController extends AbstractActionController
{
    protected $mainView;

    public function __construct()
    {
        $this->mainView = new ViewModel();
        $this->mainView->setTemplate('vigattinads/view/account-home');
        $this->mainView->setVariable('breadCrumbs', Navigation::breadcrumbs());
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
        $param1 = trim($this->params('param1', ''));
        $param2 = trim($this->params('param2', ''));
        switch(strtolower($param1))
        {
            case 'wizard':
                switch($param2)
                {
                    default:
                        $this->mainView->setVariable('title', 'Ads Wizard / Edit Info');
                        $wizardEditInfo = new AccountHome\WizardEditInfo($this);
                        return $wizardEditInfo->process();
                        break;
                }
                break;
            default:
                $this->mainView->setVariable('title', 'My Ads');
                $myAds = new AccountHome\Ads($this);
                return $myAds->process();
                break;
        }
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
