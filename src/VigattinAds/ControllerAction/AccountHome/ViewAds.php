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
     * @var $user \VigattinAds\Model\User\User
     */
    protected $userModel;

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
        $this->userModel = $this->accountHomeCtrl->getServiceLocator()->get('VigattinAds\Model\User\User');
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
        $adsModel = $this->userModel->getAds();
        $adsEntity = $adsModel->getAds($adsId);
        $formError = array(
            'adsImageError' => '',
            'adsTitleError' => '',
            'adsUrlError' => '',
            'adsKeywordError' => '',
            'adsDescriptionError' => '',
        );
        $this->actionContent->setVariables($formError);
        $this->actionContent->setVariable('ads', $adsEntity);
        return $this->actionContent;
    }
}