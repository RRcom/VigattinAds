<?php
namespace VigattinAds\ControllerAction\AccountHome;

use VigattinAds\Controller\AccountHomeController;
use Zend\View\Model\ViewModel;

class WizardChooseTemplate
{
    protected $template = array(
        '1' => array(
            'showIn' => 'vigattintrade.com',
            'template' => 'home-sidebar-left',
        ),
        '2' => array(
            'showIn' => 'vigattintrade.com',
            'template' => 'related-ads-bottom',
        ),
        '3' => array(
            'showIn' => 'vigattintourism.com',
            'template' => 'home-sidebar-right',
        ),
    );

    /**
     * @var \VigattinAds\Controller\AccountHomeController
     */
    protected $accountHomeCtrl;

    /**
     * @var \Zend\View\Model\ViewModel
     */
    protected $viewModel;

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
        $this->actionContent = new ViewModel();
        $this->actionContent->setTemplate('vigattinads/view/wizard-choose-template');
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
        $template = $this->getTemplate($this->accountHomeCtrl->getRequest()->getPost('action', ''));
        if(is_array($template))
        {
            $this->sessionManager->getStorage()->tempAdsTemplate = $template;
            header('Location: /vigattinads/account-home/ads/wizard/edit');
            exit();
        }
        return $this->actionContent;
    }

    public function getTemplate($templateCode)
    {
        $templateCode = strval($templateCode);
        if(isset($this->template[$templateCode])) return $this->template[$templateCode];
        return '';
    }
}