<?php
namespace VigattinAds\Controller\Dashboard\Ads\Create\ChooseWebsite\Import\CreateDraft;

use VigattinAds\Controller\Dashboard\Ads\AdsController;
use VigattinAds\Controller\Dashboard\Ads\Edit\EditController;
use VigattinAds\DomainModel\Image;
use VigattinAds\Controller\Dashboard\Ads\Create\ChooseWebsite\ChooseWebsiteController;

class CreateDraftController extends AdsController
{
    const DEFAULT_IMAGE = '/default/no-image.jpg';

    public function indexAction()
    {
        $siteCat = $this->createCategory();
        $this->redirectNoTemplate();
        $newAds = $this->adsUser->createAds(
            $this->sessionManager->getStorage()->tempAdsTitle,
            $this->sessionManager->getStorage()->tempAdsUrl,
            $this->createImage(),
            $this->sessionManager->getStorage()->tempAdsDescription,
            $this->sessionManager->getStorage()->tempAdsTemplate['showIn'],
            $this->sessionManager->getStorage()->tempAdsTemplate['template'],
            $siteCat['keywords'],
            $this->sessionManager->getStorage()->tempAdsPrice,
            $siteCat['category'],
            $this->sessionManager->getStorage()->tempAdsDate
        );
        $this->adsUser->flush();
        $this->clearTempData();
        $this->redirect()->toRoute('vigattinads_dashboard_ads_edit', array('param1' => $newAds->get('id')));
    }

    public function createImage()
    {
        $image = new Image(EditController::IMAGE_REPO);
        $result = $image->save_convert_resize(
            $this->sessionManager->getStorage()->tempAdsImageDataUrl,
            EditController::IMAGE_WIDTH,
            EditController::IMAGE_QUALITY,
            EditController::IMAGE_PROGRESSIVE
        );
        if($result['status'] != 'success')
        {
            return self::DEFAULT_IMAGE;
        }
        return $result['path'];
    }

    public function createCategory()
    {
        switch(strtolower($this->sessionManager->getStorage()->tempAdsTemplate['showIn'])) {
            case strtolower(ChooseWebsiteController::VIGATTINTRADE):
                if($this->sessionManager->getStorage()->tempAdsKeyword) return array('category' => $this->sessionManager->getStorage()->tempAdsKeyword, 'keywords' => '');
                return array('category' => 'homepage|general category|all', 'keywords' => '');
            case strtolower(ChooseWebsiteController::VIGATTINTOURISM):
                return array('category' => 'Homepage|Destinations|Articles|Tourist Spots|Discussion|Directory', 'keywords' => '');
            case strtolower(ChooseWebsiteController::VIGATTIN):
                return array('category' => 'Homepage', 'keywords' => '');
            case strtolower(ChooseWebsiteController::TOURISMBLOGGER):
                $categories = array('header', 'rightside', 'footer');
                $authorId = explode(',', $this->sessionManager->getStorage()->tempAdsAuthorId);
                $keywords = "";
                foreach($categories as $category) {
                    foreach($authorId as $id) {
                        $keywords .= strtolower("(tourism article $category $id)");
                    }
                }
                return array('category' => '', 'keywords' => $keywords);
            default:
                return array('category' => '', 'keywords' => '');
                break;
        }
    }

    public function redirectNoTemplate()
    {
        if(empty($this->sessionManager->getStorage()->tempAdsTemplate['showIn']) || empty($this->sessionManager->getStorage()->tempAdsTemplate['template'])) {
            $this->redirect()->toRoute('vigattinads_dashboard_ads_create_choose_website');
        }
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
