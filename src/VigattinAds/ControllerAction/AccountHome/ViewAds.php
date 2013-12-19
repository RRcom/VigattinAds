<?php
namespace VigattinAds\ControllerAction\AccountHome;

use VigattinAds\Controller\AccountHomeController;
use Zend\View\Model\ViewModel;
use VigattinAds\DomainModel\Validator;
use VigattinAds\DomainModel\SettingsManager;

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

        if($adsEntity instanceof \VigattinAds\DomainModel\Ads)
        {
            $adsViewCount = $adsEntity->get('adsView')->count();
            if(strtolower($this->accountHomeCtrl->getRequest()->getPost('submit', '')) == 'next')
            {
                $formError = array(
                    'adsTitle' => $this->accountHomeCtrl->getRequest()->getPost('ads-title', ''),
                    'adsUrl' => $this->accountHomeCtrl->getRequest()->getPost('ads-url', ''),
                    'adsKeyword' => $this->accountHomeCtrl->getRequest()->getPost('ads-keyword', ''),
                    'adsDescription' => $this->accountHomeCtrl->getRequest()->getPost('ads-description', ''),
                    'adsTitleError' => Validator::isTitleValid($this->accountHomeCtrl->getRequest()->getPost('ads-title', '')),
                    'adsUrlError' => Validator::isUrlValid($this->accountHomeCtrl->getRequest()->getPost('ads-url', '')),
                    'adsKeywordError' => Validator::isKeywordValid($this->accountHomeCtrl->getRequest()->getPost('ads-keyword', '')),
                    'adsDescriptionError' => Validator::isDescriptionValid($this->accountHomeCtrl->getRequest()->getPost('ads-description', '')),
                );
                if(!strlen($formError['adsTitleError'].$formError['adsUrlError'].$formError['adsKeywordError'].$formError['adsDescriptionError']))
                {
                    $oldValue = strtolower($adsEntity->get('adsTitle').$adsEntity->get('adsUrl').$adsEntity->get('keywords').$adsEntity->get('adsDescription'));
                    $newValue = strtolower($formError['adsTitle'].$formError['adsUrl'].$formError['adsKeyword'].$formError['adsDescription']);

                    $adsEntity->set('adsTitle', $formError['adsTitle']);
                    $adsEntity->set('adsUrl', $formError['adsUrl']);
                    $adsEntity->set('keywords', $formError['adsKeyword']);
                    $adsEntity->set('adsDescription', $formError['adsDescription']);
                    if($oldValue !== $newValue) $adsEntity->set('status', $adsEntity::STATUS_PENDING);

                    $adsEntity->persistSelf();
                    $adsEntity->flush();
                }
            }
            elseif(strtolower($this->accountHomeCtrl->getRequest()->getPost('submit', '')) == 'delete')
            {
                $settingManager = new SettingsManager($this->accountHomeCtrl->getServiceLocator());
                $viewToGoldRate = floatval($settingManager->get('viewToGoldRate'));
                $viewLimit = $adsEntity->get('viewLimit');
                if($viewLimit < 0) $viewLimit = 0;
                $adsEntity->set('viewLimit', 0);
                $adsUser->set('credit', $viewLimit*$viewToGoldRate);
                $adsUser->deleteAds($adsEntity->get('id'));

                header('Location: /vigattinads/account-home/ads');
                exit();
            }
            else
            {
                $formError = array(
                    'adsTitle' => $adsEntity->get('adsTitle'),
                    'adsUrl' => $adsEntity->get('adsUrl'),
                    'adsKeyword' => $adsEntity->get('keywords'),
                    'adsDescription' => $adsEntity->get('adsDescription'),
                    'adsTitleError' => '',
                    'adsUrlError' => '',
                    'adsKeywordError' => '',
                    'adsDescriptionError' => '',
                );
            }
        }

        $this->actionContent->setVariables($formError);
        $this->actionContent->setVariable('ads', $adsEntity);
        $this->actionContent->setVariable('userManager', $this->userManager);
        $this->actionContent->setVariable('adsUser', $adsUser);
        $this->actionContent->setVariable('adsViewCount', $adsViewCount);
        return $this->actionContent;
    }
}