<?php
namespace VigattinAds\Controller\Dashboard\Ads\Create\ChooseWebsite\Import;

use VigattinAds\Controller\Dashboard\Ads\AdsController;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

class ImportController extends AdsController
{
    public function indexAction()
    {
        $this->saveImportedAdsToSession();
        $actionContent = new ViewModel();
        $this->mainView->setVariable('title', 'Step 2. Import ads template');
        $actionContent->setTemplate('vigattinads/view/dashboard/ads/create/import/importView');
        $actionContent->setVariable('website', $this->sessionManager->getStorage()->tempAdsTemplate['showIn']);
        $this->mainView->addChild($actionContent, 'actionContent');
        return $this->mainView;
    }

    public function saveImportedAdsToSession()
    {
        if($this->getRequest()->getPost('action', '') == 'save-session') {
            $this->sessionManager->getStorage()->tempAdsTitle = $this->getRequest()->getPost('ads-title', '');
            $this->sessionManager->getStorage()->origAdsTitle = $this->getRequest()->getPost('ads-title', '');
            $this->sessionManager->getStorage()->tempAdsUrl = $this->getRequest()->getPost('ads-url', '');
            $this->sessionManager->getStorage()->origAdsUrl = $this->getRequest()->getPost('ads-url', '');
            $this->sessionManager->getStorage()->tempAdsKeyword = $this->getRequest()->getPost('ads-keyword', '');
            $this->sessionManager->getStorage()->tempAdsDescription = $this->getRequest()->getPost('ads-description', '');
            $this->sessionManager->getStorage()->origAdsDescription = $this->getRequest()->getPost('ads-description', '');
            $this->sessionManager->getStorage()->tempAdsImageDataUrl = $this->getRequest()->getPost('ads-image-data-url', '');
            $this->sessionManager->getStorage()->tempAdsPrice = $this->getRequest()->getPost('ads-price', '');
            $this->sessionManager->getStorage()->tempAdsDate = $this->getRequest()->getPost('ads-date', '');
            $this->redirect()->toRoute('vigattinads_dashboard_ads_create_choose_website_import_edit');
        }
    }
}
