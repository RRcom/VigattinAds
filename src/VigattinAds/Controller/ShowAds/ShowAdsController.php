<?php
namespace VigattinAds\Controller\ShowAds;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Mvc\MvcEvent;
use Zend\Crypt\BlockCipher;
use VigattinAds\DomainModel\AdsManager;

class ShowAdsController extends AbstractActionController
{
    const COOKIE_NAME_VIEWS = 'hyd67YuhduZr';
    const COOKIE_EXPIRE_VIEWS = 300; // every 5 minutes
    const COOKIE_NAME_CLICK = 'N78TGdteiL37';
    const COOKIE_EXPIRE_CLICK = 3600;
    const ENCRYPTION_KEY = '4^O6lP%hdy';
    const DATA_EXPIRE = 60;

    /**
     * @var \Zend\View\Model\ViewModel
     */
    protected $viewModel;

    /**
     * @var AdsManager
     */
    protected $adsManager;

    /**
     * @var AdsEntity[]
     */
    protected $searchedAds;

    /**
     * 1 construct the controller
     */
    public function __construct()
    {
        $this->viewModel = new ViewModel();
    }

    public function debugAction()
    {
        return '';
    }

    /**
     * 2 initialize require method
     * @param MvcEvent $e
     * @return mixed
     */
    public function onDispatch(MvcEvent $e) {
        // Create instance of AdsManager
        $this->adsManager = new AdsManager($this->getServiceLocator());
        // Create browser id, if already exist reuse the id
        $this->initViewSession();
        // Set template to use by this controller
        $this->layout()->setTemplate('vigattinads/layout/ads');
        // Set body background color
        $this->layout()->setVariable('bgcolor', '#'.$this->request->getQuery('bgcolor', 'FFFFFF'));
        // Call parent onDispatch
        return parent::onDispatch($e);
    }

    public function indexAction()
    {
        return $this->sidebarFbStyleAction();
    }

    public function vigattinTilesAction()
    {
        $containerWidth = $this->request->getQuery('container-width', '');
        $this->layout()->setTemplate('vigattinads/layout/ads-no-padding');
        if(strtolower($this->request->getQuery('showin', '')) == 'preview') {
            $this->viewModel->setTemplate('vigattinads/view/show-preview-ads-vigattin-tiles');
            $this->viewModel->setVariable('containerWidth', $containerWidth);
            return $this->viewModel;
        }
        $this->viewModel->setTemplate('vigattinads/view/show-ads-vigattin-tiles');
        $this->viewModel->setVariable('containerWidth', $containerWidth);

        // Create list of ads entities based on query param provided in the url
        $this->searchedAds = $this->generateAds();
        $this->viewModel->setVariable('ads', $this->searchedAds);
        return $this->viewModel;
    }

    public function sidebarAction()
    {
        // Create list of ads entities based on query param provided in the url
        $this->searchedAds = $this->generateAds();
        $this->viewModel->setTemplate('vigattinads/view/show-ads-sidebar');
        $this->viewModel->setVariable('ads', $this->searchedAds);
        return $this->viewModel;
    }

    public function sidebarFbStyleAction()
    {
        $this->layout()->setTemplate('vigattinads/layout/ads-no-padding');
        if(strtolower($this->request->getQuery('showin', '')) == 'preview') {
            $this->viewModel->setTemplate('vigattinads/view/show-preview-ads-sidebar-fbstyle');
            return $this->viewModel;
        }
        $this->viewModel->setTemplate('vigattinads/view/show-ads-sidebar-fbstyle');

        // Create list of ads entities based on query param provided in the url
        $this->searchedAds = $this->generateAds();
        $this->viewModel->setVariable('ads', $this->searchedAds);
        return $this->viewModel;
    }

    public function bottomAction()
    {
        $this->viewModel->setTemplate('vigattinads/view/show-ads-sidebar');
        $this->viewModel->setVariable('actionContent', 'ads');
        return $this->viewModel;
    }

    public function tradeFeaturedAdsAction()
    {
        $this->layout()->setTemplate('vigattinads/layout/ads-no-padding');
        if(strtolower($this->request->getQuery('showin', '')) == 'preview') {
            $this->viewModel->setTemplate('vigattinads/view/show-preview-trade-featured-ads');
            return $this->viewModel;
        }
        $this->viewModel->setTemplate('vigattinads/view/show-trade-featured-ads');

        // Create list of ads entities based on query param provided in the url
        $this->searchedAds = $this->generateAds();
        $this->viewModel->setVariable('ads', $this->searchedAds);
        return $this->viewModel;
    }

    public function tradeAdsListingAction()
    {
        $this->layout()->setTemplate('vigattinads/layout/ads-no-padding');
        if(strtolower($this->request->getQuery('showin', '')) == 'preview') {
            $this->viewModel->setTemplate('vigattinads/view/show-preview-trade-ads-listing');
            return $this->viewModel;
        }
        $this->viewModel->setTemplate('vigattinads/view/show-trade-ads-listing');

        // Create list of ads entities based on query param provided in the url
        $this->searchedAds = $this->generateAds();
        $this->viewModel->setVariable('ads', $this->searchedAds);
        return $this->viewModel;
    }

    public function tradeSidebarAdsAction()
    {
        // Create list of ads entities based on query param provided in the url
        $this->searchedAds = $this->generateAds();
        $this->layout()->setTemplate('vigattinads/layout/ads-no-padding');
        $this->viewModel->setTemplate('vigattinads/view/show-ads-sidebar-fbstyle');
        $this->viewModel->setVariable('ads', $this->searchedAds);
        return $this->viewModel;
    }

    /**
     * Add view count to the ads being viewed
     * @return JsonModel
     */
    public function validateAction()
    {

        $startT = microtime(true);

        $jsonView = new JsonModel();
        $status = '';

        if(empty($_COOKIE[self::COOKIE_NAME_VIEWS]))
        {
            $jsonView->setVariable('status', 'I don`t know what to do!');
            return $jsonView;
        }

        $ids = $this->request->getPost('ids', array());
        if(!is_array($ids))
        {
            $status = 'invalid data';
            $jsonView->setVariable('status', $status);
            return $jsonView;
        }

        $adsEntities = $this->adsManager->searchAdsByIds($ids);

        foreach($adsEntities as $adsEntity)
        {
            $adsViewLimit = $adsEntity->get('viewLimit');
            if($adsViewLimit)
            {
                $adsView = $adsEntity->addView(
                    isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
                    $_SERVER['REMOTE_ADDR'].'_'.$_COOKIE[self::COOKIE_NAME_VIEWS],
                    $isClicked = false
                );
                $adsView->persistSelf();
                $adsEntity->set('viewLimit', $adsViewLimit - 1);
                $adsEntity->set('viewCount', $adsEntity->get('viewCount') + 1);
                $adsEntity->persistSelf();
            }
        }
        $this->adsManager->flush();
        $jsonView->setVariable('status', 'success');
        $jsonView->setVariable('time', microtime(true) - $startT);
        return $jsonView;
    }

    /**
     * Initialize session
     * @return string
     */
    public function initViewSession()
    {
        if(isset($_COOKIE[self::COOKIE_NAME_VIEWS])) return $_COOKIE[self::COOKIE_NAME_VIEWS];
        $id = uniqid();
        setcookie(self::COOKIE_NAME_VIEWS, $id, time()+self::COOKIE_EXPIRE_VIEWS, "/vigattinads");
        return $id;
    }

    /**
     * Generate ads from the supplied request data
     * @return \VigattinAds\DomainModel\Ads[]
     */
    public function generateAds()
    {
        $showIn = $this->request->getQuery('showin', '');
        $template = $this->request->getQuery('template', '');
        $keyword = $this->request->getQuery('keyword', '');
        $limit = $this->request->getQuery('limit', 8);
        return $this->adsManager->getRotationAds($showIn, $template, $keyword, $limit);
    }
}
