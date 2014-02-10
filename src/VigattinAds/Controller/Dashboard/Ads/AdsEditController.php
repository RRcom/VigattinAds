<?php
namespace VigattinAds\Controller\Dashboard\Ads;

use Zend\View\Model\ViewModel;
use VigattinAds\DomainModel\Validator;
use VigattinAds\DomainModel\VigattinTrade\Category as TradeCategory;

class AdsEditController extends AdsController
{
    public function indexAction()
    {
        $tradeCategory = new TradeCategory();
        $adsId = $this->params('param1', '');
        $adsViewCount = 0;
        $adsEntity = $this->adsUser->getSingleAds($adsId);

        if($adsEntity instanceof \VigattinAds\DomainModel\Ads) {
            $adsViewCount = $adsEntity->get('adsView')->count();
            if(strtolower($this->getRequest()->getPost('submit', '')) == 'next') {
                $formError = array(
                    'adsTitle' => $this->getRequest()->getPost('ads-title', ''),
                    'adsUrl' => $this->getRequest()->getPost('ads-url', ''),
                    'adsKeyword' => $this->getRequest()->getPost('ads-keyword', ''),
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
                    $oldValue = strtolower($adsEntity->get('adsTitle').$adsEntity->get('adsUrl').$adsEntity->get('keywords').$adsEntity->get('adsDescription'));
                    $newValue = strtolower($formError['adsTitle'].$formError['adsUrl'].$formError['adsKeyword'].$formError['adsDescription']);

                    $adsEntity->set('adsTitle', $formError['adsTitle']);
                    $adsEntity->set('adsUrl', $formError['adsUrl']);
                    $adsEntity->set('keywords', $formError['adsKeyword']);
                    $adsEntity->set('adsPrice', $formError['adsPrice']);
                    $adsEntity->set('adsDescription', $formError['adsDescription']);
                    if($oldValue !== $newValue) {
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
        $actionContent->setTemplate('vigattinads/view/dashboard/ads/adsEditWithCatView');
        $actionContent->setVariables($formError);
        $actionContent->setVariable('ads', $adsEntity);
        $actionContent->setVariable('userManager', $this->userManager);
        $actionContent->setVariable('adsUser', $this->adsUser);
        $actionContent->setVariable('adsViewCount', $adsViewCount);
        $actionContent->setVariable('tradeCategoryList', $tradeCategory->getCategoryList());

        $this->mainView->addChild($actionContent, 'actionContent');
        return $this->mainView;
    }
}
