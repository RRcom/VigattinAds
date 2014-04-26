<?php
namespace VigattinAds\Controller\Dashboard\Ads;

use Zend\View\Model\ViewModel;
use VigattinAds\DomainModel\Validator;
use VigattinAds\Controller\Dashboard\Ads\AdsWizardEditInfoController;

class AdsEditController extends AdsController
{
    public function indexAction()
    {
        $adsId = $this->params('param1', '');
        $adsViewCount = 0;
        $adsEntity = $this->adsUser->getSingleAds($adsId);

        if($adsEntity instanceof \VigattinAds\DomainModel\Ads) {
            // $adsViewCount = $adsEntity->get('adsView')->count(); old count
            $adsViewCount = $adsEntity->get('viewCount');
            /** @var \Doctrine\Common\Collections\ArrayCollection $adsApproveLog */
            $adsApproveLog =  $adsEntity->get('adsApproveLog');
            if(strtolower($this->getRequest()->getPost('submit', '')) == 'next') {
                $formError = array(
                    'adsTitle' => $this->getRequest()->getPost('ads-title', ''),
                    'adsUrl' => $this->getRequest()->getPost('ads-url', ''),
                    'adsKeyword' => $this->getRequest()->getPost('ads-keyword', ''),
                    'adsTempKeyword' => $adsEntity->get('category'),
                    'adsPrice' => $this->getRequest()->getPost('ads-price', ''),
                    'adsDescription' => $this->getRequest()->getPost('ads-description', ''),
                    'adsTitleError' => Validator::isTitleValid($this->getRequest()->getPost('ads-title', '')),
                    'adsUrlError' => Validator::isUrlValid($this->getRequest()->getPost('ads-url', '')),
                    'adsKeywordError' => Validator::isKeywordValid($this->getRequest()->getPost('ads-keyword', '')),
                    'adsPriceError' => Validator::isNumber($this->getRequest()->getPost('ads-price', '')),
                    'adsDescriptionError' => Validator::isDescriptionValid($this->getRequest()->getPost('ads-description', '')),
                );
                if(!strlen($formError['adsTitleError'].$formError['adsUrlError'].$formError['adsKeywordError'].$formError['adsPriceError'].$formError['adsDescriptionError'])) {

                    $adsKeyword = $this->getRequest()->getPost('ads-keyword', '');
                    $featuredAds = $this->getRequest()->getPost('featuredAds', array());
                    $adsListing = $this->getRequest()->getPost('adsListing', array());

                    // check some value that need to verify first if change happen
                    $oldValue = strtolower($adsEntity->get('adsTitle').$adsEntity->get('adsUrl').$adsEntity->get('adsDescription'));
                    $newValue = strtolower($formError['adsTitle'].$formError['adsUrl'].$formError['adsDescription']);

                    $adsEntity->set('adsTitle', $formError['adsTitle']);
                    $adsEntity->set('adsUrl', $formError['adsUrl']);
                    if(strtolower($adsEntity->get('showIn')) == 'vigattintrade.com') $adsEntity->set('keywords', AdsWizardEditInfoController::processTradeAdditionalAdsPosition($this->getRequest()->getPost('selectedKeyword', array())));
                    else $adsEntity->set('keywords', $formError['adsKeyword']);
                    $adsEntity->set('adsPrice', $formError['adsPrice']);
                    $adsEntity->set('adsDescription', $formError['adsDescription']);
                    if($oldValue !== $newValue) {
                        // Change log to RE-EDIT status
                        /** @var \VigattinAds\DomainModel\AdsApproveLog $log */
                        $log = $this->adsManager->getLogByReviewVersion($adsEntity->get('reviewVersion'));
                        $log->set('reviewResult', $adsEntity::STATUS_VALUE_CHANGED);
                        $log->set('approvedTime', time());
                        $log->persistSelf();
                        $log->flush();

                        // Create new log status
                        $this->adsManager->changeAdsStatus($adsEntity->get('reviewVersion'), $adsEntity::STATUS_VALUE_CHANGED, '');
                        $adsEntity->set('reviewVersion', uniqid());
                        $adsEntity->set('status', $adsEntity::STATUS_PENDING);
                    }


                    $adsEntity->persistSelf();
                    $adsEntity->flush();
                }
            }
            elseif(strtolower($this->getRequest()->getPost('submit', '')) == 'delete') {
                $viewToGoldRate = floatval($this->settingsManager->get('viewToGoldRate'));
                $viewLimit = $adsEntity->get('viewLimit');
                if($viewLimit < 0) $viewLimit = 0;
                $adsEntity->set('viewLimit', 0);
                $this->adsUser->set('credit', floatval($this->adsUser->get('credit'))+($viewLimit*$viewToGoldRate));
                $adsEntity->deleteSelf();
                $adsEntity->flush();

                header('Location: /vigattinads/dashboard/ads');
                exit();
            }
            elseif(strtolower($this->getRequest()->getPost('submit', '')) == 'pause') {
                if($adsEntity->get('status') == $adsEntity::STATUS_APPROVED) {
                    $adsEntity->set('status', $adsEntity::STATUS_PAUSED);
                    $adsEntity->persistSelf();
                    $adsEntity->flush();
                }
                $formError = array(
                    'adsTitle' => $adsEntity->get('adsTitle'),
                    'adsUrl' => $adsEntity->get('adsUrl'),
                    'adsKeyword' => $adsEntity->get('keywords'),
                    'adsTempKeyword' => $adsEntity->get('category'),
                    'adsPrice' => $adsEntity->get('adsPrice'),
                    'adsDescription' => $adsEntity->get('adsDescription'),
                    'adsTitleError' => '',
                    'adsUrlError' => '',
                    'adsKeywordError' => '',
                    'adsPriceError' => '',
                    'adsDescriptionError' => '',
                );
            }
            elseif(strtolower($this->getRequest()->getPost('submit', '')) == 'resume') {
                if($adsEntity->get('status') == $adsEntity::STATUS_PAUSED) {
                    $adsEntity->set('status', $adsEntity::STATUS_APPROVED);
                    $adsEntity->persistSelf();
                    $adsEntity->flush();
                }
                $formError = array(
                    'adsTitle' => $adsEntity->get('adsTitle'),
                    'adsUrl' => $adsEntity->get('adsUrl'),
                    'adsKeyword' => $adsEntity->get('keywords'),
                    'adsTempKeyword' => $adsEntity->get('category'),
                    'adsPrice' => $adsEntity->get('adsPrice'),
                    'adsDescription' => $adsEntity->get('adsDescription'),
                    'adsTitleError' => '',
                    'adsUrlError' => '',
                    'adsPriceError' => '',
                    'adsKeywordError' => '',
                    'adsDescriptionError' => '',
                );
            }
            else {
                $formError = array(
                    'adsTitle' => $adsEntity->get('adsTitle'),
                    'adsUrl' => $adsEntity->get('adsUrl'),
                    'adsKeyword' => $adsEntity->get('keywords'),
                    'adsTempKeyword' => $adsEntity->get('category'),
                    'adsPrice' => $adsEntity->get('adsPrice'),
                    'adsDescription' => $adsEntity->get('adsDescription'),
                    'adsTitleError' => '',
                    'adsUrlError' => '',
                    'adsKeywordError' => '',
                    'adsPriceError' => '',
                    'adsDescriptionError' => '',
                );
            }
        } else {
            header('Location: /vigattinads/dashboard/ads');
            exit();
        }

        $this->mainView->setVariable('title', 'Ads Edit');
        $actionContent = new ViewModel();
        switch(strtolower($adsEntity->get('showIn'))) {
            case 'vigattintrade.com':
                if(strtolower($adsEntity->get('template')) == 'home-sidebar-left') {
                    $actionContent->setTemplate('vigattinads/view/dashboard/ads/adsEditWithCatView');
                }
                else {
                    $actionContent->setTemplate('vigattinads/view/dashboard/ads/adsEditView');
                }
                break;
            case 'vigattintourism.com':
                $actionContent->setTemplate('vigattinads/view/dashboard/ads/adsEditWithCatTourismView');
                break;
            case 'vigattin.com':
                $actionContent->setTemplate('vigattinads/view/dashboard/ads/adsEditVigattinView');
                break;
            default:
                $actionContent->setTemplate('vigattinads/view/dashboard/ads/adsEditView');
                break;
        }
        $actionContent->setVariables($formError);
        $actionContent->setVariable('ads', $adsEntity);
        $actionContent->setVariable('userManager', $this->userManager);
        $actionContent->setVariable('adsUser', $this->adsUser);
        $actionContent->setVariable('adsViewCount', $adsViewCount);
        $actionContent->setVariable('adsReviewReason', $adsEntity->getLastReviewReason());
        $actionContent->setVariable('request', $this->getRequest());

        $this->mainView->addChild($actionContent, 'actionContent');
        return $this->mainView;
    }
}
