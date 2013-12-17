<?php
namespace VigattinAds\ControllerAction\AccountHome;

use VigattinAds\Controller\AccountHomeController;
use Zend\View\Model\ViewModel;

class ViewAds
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

    /**
     * @var \Zend\View\Model\ViewModel
     */
    protected $actionContent;
    
    /**
     * @var \Zend\Session\SessionManager
     */
    protected $sessionManager;

    public function __construct(AccountHomeController $accountHomeCtrl)
    {
        $this->accountHomeCtrl = $accountHomeCtrl;
        $this->viewModel = $accountHomeCtrl->getMainView();
        $this->userManager = $this->accountHomeCtrl->getServiceLocator()->get('VigattinAds\DomainModel\UserManager');
        $this->actionContent = new ViewModel();
        $this->actionContent->setTemplate('vigattinads/view/view-ads');
        $this->sessionManager = $this->accountHomeCtrl->getServiceLocator()->get('Zend\Session\SessionManager');
    }

    public function process()
    {
        $actionContent = $this->action();
        $this->viewModel->addChild($actionContent, 'actionContent');
        return $this->viewModel;
    }

    public function action()
    {
        $adsId = $this->accountHomeCtrl->params('param2', '');
        $adsUser = $this->userManager->getCurrentUser();
        $adsUser->refresh();
        $adsViewCount = 0;
        $adsEntity = $adsUser->getSingleAds($adsId);
        if($adsEntity instanceof \VigattinAds\DomainModel\Ads) $adsViewCount = $adsEntity->get('adsView')->count();

        $formError = array(
            'adsImageError' => '',
            'adsTitleError' => '',
            'adsUrlError' => '',
            'adsKeywordError' => '',
            'adsDescriptionError' => '',
        );
        $this->actionContent->setVariables($formError);
        $this->actionContent->setVariable('ads', $adsEntity);
        $this->actionContent->setVariable('userManager', $this->userManager);
        $this->actionContent->setVariable('adsUser', $adsUser);
        $this->actionContent->setVariable('adsViewCount', $adsViewCount);
        return $this->actionContent;
    }
}