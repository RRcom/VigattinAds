<?php
namespace VigattinAds\Controller\Dashboard\Ads\Edit;

use Zend\View\Model\ViewModel;
use VigattinAds\Controller\Dashboard\Ads\AdsController;
use VigattinAds\DomainModel\Validator;
use VigattinAds\DomainModel\Ads;
use VigattinAds\Controller\Dashboard\Ads\Create\ChooseWebsite\ChooseWebsiteController;
use VigattinAds\DomainModel\AdsCategory;

class EditController extends AdsController
{
    /** @var \VigattinAds\DomainModel\Ads */
    protected $adsEntity;

    protected $formValue = array();

    protected $adsViewCount = 0;

    protected $formAction;

    protected $showIn;

    protected $selectedCategory;

    public function __construct()
    {
        $this->formValue = array(
            'adsTitle' => '',
            'adsUrl' => '',
            'adsKeyword' => '',
            'adsCategory' => '',
            'adsPrice' => '',
            'adsDescription' => '',
            'adsTitleError' => '',
            'adsUrlError' => '',
            'adsPriceError' => '',
            'adsDescriptionError' => '',
        );
    }

    public function indexAction()
    {
        // set partial view title
        $this->mainView->setVariable('title', 'Ads Edit');

        // get form action
        $this->formAction = strtolower($this->getRequest()->getPost('submit', ''));

        // get selected category
        $this->selectedCategory = $this->getRequest()->getPost('selectedCategory', array());

        // get ads entity from param1 id
        $this->adsEntity = $this->adsUser->getSingleAds($this->params('param1', ''));

        // load edit partial view
        $actionContent = new ViewModel();

        // block if no ads found
        if(!$this->adsEntity instanceof \VigattinAds\DomainModel\Ads) {
            $actionContent->setTemplate('vigattinads/view/dashboard/ads/edit/notFoundView');
            $this->mainView->addChild($actionContent, 'actionContent');
            return $this->mainView;
        } else $actionContent->setTemplate('vigattinads/view/dashboard/ads/edit/editView');

        // get show in data
        $this->showIn = strtolower($this->params('param2', '') ? $this->params('param2') : $this->adsEntity->get('showIn'));

        // load change target site partial view
        $actionContent->addChild($this->changeSiteTargetMenu(), 'changeTargetSiteView');

        // load category partial
        $actionContent->addChild($this->categoryMenu(), 'categoryView');

        // process on enter page
        $this->onEnter();

        // process form action
        $this->processFormAction();

        // load required variable to action content
        $actionContent->setVariables($this->formValue);
        $actionContent->setVariable('ads', $this->adsEntity);
        $actionContent->setVariable('userManager', $this->userManager);
        $actionContent->setVariable('adsUser', $this->adsUser);
        $actionContent->setVariable('adsViewCount', $this->adsViewCount);
        $actionContent->setVariable('adsReviewReason', $this->adsEntity->getLastReviewReason());
        $actionContent->setVariable('request', $this->getRequest());

        // load the main view
        $this->mainView->addChild($actionContent, 'actionContent');
        return $this->mainView;
    }

    /**
     * If user only enter the page
     */
    protected function onEnter()
    {
        $this->formValue['adsTitle'] = $this->adsEntity->get('adsTitle');
        $this->formValue['adsUrl'] = $this->adsEntity->get('adsUrl');
        $this->formValue['adsKeyword'] = $this->adsEntity->get('keywords');
        $this->formValue['adsCategory'] = $this->adsEntity->get('category');
        $this->formValue['adsPrice'] = $this->adsEntity->get('adsPrice');
        $this->formValue['adsDescription'] = $this->adsEntity->get('adsDescription');
    }

    protected function processFormAction()
    {
        switch($this->formAction) {
            case 'next':
                $this->onNext();
                break;
            case 'resume':
                $this->onResume();
                break;
            case 'delete':
                $this->onDelete();
                break;
            case 'pause':
                $this->onPause();
                break;
            default:
                break;
        }
    }

    protected function onDelete()
    {
        $viewToGoldRate = floatval($this->settingsManager->get('viewToGoldRate'));
        $viewLimit = $this->adsEntity->get('viewLimit');
        if($viewLimit < 0) $viewLimit = 0;
        $this->adsEntity->set('viewLimit', 0);
        $this->adsUser->set('credit', floatval($this->adsUser->get('credit'))+($viewLimit*$viewToGoldRate));
        $this->adsEntity->deleteSelf();
        $this->adsEntity->flush();
        $this->redirect()->toRoute('vigattinads_dashboard_ads');
    }

    protected function onPause()
    {
        if($this->adsEntity->get('status') == Ads::STATUS_APPROVED) {
            $this->adsEntity->set('status', Ads::STATUS_PAUSED);
            $this->adsEntity->persistSelf();
            $this->adsEntity->flush();
        }
    }

    protected function onResume()
    {
        if($this->adsEntity->get('status') == Ads::STATUS_PAUSED) {
            $this->adsEntity->set('status', Ads::STATUS_APPROVED);
            $this->adsEntity->persistSelf();
            $this->adsEntity->flush();
        }
    }

    protected function onNext()
    {
        $this->validateInput();
        // return if has error
        if(strlen($this->formValue['adsTitleError'].$this->formValue['adsUrlError'].$this->formValue['adsKeywordError'].$this->formValue['adsPriceError'].$this->formValue['adsDescriptionError'])) return;
        // check some value that need to verify first if change happen
        $oldValue = strtolower($this->adsEntity->get('adsTitle').$this->adsEntity->get('adsUrl').$this->adsEntity->get('adsDescription'));
        $newValue = strtolower($this->formValue['adsTitle'].$this->formValue['adsUrl'].$this->formValue['adsDescription']);
        // set new value
        $this->adsEntity->set('adsTitle', $this->formValue['adsTitle']);
        $this->adsEntity->set('adsUrl', $this->formValue['adsUrl']);
        $this->adsEntity->set('keywords', $this->keywordGenerator());
        $this->adsEntity->set('adsPrice', $this->formValue['adsPrice']);
        $this->adsEntity->set('adsDescription', $this->formValue['adsDescription']);
        $this->adsEntity->set('showIn', $this->showIn);
        // if has new value
        if($oldValue !== $newValue) {
            // Change log to RE-EDIT status
            $log = $this->adsManager->getLogByReviewVersion($this->adsEntity->get('reviewVersion'));
            if($log instanceof \VigattinAds\DomainModel\AdsApproveLog) {
                $log->set('reviewResult', Ads::STATUS_VALUE_CHANGED);
                $log->set('approvedTime', time());
                $log->persistSelf();
                $log->flush();

                // Create new log status
                $this->adsManager->changeAdsStatus($this->adsEntity->get('reviewVersion'), Ads::STATUS_VALUE_CHANGED, '');
                $this->adsEntity->set('reviewVersion', uniqid());
                $this->adsEntity->set('status', Ads::STATUS_PENDING);
            }
        }
        $this->adsEntity->persistSelf();
        $this->adsEntity->flush();
        $this->redirect()->toRoute('vigattinads_dashboard_ads_edit', array('param1' => $this->adsEntity->get('id')));
    }

    protected function validateInput()
    {
        $this->formValue['adsTitle'] = $this->getRequest()->getPost('ads-title', '');
        $this->formValue['adsUrl'] = $this->getRequest()->getPost('ads-url', '');
        $this->formValue['adsKeyword'] = $this->getRequest()->getPost('ads-keyword', '');
        $this->formValue['adsCategory'] = $this->adsEntity->get('category');
        $this->formValue['adsPrice'] = $this->getRequest()->getPost('ads-price', '');
        $this->formValue['adsDescription'] = $this->getRequest()->getPost('ads-description', '');
        $this->formValue['adsTitleError'] = Validator::isTitleValid($this->getRequest()->getPost('ads-title', ''));
        $this->formValue['adsUrlError'] = Validator::isUrlValid($this->getRequest()->getPost('ads-url', ''));
        $this->formValue['adsKeywordError'] = Validator::isKeywordValid($this->getRequest()->getPost('ads-keyword', ''));
        $this->formValue['adsPriceError'] = Validator::isNumber($this->getRequest()->getPost('ads-price', ''));
        $this->formValue['adsDescriptionError'] = Validator::isDescriptionValid($this->getRequest()->getPost('ads-description', ''));
    }

    protected function categoryMenu()
    {
        $catView = new ViewModel();
        $catView->setTemplate('vigattinads/view/dashboard/ads/edit/category/siteCatView');
        switch($this->showIn) {
            case ChooseWebsiteController::VIGATTIN:
                $catProvider = new AdsCategory\VigattinAdsCategoryProvider($this->serviceLocator, $this->adsEntity, $this->getRequest()->getPost('selectedCategory', array()));
                $catView->setVariable('title', 'Vigattin Directory');
                $catView->setVariable('description', 'Choose which directory the ads will appear');
                $catView->setVariable('adsCategories', $catProvider->getAdsCategory());
                break;
            case ChooseWebsiteController::VIGATTINTOURISM:
                $catProvider = new AdsCategory\TourismAdsCategoryProvider($this->serviceLocator, $this->adsEntity, $this->getRequest()->getPost('selectedCategory', array()));
                $catView->setVariable('title', 'Vigattintourism Directory');
                $catView->setVariable('description', 'Choose which directory the ads will appear');
                $catView->setVariable('adsCategories', $catProvider->getAdsCategory());
                break;
            case ChooseWebsiteController::VIGATTINTRADE:
                $catProvider = new AdsCategory\VigattinAdsCategoryProvider($this->serviceLocator, $this->adsEntity, $this->getRequest()->getPost('selectedCategory', array()));
                $catView->setVariable('title', 'Vigattin Directory');
                $catView->setVariable('description', 'Choose which directory the ads will appear');
                $catView->setVariable('adsCategories', $catProvider->getAdsCategory());
                break;
            case ChooseWebsiteController::TOURISMBLOGGER:
                $catProvider = new AdsCategory\TourismArticleAdsCategoryProvider($this->serviceLocator, $this->adsEntity, $this->getRequest()->getPost('selectedCategory', array()));
                $catView->setVariable('title', 'Page Position');
                $catView->setVariable('description', 'Choose which position the ads will appear');
                $catView->setVariable('adsCategories', $catProvider->getAdsCategory());
                break;
            default:
                $catProvider = new AdsCategory\VigattinAdsCategoryProvider($this->serviceLocator, $this->adsEntity, $this->getRequest()->getPost('selectedCategory', array()));
                $catView->setVariable('title', 'Vigattin Directory');
                $catView->setVariable('description', 'Choose which directory the ads will appear');
                $catView->setVariable('adsCategories', $catProvider->getAdsCategory());
                break;
        }
        return $catView;
    }

    protected function changeSiteTargetMenu()
    {
        $changeTargetSiteView = new ViewModel();
        $changeTargetSiteView->setTemplate('vigattinads/view/dashboard/ads/edit/changeTargetSiteView');
        $changeTargetSiteView->setVariable('ads', $this->adsEntity);
        $changeTargetSiteView->setVariable('showIn', $this->showIn);
        return $changeTargetSiteView;
    }

    protected function keywordGenerator()
    {
        return implode('', $this->getRequest()->getPost('selectedCategory', array()));
    }
}
