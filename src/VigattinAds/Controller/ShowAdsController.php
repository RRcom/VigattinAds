<?php
namespace VigattinAds\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Mvc\MvcEvent;
use Zend\Crypt\BlockCipher;
use VigattinAds\Model\Ads\Ads as AdsModel;
use VigattinAds\Entity\AdsView;
use VigattinAds\Entity\Ads as AdsEntity;

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
     * @var AdsModel
     */
    protected $adsModel;

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

    /**
     * 2 initialize require method
     * @param MvcEvent $e
     * @return mixed
     */
    public function onDispatch(MvcEvent $e) {
        // Create instance of adsModel
        $this->adsModel = new AdsModel($this->getServiceLocator());
        // Create browser id, if already exist reuse the id
        $this->initViewSession();
        // Create list of ads entities based on query param provided in the url
        $this->searchedAds = $this->generateAds();
        // Set template to use by this controller
        $this->layout()->setTemplate('vigattinads/layout/ads');
        // Call parent onDispatch
        return parent::onDispatch($e);
    }

    public function indexAction()
    {
        return $this->sidebarAction();
    }

    public function sidebarAction()
    {
        $this->viewModel->setTemplate('vigattinads/view/show-ads-sidebar');
        $this->viewModel->setVariable('ads', $this->searchedAds);
        return $this->viewModel;
    }

    public function bottomAction()
    {
        $this->viewModel->setTemplate('vigattinads/view/show-ads-sidebar');
        $this->viewModel->setVariable('actionContent', 'ads');
        return $this->viewModel;
    }

    public function validateAction()
    {
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

        $adsEntities = $this->adsModel->publicGetAds($ids);

        foreach($adsEntities as $adsEntity)
        {
            $view = new AdsView();
            $view->setAds($adsEntity);
            $view->setClicked(false);
            $view->setViewTime(time());
            $view->setAdsReferrer($_SERVER['HTTP_REFERER']);
            $view->setBrowserId($_SERVER['REMOTE_ADDR'].'_'.$_COOKIE[self::COOKIE_NAME_VIEWS]);
            $this->adsModel->getEntityManager()->persist($view);
        }
        $this->adsModel->getEntityManager()->flush();
        $jsonView->setVariable('status', 'success');
        return $jsonView;
    }

    public function initViewSession()
    {
        if(isset($_COOKIE[self::COOKIE_NAME_VIEWS])) return $_COOKIE[self::COOKIE_NAME_VIEWS];
        $id = uniqid();
        setcookie(self::COOKIE_NAME_VIEWS, $id, time()+self::COOKIE_EXPIRE_VIEWS, "/vigattinads");
        return $id;
    }

    public function generateAds()
    {
        $showIn = $this->request->getQuery('showin', '');
        $template = $this->request->getQuery('template', '');
        $keyword = $this->request->getQuery('keyword', '');
        return $this->adsModel->publicSearchAds($showIn, $template, $keyword);
    }
}
