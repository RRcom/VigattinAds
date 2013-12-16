<?php
namespace VigattinAds\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;
use Zend\View\ViewEvent;
use VigattinAds\ControllerAction\AccountHome;
use VigattinAds\DomainModel\Navigation;

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
        $this->mainView->setVariable('title', 'Profile Maintenance');
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
                    case 'template':
                        $this->mainView->setVariable('title', 'Please choose which ads space to use.');
                        $wizardChooseTemplte = new AccountHome\WizardChooseTemplate($this);
                        return $wizardChooseTemplte->process();
                        break;
                    case 'edit':
                        $this->mainView->setVariable('title', 'Edit your ads information');
                        $wizardEditInfo = new AccountHome\WizardEditInfo($this);
                        return $wizardEditInfo->process();
                        break;
                    case 'image':
                        $this->mainView->setVariable('title', 'Upload your ads image.');
                        $wizardChooseTemplte = new AccountHome\WizardUploadImage($this);
                        return $wizardChooseTemplte->process();
                        break;
                    default:
                        $this->mainView->setVariable('title', 'Please choose which ads space to use.');
                        $wizardChooseTemplte = new AccountHome\WizardChooseTemplate($this);
                        return $wizardChooseTemplte->process();
                        break;
                }
                break;
            case 'edit':
                $this->mainView->setVariable('title', 'Edit Ads');
                $editAds = new AccountHome\EditAds($this);
                return $editAds->process();
                break;
            case 'view':
                $this->mainView->setVariable('title', 'View ads full details');
                $viewAds = new AccountHome\ViewAds($this);
                return $viewAds->process();
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
