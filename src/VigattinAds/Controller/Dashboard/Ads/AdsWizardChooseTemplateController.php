<?php
namespace VigattinAds\Controller\Dashboard\Ads;

use Zend\View\Model\ViewModel;

class AdsWizardChooseTemplateController extends AdsController
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

    public function indexAction()
    {
        $template = $this->getTemplate($this->getRequest()->getPost('action', ''));
        if(is_array($template)) {
            $this->sessionManager->getStorage()->tempAdsTemplate = $template;
            header('Location: /vigattinads/dashboard/ads/info');
            exit();
        }

        $this->saveImportedAdsToSession();

        $this->mainView->setVariable('title', 'Choose Ads Template');
        $actionContent = new ViewModel();
        $actionContent->setTemplate('vigattinads/view/dashboard/ads/adsWizardChooseTemplateView');
        $this->mainView->addChild($actionContent, 'actionContent');
        return $this->mainView;
    }

    public function getTemplate($templateCode)
    {
        $templateCode = strval($templateCode);
        if(isset($this->template[$templateCode])) return $this->template[$templateCode];
        return '';
    }

    public function saveImportedAdsToSession()
    {
        if($this->getRequest()->getPost('action', '') == 'save-session') {
            $this->sessionManager->getStorage()->tempAdsTitle = $this->getRequest()->getPost('ads-title', '');
            $this->sessionManager->getStorage()->tempAdsUrl = $this->getRequest()->getPost('ads-url', '');
            $this->sessionManager->getStorage()->tempAdsKeyword = $this->getRequest()->getPost('ads-keyword', '');
            $this->sessionManager->getStorage()->tempAdsDescription = $this->getRequest()->getPost('ads-description', '');
            $this->sessionManager->getStorage()->tempAdsImageDataUrl = $this->getRequest()->getPost('ads-image-data-url', '');
        }
    }
}
