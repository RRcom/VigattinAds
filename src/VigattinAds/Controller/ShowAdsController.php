<?php
namespace VigattinAds\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Mvc\MvcEvent;
use Zend\Crypt\BlockCipher;
use VigattinAds\Model\Ads\Ads as AdsModel;
use VigattinAds\Entity\AdsView;

class ShowAdsController extends AbstractActionController
{
    const COOKIE_NAME_VIEWS = 'hyd67YuhduZr';
    const COOKIE_EXPIRE_VIEWS = 3600;
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

    public function __construct()
    {
        $this->viewModel = new ViewModel();
    }

    public function indexAction()
    {
        return $this->sidebarAction();
    }

    public function sidebarAction()
    {
        $this->viewModel->setTemplate('vigattinads/view/show-ads-sidebar');
        $this->viewModel->setVariable('ads', $this->generateAds($this->request->getQuery('adsid', array())));
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
        $status = '';
        $data = $this->request->getPost('data', '');
        $data = $this->processData($data);
        $jsonView = new JsonModel();

        if(!is_array($data))
        {
            $status = 'invalid data';
            $jsonView->setVariable('status', $status);
            return $jsonView;
        }

        if($data['expire'] < time())
        {
            $status = 'data expire';
            $jsonView->setVariable('status', $status);
            return $jsonView;
        }

        $adsEntities = $this->generateAds($data['adsId']);

        foreach($adsEntities as $adsEntity)
        {
            $view = new AdsView();
            $view->setAds($adsEntity);
            $view->setClicked(false);
            $view->setViewTime(time());
            $view->setAdsReferrer($_SERVER['HTTP_REFERER']);
            $view->setBrowserId($_SERVER['REMOTE_ADDR'].'_'.$data['browserId']);
            $this->adsModel->getEntityManager()->persist($view);
        }
        $this->adsModel->getEntityManager()->flush();
        $jsonView->setVariable('status', 'success');
        return $jsonView;
    }

    public function processData($data)
    {
        $salt = isset($_COOKIE[self::COOKIE_NAME_VIEWS]) ? $_COOKIE[self::COOKIE_NAME_VIEWS] : '';
        $blockCipher = BlockCipher::factory('mcrypt', array('algo' => 'aes'));
        $blockCipher->setKey(self::ENCRYPTION_KEY.$salt);
        $data = $blockCipher->decrypt($data);
        return unserialize($data);
    }

    public function initViewSession()
    {
        $id = uniqid();
        setcookie(self::COOKIE_NAME_VIEWS, $id, time()+self::COOKIE_EXPIRE_VIEWS, "/vigattinads");
        return $id;
    }

    public function createSecretData($browserId, $adsId)
    {
        $data = array(
            'expire' => time()+self::DATA_EXPIRE,
            'browserId' => $browserId,
            'adsId' => $adsId,
        );
        $data = serialize($data);
        $blockCipher = BlockCipher::factory('mcrypt', array('algo' => 'aes'));
        $blockCipher->setKey(self::ENCRYPTION_KEY.$browserId);
        return $blockCipher->encrypt($data);
    }

    public function generateAds($adsIds)
    {
        return $this->adsModel->publicGetAds($adsIds);
    }

    public function onDispatch(MvcEvent $e) {
        $this->adsModel = new AdsModel($this->getServiceLocator());
        $adsId = $this->request->getQuery('adsid', array());
        $uniqueId = $this->initViewSession();
        $this->layout()->setTemplate('vigattinads/layout/ads');
        $this->layout()->setVariable('js', 'var data = "'.$this->createSecretData($uniqueId, $adsId).'"');
        return parent::onDispatch($e);
    }
}
