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

    protected $adsKeyword = '';

    public function indexAction()
    {
        // redirect to choose template wizard if has no template in session
        $this->redirectNoTemplate();

        // if request template is true clear the template session then redirect to choose template wizard
        $this->resetTemplate();

        // if user submit the form
        if(strtolower($this->getRequest()->getPost('submit', '')) == 'next') {
            $adsKeyword = $this->getRequest()->getPost('ads-keyword', '');
            $featuredAds = $this->getRequest()->getPost('featuredAds', array());
            $adsListing = $this->getRequest()->getPost('adsListing', array());
            if(strtolower($this->sessionManager->getStorage()->tempAdsTemplate['showIn']) == 'vigattintrade.com') $this->adsKeyword = $this->processTradeAdditionalAdsPosition($this->getRequest()->getPost('selectedKeyword', array()));
            else $this->adsKeyword = $adsKeyword;
            $formError = $this->onSubmit();
        }
        // default action refresh or just enter page
        else {
            $formError = $this->createDefaultValue();
        }

        // set main view title
        $this->mainView->setVariable('title', 'Edit Ads Info');

        // create final view
        $actionContent = new ViewModel();
        switch(strtolower($this->sessionManager->getStorage()->tempAdsTemplate['showIn'])) {
            case 'vigattintrade.com':
                if($this->sessionManager->getStorage()->tempAdsTemplate['template'] == 'home-sidebar-left') {
                    $actionContent->setTemplate('vigattinads/view/dashboard/ads/adsWizardEditInfoView');
                }
                else {
                    $actionContent->setTemplate('vigattinads/view/dashboard/ads/adsWizardEditInfoNoCatView');
                }
                break;
            case 'vigattintourism.com':
                $actionContent->setTemplate('vigattinads/view/dashboard/ads/adsWizardEditInfoTourismView');
                $formError['adsTempKeyword'] = 'Homepage|Destination|Articles|Tourist Spots|Discussion|Directory';
                $formError['adsKeyword'] = '';
                break;
            case 'vigattin.com':
                $actionContent->setTemplate('vigattinads/view/dashboard/ads/adsWizardEditInfoVigattinView');
                $formError['adsTempKeyword'] = 'Homepage';
                $formError['adsKeyword'] = '';
                break;
            default:
                $actionContent->setTemplate('vigattinads/view/dashboard/ads/adsWizardEditInfoNoCatView');
                break;
        }

        // pass variable to final view
        $actionContent->setVariables($formError);
        $actionContent->setVariable('origAdsTitle', $this->sessionManager->getStorage()->origAdsTitle);
        $actionContent->setVariable('origAdsDescription', $this->sessionManager->getStorage()->origAdsDescription);
        $actionContent->setVariable('origAdsUrl', $this->sessionManager->getStorage()->origAdsUrl);
        $actionContent->setVariable('request', $this->getRequest());

        // append final view to main view
        $this->mainView->addChild($actionContent, 'actionContent');
        return $this->mainView;
    }

    public function redirectNoTemplate()
    {
        if(empty($this->sessionManager->getStorage()->tempAdsTemplate['showIn']) || empty($this->sessionManager->getStorage()->tempAdsTemplate['template'])) {
            header('Location: /vigattinads/dashboard/ads/template');
            exit();
        }
    }

    public function resetTemplate()
    {
        if(strtolower($this->getRequest()->getQuery('template', '')) == 'true') {
            $this->sessionManager->getStorage()->tempAdsTemplate['showIn'] = '';
            $this->sessionManager->getStorage()->tempAdsTemplate['template'] = '';
            header('Location: /vigattinads/dashboard/ads/template');
            exit();
        }
    }

    public function onSubmit()
    {
        $formError = array(
            'adsTitle' => $this->getRequest()->getPost('ads-title', ''),
            'adsUrl' => $this->getRequest()->getPost('ads-url', ''),
            'adsKeyword' => $this->adsKeyword,
            'adsTempKeyword' => $this->sessionManager->getStorage()->tempAdsKeyword,
            'adsPrice' => $this->getRequest()->getPost('ads-price', ''),
            'adsDescription' => $this->getRequest()->getPost('ads-description', ''),
            'adsImageDataUrl' => $this->getRequest()->getPost('ads-image-data-url', ''),
            'adsShowIn' => $this->sessionManager->getStorage()->tempAdsTemplate['showIn'],
            'adsTemplate' => $this->sessionManager->getStorage()->tempAdsTemplate['template'],
            'adsDate' => $this->sessionManager->getStorage()->tempAdsDate,
            'adsImageError' => Validator::isImageString($this->getRequest()->getPost('ads-image-data-url', '')),
            'adsTitleError' => Validator::isTitleValid($this->getRequest()->getPost('ads-title', '')),
            'adsUrlError' => Validator::isUrlValid($this->getRequest()->getPost('ads-url', '')),
            'adsKeywordError' => Validator::isKeywordValid($this->getRequest()->getPost('ads-keyword', '')),
            'adsPriceError' => Validator::isNumber($this->getRequest()->getPost('ads-price', '')),
            'adsDescriptionError' => Validator::isDescriptionValid($this->getRequest()->getPost('ads-description', '')),
        );
        $this->sessionManager->getStorage()->tempAdsTitle = $formError['adsTitle'];
        $this->sessionManager->getStorage()->tempAdsUrl = $formError['adsUrl'];
        $this->sessionManager->getStorage()->tempAdsPrice = $formError['adsPrice'];
        $this->sessionManager->getStorage()->tempAdsDescription = $formError['adsDescription'];
        if(!strlen($formError['adsImageError'].$formError['adsTitleError'].$formError['adsUrlError'].$formError['adsKeywordError'].$formError['adsPriceError'].$formError['adsDescriptionError'])) {
            $formError['adsImageError'] = $this->processRequest();
        }
        return $formError;
    }

    public function createDefaultValue()
    {
        $formError = array(
            'adsTitle' => $this->sessionManager->getStorage()->tempAdsTitle,
            'adsUrl' => $this->sessionManager->getStorage()->tempAdsUrl,
            'adsKeyword' => '',
            'adsTempKeyword' => $this->sessionManager->getStorage()->tempAdsKeyword,
            'adsPrice' => $this->sessionManager->getStorage()->tempAdsPrice,
            'adsDescription' => '', //$this->sessionManager->getStorage()->tempAdsDescription,
            'adsImageDataUrl' => $this->sessionManager->getStorage()->tempAdsImageDataUrl,
            'adsShowIn' => $this->sessionManager->getStorage()->tempAdsTemplate['showIn'],
            'adsTemplate' => $this->sessionManager->getStorage()->tempAdsTemplate['template'],
            'adsDate' => $this->sessionManager->getStorage()->tempAdsDate,
            'adsImageError' => '',
            'adsTitleError' => '',
            'adsUrlError' => '',
            'adsKeywordError' => '',
            'adsPriceError' => '',
            'adsDescriptionError' => '',
        );
        return $formError;
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
                $this->adsKeyword,
                $this->sessionManager->getStorage()->tempAdsPrice,
                $this->getRequest()->getPost('ads-temp-keyword', ''),
                $this->sessionManager->getStorage()->tempAdsDate
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

    /**
     * add additional keyword to ads
     * @param string $adsKeyword the original keyword
     * @param array $featuredAds added keyword
     * @param array $adsListing added keyword
     * @return mixed|string new keyword
     */
    static public function processTradeAdditionalAdsPosition($selectedKeywordArray)
    {
        $adsKeyword = '';
        foreach($selectedKeywordArray as $value) {
            $adsKeyword .= $value;
        }
        return $adsKeyword;
    }

    static public function isCatMatch($allowedCat, $cat)
    {
        $allowedCat = strtolower($allowedCat);
        $cat = strtolower(trim($cat, '()'));
        if(strpos($allowedCat, $cat) !== false) return true;
        else return false;
    }
}
