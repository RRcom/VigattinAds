<?php
namespace VigattinAds\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use VigattinAds\DomainModel\SettingsManager;

class JsonServiceController extends AbstractActionController
{
    /**
     * @var \Zend\View\Model\JsonModel
     */
    protected $jsonView;

    /**
     * @var array;
     */
    protected $request;

    public function __construct()
    {
        $this->jsonView = new JsonModel();
    }

    public function indexAction()
    {
        $this->jsonView->setVariable('message', 'Welcome');
        return $this->jsonView;
    }

    public function verifyAction()
    {
        $action =  isset($this->request['action']) ? $this->request['action'] : '';
        $data = isset($this->request['data']) ? $this->request['data'] : '';
        switch($action)
        {
            case 'ads_title':
                //$error = Helper\Validator::isTitleValid($data);
                break;
            case 'ads_url':
                //$error = Helper\Validator::isUrlValid($data);
                break;
            case 'ads_description':
                //$error = Helper\Validator::isDescriptionValid($data);
                break;
            case 'ads_keywords':
                //$error = Helper\Validator::isKeywordValid($data);
                break;
        }
        $result = array(
            //status' => $error ? 'failed' : 'success',
            //'reason' => $error,
        );
        $this->jsonView->setVariables($result);
        return $this->jsonView;
    }

    public function addViewCreditAction()
    {
        $jsonResult = array(
            'status' => 'failed',
            'reason' => 'unknown error',
        );

        $requestViews = intval($this->request['requestViews']);
        if($requestViews < 1)
        {
            $jsonResult['status'] = 'failed';
            $jsonResult['reason'] = 'view request must be higher than 0';
            return $this->jsonView->setVariables($jsonResult);
        }
        $adsId = intval($this->request['adsId']);

        $settingManager = new SettingsManager($this->serviceLocator);
        $viewToGoldRate = floatval($settingManager->get('viewToGoldRate'));

        /** @var $user \VigattinAds\DomainModel\AdsUser */
        $user = $this->serviceLocator->get('VigattinAds\DomainModel\UserManager')->getCurrentUser();
        if(!($user instanceof \VigattinAds\DomainModel\AdsUser))
        {
            $jsonResult['status'] = 'failed';
            $jsonResult['reason'] = 'user not found';
            return $this->jsonView->setVariables($jsonResult);
        }
        $user->refresh();

        /** @var $ads \VigattinAds\DomainModel\Ads */
        $ads = $user->getSingleAds($adsId);
        if(!($ads instanceof \VigattinAds\DomainModel\Ads))
        {
            $jsonResult['status'] = 'failed';
            $jsonResult['reason'] = 'ads not found';
            return $this->jsonView->setVariables($jsonResult);
        }

        // calculate how many gold left;
        $goldLeft = ($user->get('credit') - ($requestViews * $viewToGoldRate));
        if($goldLeft < 0)
        {
            $jsonResult['status'] = 'failed';
            $jsonResult['reason'] = 'not enough gold';
            return $this->jsonView->setVariables($jsonResult);
        }

        $user->set('credit', $goldLeft);
        $user->persistSelf();
        $ads->set('viewLimit', $ads->get('viewLimit') + $requestViews);
        $ads->persistSelf();
        $user->flush();

        $jsonResult['status'] = 'success';
        $jsonResult['reason'] = '';
        $jsonResult['gold'] = $user->get('credit');
        $jsonResult['views'] = $ads->get('viewLimit');
        return $this->jsonView->setVariables($jsonResult);
    }

    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        if(strtolower($this->params('param1')) == 'post') $this->request = $this->getRequest()->getPost();
        else $this->request = $this->getRequest()->getQuery();
        return parent::onDispatch($e);
    }
}
