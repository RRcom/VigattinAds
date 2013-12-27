<?php
namespace VigattinAds\ControllerAction\AccountHome;

use VigattinAds\Controller\AccountHomeController;
use Zend\View\Model\ViewModel;
use VigattinAds\DomainModel\UserManager;
use VigattinAds\DomainModel\AdsUser;
use Doctrine\ORM\Tools\Pagination;

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
     * @var \VigattinAds\DomainModel\UserManager
     */
    protected $userManager;

    /**
     * @var \VigattinAds\DomainModel\AdsUser
     */
    protected $adsUser;

    public function __construct(AccountHomeController $accountHomeCtrl)
    {
        $this->accountHomeCtrl = $accountHomeCtrl;
        $this->viewModel = $accountHomeCtrl->getMainView();
        $this->userManager = $this->accountHomeCtrl->getServiceLocator()->get('VigattinAds\DomainModel\UserManager');
        $this->adsUser = $this->userManager->getCurrentUser();
    }

    public function process()
    {
        /** @var  $ads \Doctrine\Common\Collections\ArrayCollection */
        $ads = $this->adsUser->get('ads');
        $actionContent = new ViewModel();
        $actionContent->setTemplate('vigattinads/view/account-home-ads');
        $actionContent->setVariable('adsList', $ads);
        $actionContent->setVariable('userManager', $this->userManager);
        $actionContent->setVariable('adsUser', $this->adsUser);
        $this->viewModel->addChild($actionContent, 'actionContent');
        return $this->viewModel;
    }
}