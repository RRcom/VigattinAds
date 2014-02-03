<?php
namespace VigattinAds\Controller\Dashboard\Ads;

use Zend\View\Model\ViewModel;
use VigattinAds\DomainModel\Image;
use VigattinAds\DomainModel\Validator;

class AdsWizardEditInfoController extends AdsController
{
    const IMAGE_REPO = 'repo';
    const IMAGE_WIDTH = 150;
    const IMAGE_QUALITY = 75;
    const IMAGE_PROGRESSIVE = true;

    public function indexAction()
    {
        if(empty($this->sessionManager->getStorage()->tempAdsTemplate['showIn']) || empty($this->sessionManager->getStorage()->tempAdsTemplate['template'])) {
            header('Location: /vigattinads/dashboard/ads/template');
            exit();
        }
        if(strtolower($this->getRequest()->getPost('submit', '')) == 'next') {
            $formError = array(
                'adsTitle' => $this->getRequest()->getPost('ads-title', ''),
                'adsUrl' => $this->getRequest()->getPost('ads-url', ''),
                'adsKeyword' => $this->getRequest()->getPost('ads-keyword', ''),
                'adsPrice' => $this->getRequest()->getPost('ads-price', ''),
                'adsDescription' => $this->getRequest()->getPost('ads-description', ''),
                'adsImageDataUrl' => $this->getRequest()->getPost('ads-image-data-url', ''),
                'adsImageError' => Validator::isImageString($this->getRequest()->getPost('ads-image-data-url', '')),
                'adsTitleError' => Validator::isTitleValid($this->getRequest()->getPost('ads-title', '')),
                'adsUrlError' => Validator::isUrlValid($this->getRequest()->getPost('ads-url', '')),
                'adsKeywordError' => Validator::isKeywordValid($this->getRequest()->getPost('ads-keyword', '')),
                'adsPriceError' => Validator::isNumber($this->getRequest()->getPost('ads-price', '')),
                'adsDescriptionError' => Validator::isDescriptionValid($this->getRequest()->getPost('ads-description', '')),
            );
            $this->sessionManager->getStorage()->tempAdsTitle = $formError['adsTitle'];
            $this->sessionManager->getStorage()->tempAdsUrl = $formError['adsUrl'];
            $this->sessionManager->getStorage()->tempAdsKeyword = $formError['adsKeyword'];
            $this->sessionManager->getStorage()->tempAdsPrice = $formError['adsPrice'];
            $this->sessionManager->getStorage()->tempAdsDescription = $formError['adsDescription'];
            if(!strlen($formError['adsImageError'].$formError['adsTitleError'].$formError['adsUrlError'].$formError['adsKeywordError'].$formError['adsPriceError'].$formError['adsDescriptionError'])) {
                $formError['adsImageError'] = $this->processRequest();
            }
        }
        else {
            $formError = array(
                'adsTitle' => $this->sessionManager->getStorage()->tempAdsTitle,
                'adsUrl' => $this->sessionManager->getStorage()->tempAdsUrl,
                'adsKeyword' => $this->sessionManager->getStorage()->tempAdsKeyword,
                'adsPrice' => $this->sessionManager->getStorage()->tempAdsPrice,
                'adsDescription' => $this->sessionManager->getStorage()->tempAdsDescription,
                'adsImageDataUrl' => $this->sessionManager->getStorage()->tempAdsImageDataUrl,
                'adsImageError' => '',
                'adsTitleError' => '',
                'adsUrlError' => '',
                'adsKeywordError' => '',
                'adsPriceError' => '',
                'adsDescriptionError' => '',
            );
        }

        $this->mainView->setVariable('title', 'Edit Ads Info');
        $actionContent = new ViewModel();
        $actionContent->setTemplate('vigattinads/view/dashboard/ads/adsWizardEditInfoView');
        $actionContent->setVariables($formError);
        $this->mainView->addChild($actionContent, 'actionContent');
        return $this->mainView;
    }

    public function processRequest()
    {
        $repo = 'public/'.self::IMAGE_REPO;
        $image = new Image($repo);
        $uploadedImage = $this->getRequest()->getPost('ads-image-data-url', '');
        if(!$uploadedImage) {
            $uploadedImage = $this->sessionManager->getStorage()->adsImageDataUrl;
        }
        $result = $image->save_convert_resize(
            $uploadedImage,
            self::IMAGE_WIDTH,
            self::IMAGE_QUALITY,
            self::IMAGE_PROGRESSIVE
        );
        if($result['status'] == 'success')
        {
            $this->adsUser->createAds(
                $this->sessionManager->getStorage()->tempAdsTitle,
                $this->sessionManager->getStorage()->tempAdsUrl,
                $result['path'],
                $this->sessionManager->getStorage()->tempAdsDescription,
                $this->sessionManager->getStorage()->tempAdsTemplate['showIn'],
                $this->sessionManager->getStorage()->tempAdsTemplate['template'],
                $this->sessionManager->getStorage()->tempAdsKeyword,
                $this->sessionManager->getStorage()->tempAdsPrice
            );
            $this->adsUser->flush();
            $this->clearTempData();
            header('Location: /vigattinads/dashboard/ads');
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
        $this->sessionManager->getStorage()->tempAdsPrice = null;
        $this->sessionManager->getStorage()->tempAdsImageDataUrl = null;
    }
}
