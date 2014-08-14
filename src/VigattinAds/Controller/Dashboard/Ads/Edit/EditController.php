<?php
namespace VigattinAds\Controller\Dashboard\Ads\Edit;

use Zend\View\Model\ViewModel;
use VigattinAds\DomainModel\Validator;
use VigattinAds\Controller\Dashboard\Ads\AdsController;
use VigattinAds\Controller\Dashboard\Ads\Create\ChooseWebsite\ChooseWebsiteController;

class EditController extends AdsController
{
    /** @var \VigattinAds\DomainModel\Ads */
    protected $adsEntity;

    public function indexAction()
    {
        $this->adsEntity = $this->adsUser->getSingleAds($this->params('param1', ''));
        if($this->params('param2', '')) $this->adsEntity->set('showIn', $this->params('param2'));
        $adsViewCount = 0;

        if($this->adsEntity instanceof \VigattinAds\DomainModel\Ads) {
            $adsViewCount = $this->adsEntity->get('viewCount');
            /** @var \Doctrine\Common\Collections\ArrayCollection $adsApproveLog */
            $adsApproveLog =  $this->adsEntity->get('adsApproveLog');
            if(strtolower($this->getRequest()->getPost('submit', '')) == 'next') {
                $formError = $this->onApply();
            }
            elseif(strtolower($this->getRequest()->getPost('submit', '')) == 'delete') {
                $this->onDelete();
            }
            elseif(strtolower($this->getRequest()->getPost('submit', '')) == 'pause') {
                $formError = $this->onPause();
            }
            elseif(strtolower($this->getRequest()->getPost('submit', '')) == 'resume') {
                $formError = $this->onResume();
            }
            else {
                $formError = $this->onEnter();
            }
        } else {
            return $this->redirect()->toRoute('vigattinads_dashboard_ads');
        }

        $this->mainView->setVariable('title', 'Ads Edit');
        $actionContent = new ViewModel();
        $this->setTemplate($actionContent, $this->adsEntity, $this->adsEntity->get('showIn'));

        $actionContent->setVariables($formError);
        $actionContent->setVariable('ads', $this->adsEntity);
        $actionContent->setVariable('userManager', $this->userManager);
        $actionContent->setVariable('adsUser', $this->adsUser);
        $actionContent->setVariable('adsViewCount', $adsViewCount);
        $actionContent->setVariable('adsReviewReason', $this->adsEntity->getLastReviewReason());
        $actionContent->setVariable('request', $this->getRequest());

        $changeTargetSiteView = new ViewModel();
        $changeTargetSiteView->setTemplate('vigattinads/view/dashboard/ads/edit/changeTargetSiteView');
        $changeTargetSiteView->setVariable('ads', $this->adsEntity);
        $actionContent->addChild($changeTargetSiteView, 'changeTargetSiteView');

        $this->mainView->addChild($actionContent, 'actionContent');
        return $this->mainView;
    }

    public function onApply()
    {
        $formError = array(
            'adsTitle' => $this->getRequest()->getPost('ads-title', ''),
            'adsUrl' => $this->getRequest()->getPost('ads-url', ''),
            'adsKeyword' => $this->getRequest()->getPost('ads-keyword', ''),
            'adsTempKeyword' => $this->adsEntity->get('category'),
            'adsPrice' => $this->getRequest()->getPost('ads-price', ''),
            'adsDescription' => $this->getRequest()->getPost('ads-description', ''),
            'adsTitleError' => Validator::isTitleValid($this->getRequest()->getPost('ads-title', '')),
            'adsUrlError' => Validator::isUrlValid($this->getRequest()->getPost('ads-url', '')),
            'adsKeywordError' => Validator::isKeywordValid($this->getRequest()->getPost('ads-keyword', '')),
            'adsPriceError' => Validator::isNumber($this->getRequest()->getPost('ads-price', '')),
            'adsDescriptionError' => Validator::isDescriptionValid($this->getRequest()->getPost('ads-description', '')),
        );
        if(!strlen($formError['adsTitleError'].$formError['adsUrlError'].$formError['adsKeywordError'].$formError['adsPriceError'].$formError['adsDescriptionError'])) {
            // check some value that need to verify first if change happen
            $oldValue = strtolower($this->adsEntity->get('adsTitle').$this->adsEntity->get('adsUrl').$this->adsEntity->get('adsDescription'));
            $newValue = strtolower($formError['adsTitle'].$formError['adsUrl'].$formError['adsDescription']);

            $this->adsEntity->set('adsTitle', $formError['adsTitle']);
            $this->adsEntity->set('adsUrl', $formError['adsUrl']);
            if(strtolower($this->adsEntity->get('showIn')) == 'vigattintrade.com') $this->adsEntity->set('keywords', \VigattinAds\Controller\Dashboard\Ads\AdsWizardEditInfoController::processTradeAdditionalAdsPosition($this->getRequest()->getPost('selectedKeyword', array())));
            else $this->adsEntity->set('keywords', $formError['adsKeyword']);
            $this->adsEntity->set('adsPrice', $formError['adsPrice']);
            $this->adsEntity->set('adsDescription', $formError['adsDescription']);
            if($oldValue !== $newValue) {
                // Change log to RE-EDIT status
                /** @var \VigattinAds\DomainModel\AdsApproveLog $log */
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
            return $formError;
        }
    }

    public function onDelete()
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

    public function onPause()
    {
        if($this->adsEntity->get('status') == Ads::STATUS_APPROVED) {
            $this->adsEntity->set('status', Ads::STATUS_PAUSED);
            $this->adsEntity->persistSelf();
            $this->adsEntity->flush();
        }
        $formError = array(
            'adsTitle' => $this->adsEntity->get('adsTitle'),
            'adsUrl' => $this->adsEntity->get('adsUrl'),
            'adsKeyword' => $this->adsEntity->get('keywords'),
            'adsTempKeyword' => $this->adsEntity->get('category'),
            'adsPrice' => $this->adsEntity->get('adsPrice'),
            'adsDescription' => $this->adsEntity->get('adsDescription'),
            'adsTitleError' => '',
            'adsUrlError' => '',
            'adsKeywordError' => '',
            'adsPriceError' => '',
            'adsDescriptionError' => '',
        );
        return $formError;
    }

    public function onResume()
    {
        if($this->adsEntity->get('status') == Ads::STATUS_PAUSED) {
            $this->adsEntity->set('status', Ads::STATUS_APPROVED);
            $this->adsEntity->persistSelf();
            $this->adsEntity->flush();
        }
        $formError = array(
            'adsTitle' => $this->adsEntity->get('adsTitle'),
            'adsUrl' => $this->adsEntity->get('adsUrl'),
            'adsKeyword' => $this->adsEntity->get('keywords'),
            'adsTempKeyword' => $this->adsEntity->get('category'),
            'adsPrice' => $this->adsEntity->get('adsPrice'),
            'adsDescription' => $this->adsEntity->get('adsDescription'),
            'adsTitleError' => '',
            'adsUrlError' => '',
            'adsPriceError' => '',
            'adsKeywordError' => '',
            'adsDescriptionError' => '',
        );
        return $formError;
    }

    public function onEnter()
    {
        $formError = array(
            'adsTitle' => $this->adsEntity->get('adsTitle'),
            'adsUrl' => $this->adsEntity->get('adsUrl'),
            'adsKeyword' => $this->adsEntity->get('keywords'),
            'adsTempKeyword' => $this->setAdsTempKeyword($this->adsEntity->get('showIn')),
            'adsPrice' => $this->adsEntity->get('adsPrice'),
            'adsDescription' => $this->adsEntity->get('adsDescription'),
            'adsTitleError' => '',
            'adsUrlError' => '',
            'adsKeywordError' => '',
            'adsPriceError' => '',
            'adsDescriptionError' => '',
        );
        return $formError;
    }

    public function setTemplate(ViewModel $viewModel, \VigattinAds\DomainModel\Ads $adsEntity, $showIn)
    {
        switch(strtolower($showIn)) {
            case ChooseWebsiteController::VIGATTINTRADE:
                if(strtolower($adsEntity->get('template')) == 'home-sidebar-left') {
                    $viewModel->setTemplate('vigattinads/view/dashboard/ads/edit/adsEditWithCatView');
                }
                else {
                    $viewModel->setTemplate('vigattinads/view/dashboard/ads/edit/adsEditView');
                }
                break;
            case ChooseWebsiteController::VIGATTINTOURISM:
                $viewModel->setTemplate('vigattinads/view/dashboard/ads/edit/adsEditWithCatTourismView');
                break;
            case ChooseWebsiteController::VIGATTIN:
                $viewModel->setTemplate('vigattinads/view/dashboard/ads/edit/adsEditVigattinView');
                break;
            case ChooseWebsiteController::TOURISMBLOGGER:
                $viewModel->setTemplate('vigattinads/view/dashboard/ads/edit/adsEditView');
                break;
            default:
                $viewModel->setTemplate('vigattinads/view/dashboard/ads/edit/adsEditView');
                break;
        }
        return $viewModel;
    }

    public function setAdsTempKeyword($showIn)
    {
        switch(strtolower($showIn)) {
            case strtolower(ChooseWebsiteController::VIGATTINTRADE):
                return '';
                break;
            case strtolower(ChooseWebsiteController::VIGATTINTOURISM):
                return 'Homepage|Destination|Articles|Tourist Spots|Discussion|Directory';
                break;
            case strtolower(ChooseWebsiteController::VIGATTIN):
                return 'Homepage';
                break;
            case strtolower(ChooseWebsiteController::TOURISMBLOGGER):
                return 'Homepage';
                break;
        }
        return '';
    }
}
