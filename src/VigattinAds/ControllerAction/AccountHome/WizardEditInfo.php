<?php
namespace VigattinAds\ControllerAction\AccountHome;

use VigattinAds\Controller\AccountHomeController;
use Zend\View\Model\ViewModel;
use VigattinAds\DomainModel\Validator;
use VigattinAds\DomainModel\Image;
use VigattinAds\DomainModel\SettingsManager;

class WizardEditInfo
{
    const IMAGE_REPO = 'repo';
    const IMAGE_WIDTH = 150;
    const IMAGE_QUALITY = 75;
    const IMAGE_PROGRESSIVE = true;

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
        $this->actionContent->setTemplate('vigattinads/view/wizard-edit-info');
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
        if(strtolower($this->accountHomeCtrl->getRequest()->getPost('submit', '')) == 'next')
        {
            $formError = array(
                'adsTitle' => $this->accountHomeCtrl->getRequest()->getPost('ads-title', ''),
                'adsUrl' => $this->accountHomeCtrl->getRequest()->getPost('ads-url', ''),
                'adsKeyword' => $this->accountHomeCtrl->getRequest()->getPost('ads-keyword', ''),
                'adsDescription' => $this->accountHomeCtrl->getRequest()->getPost('ads-description', ''),
                'adsImageError' => Validator::isImage($this->accountHomeCtrl->getRequest()->getFiles('ads-image')),
                'adsTitleError' => Validator::isTitleValid($this->accountHomeCtrl->getRequest()->getPost('ads-title', '')),
                'adsUrlError' => Validator::isUrlValid($this->accountHomeCtrl->getRequest()->getPost('ads-url', '')),
                'adsKeywordError' => Validator::isKeywordValid($this->accountHomeCtrl->getRequest()->getPost('ads-keyword', '')),
                'adsDescriptionError' => Validator::isDescriptionValid($this->accountHomeCtrl->getRequest()->getPost('ads-description', '')),
            );
            $this->sessionManager->getStorage()->tempAdsTitle = $formError['adsTitle'];
            $this->sessionManager->getStorage()->tempAdsUrl = $formError['adsUrl'];
            $this->sessionManager->getStorage()->tempAdsKeyword = $formError['adsKeyword'];
            $this->sessionManager->getStorage()->tempAdsDescription = $formError['adsDescription'];
            if(!strlen($formError['adsImageError'].$formError['adsTitleError'].$formError['adsUrlError'].$formError['adsKeywordError'].$formError['adsDescriptionError']))
            {
                $formError['adsImageError'] = $this->processRequest();
            }
        }
        else
        {
            $formError = array(
                'adsTitle' => $this->sessionManager->getStorage()->tempAdsTitle,
                'adsUrl' => $this->sessionManager->getStorage()->tempAdsUrl,
                'adsKeyword' => $this->sessionManager->getStorage()->tempAdsKeyword,
                'adsDescription' => $this->sessionManager->getStorage()->tempAdsDescription,
                'adsImageError' => '',
                'adsTitleError' => '',
                'adsUrlError' => '',
                'adsKeywordError' => '',
                'adsDescriptionError' => '',
            );
        }
        $this->actionContent->setVariables($formError);
        return $this->actionContent;
    }

    public function processRequest()
    {
        $repo = realpath(__DIR__.'/../../../../../../public/'.self::IMAGE_REPO);
        $image = new Image($repo);
        $uploadedImage = $this->accountHomeCtrl->getRequest()->getFiles('ads-image');
        $result = $image->save_convert_resize(
            $uploadedImage['tmp_name'],
            self::IMAGE_WIDTH,
            self::IMAGE_QUALITY,
            self::IMAGE_PROGRESSIVE
        );
        if($result['status'] == 'success')
        {
            $adsUser = $this->userManager->getCurrentUser();
            $adsUser->createAds(
                $this->sessionManager->getStorage()->tempAdsTitle,
                $this->sessionManager->getStorage()->tempAdsUrl,
                $result['path'],
                $this->sessionManager->getStorage()->tempAdsDescription,
                $this->sessionManager->getStorage()->tempAdsTemplate['showIn'],
                $this->sessionManager->getStorage()->tempAdsTemplate['template'],
                $this->sessionManager->getStorage()->tempAdsKeyword
            );
            $adsUser->flush();
            $this->clearTempData();
            header('Location: /vigattinads/account-home/ads');
            exit();
        }
        return $result['reason'].' '.$repo;
    }

    public function clearTempData()
    {
        $this->sessionManager->getStorage()->tempAdsTitle = null;
        $this->sessionManager->getStorage()->tempAdsUrl = null;
        $this->sessionManager->getStorage()->tempAdsDescription = null;
        $this->sessionManager->getStorage()->tempAdsTemplate = null;
        $this->sessionManager->getStorage()->tempAdsKeyword = null;
    }
}