<?php
namespace VigattinAds\ControllerAction\AccountHome;

use VigattinAds\Controller\AccountHomeController;
use Zend\View\Model\ViewModel;

class Ads
{
    /**
     * @var \VigattinAds\Controller\AccountHomeController
     */
    protected $accountHomeCtrl;

    /**
     * @var \Zend\View\Model\ViewModel
     */
    protected $viewModel;

    /**
     * @var $user \VigattinAds\DomainModel\UserManager
     */
    protected $userManager;

    public function __construct(AccountHomeController $accountHomeCtrl)
    {
        $this->accountHomeCtrl = $accountHomeCtrl;
        $this->viewModel = $accountHomeCtrl->getMainView();
        $this->userManager = $this->accountHomeCtrl->getServiceLocator()->get('VigattinAds\DomainModel\UserManager');
    }

    public function process()
    {
        $actionContent = new ViewModel();
        $actionContent->setTemplate('vigattinads/view/account-home-ads');
        $actionContent->setVariable('adsList', $this->getAdsList());
        $actionContent->setVariable('userManager', $this->userManager);
        $this->viewModel->addChild($actionContent, 'actionContent');
        return $this->viewModel;
    }

    public function getAdsList()
    {
        /** @var $user \VigattinAds\DomainModel\AdsUser; */
        $adsUser = $this->userManager->getCurrentUser();
        /** @var $adsList \Doctrine\Common\Collections\ArrayCollection */
        $adsList = $adsUser->get('ads');
        return $adsList;
    }
}