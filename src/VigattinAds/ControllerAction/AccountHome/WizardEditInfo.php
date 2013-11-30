<?php
namespace VigattinAds\ControllerAction\AccountHome;

use VigattinAds\Controller\AccountHomeController;
use Zend\View\Model\ViewModel;
use VigattinAds\Model\Helper\Validator;

class WizardEditInfo
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

    public function __construct(AccountHomeController $accountHomeCtrl)
    {
        $this->accountHomeCtrl = $accountHomeCtrl;
        $this->viewModel = $accountHomeCtrl->getMainView();
        $this->userModel = $this->accountHomeCtrl->getServiceLocator()->get('VigattinAds\Model\User\User');
        $this->actionContent = new ViewModel();
        $this->actionContent->setTemplate('vigattinads/view/wizard-edit-info');
    }

    public function process()
    {
        $actionContent = $this->action();
        $this->viewModel->addChild($actionContent, 'actionContent');
        return $this->viewModel;
    }

    public function action()
    {
        if(strtolower($this->accountHomeCtrl->getRequest()->getPost('submit', '')) == 'next')
        {
            $formError = array(
                'adsName' => $this->accountHomeCtrl->getRequest()->getPost('ads-name', ''),
                'adsUrl' => $this->accountHomeCtrl->getRequest()->getPost('ads-url', ''),
                'adsDescription' => $this->accountHomeCtrl->getRequest()->getPost('ads-description', ''),
                'adsNameError' => Validator::isNameValid($this->accountHomeCtrl->getRequest()->getPost('ads-name', '')),
                'adsUrlError' => Validator::isUrlValid($this->accountHomeCtrl->getRequest()->getPost('ads-url', '')),
                'adsDescriptionError' => Validator::isDescriptionValid($this->accountHomeCtrl->getRequest()->getPost('ads-description', '')),
            );
        }
        else
        {
            $formError = array(
                'adsName' => $this->accountHomeCtrl->getRequest()->getPost('ads-name', ''),
                'adsUrl' => $this->accountHomeCtrl->getRequest()->getPost('ads-url', ''),
                'adsDescription' => $this->accountHomeCtrl->getRequest()->getPost('ads-description', ''),
                'adsNameError' => '',
                'adsUrlError' => '',
                'adsDescriptionError' => '',
            );
        }
        $this->actionContent->setVariables($formError);
        return $this->actionContent;
    }
}