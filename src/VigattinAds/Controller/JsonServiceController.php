<?php
namespace VigattinAds\Controller;

use VigattinAds\DomainModel\AdsManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\InArray;
use Zend\View\Model\JsonModel;
use VigattinAds\DomainModel\SettingsManager;
use VigattinAds\DomainModel\Ads;

class JsonServiceController extends AbstractActionController
{
    /** @var \VigattinAds\DomainModel\UserManager */
    protected $userManager;

    /** @var  \VigattinAds\DomainModel\AdsManager */
    protected $adsManager;

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
        if($requestViews < 0)
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
        $goldLeft = ($user->get('credit') + (($ads->get('viewLimit') - $requestViews) * $viewToGoldRate));
        if($goldLeft < 0)
        {
            $jsonResult['status'] = 'failed';
            $jsonResult['reason'] = 'not enough gold, please refresh this page to update current gold status';
            return $this->jsonView->setVariables($jsonResult);
        }

        $user->set('credit', $goldLeft);
        $user->persistSelf();
        $ads->set('viewLimit', $requestViews);
        $ads->persistSelf();
        $user->flush();

        $jsonResult['status'] = 'success';
        $jsonResult['reason'] = '';
        $jsonResult['gold'] = $user->get('credit');
        $jsonResult['views'] = $ads->get('viewLimit');

        // Update session
        $entityManager = $this->serviceLocator->get('Doctrine\ORM\EntityManager');
        $sessionManager = $this->serviceLocator->get('Zend\Session\SessionManager');
        $user->set('serviceManager', null);
        $user->set('entityManager', null);
        $entityManager->detach($user);
        $sessionManager->getStorage()->user = $user;

        return $this->jsonView->setVariables($jsonResult);
    }

    public function updateAccountAction()
    {
        $user = $this->userManager->getCurrentUser();
        if(!$user->hasPermit($user::PERMIT_ADMIN_ACCESS)) {
            $this->jsonView->setVariable('error', 'no access');
        }
        $id = $this->request['id'];
        $email = $this->request['email'];
        $username = $this->request['username'];
        $password = $this->request['password'];
        $repeatPassword = $this->request['repeatPassword'];
        $firstName = $this->request['firstName'];
        $lastName = $this->request['lastName'];
        $privilege = $this->request['privilege'];
        $gold = $this->request['gold'];
        $result = $this->userManager->updateUser($id, $email, $username, $firstName, $lastName, $gold, $privilege);
        if($password) {
            if($password != $repeatPassword) {
                $result['repeatPassword'] = 'repeat password not match';
                $result['status'] = 'failed';
            } else {
                $passResult = $this->userManager->changePassword($id, $password);
                if($passResult != 'success') {
                    $result['password'] = $passResult;
                    $result['status'] = 'failed';
                }
            }

        }
        $this->jsonView->setVariables($result);
        return $this->jsonView;
    }

    public function createAccountAction()
    {
        $user = $this->userManager->getCurrentUser();
        if(!$user->hasPermit($user::PERMIT_ADMIN_ACCESS)) {
            $this->jsonView->setVariable('error', 'no access');
        }
        $errors = array(
            'status' => '',
            'general' => '',
            'email' => '',
            'username' => '',
            'password' => '',
            'repeatPassword' => '',
            'firstName' => '',
            'lastName' => '',
        );
        $email = $this->request['email'];
        $username = $this->request['username'];
        $password = $this->request['password'];
        $repeatPassword = $this->request['repeatPassword'];
        $firstName = $this->request['firstName'];
        $lastName = $this->request['lastName'];
        $privilege = $this->request['privilege'];
        $gold = $this->request['gold'];
        if($password != $repeatPassword) {
            $errors['repeatPassword'] = 'repeat password not match';
            $errors['status'] = 'failed';
        } else {
            $result = $this->userManager->createUser($email, $username, $password, $firstName, $lastName, $gold, $privilege);
            if(!($result instanceof \VigattinAds\DomainModel\AdsUser)) {
                $errors = array_merge($errors, $result);
                $errors['status'] = 'failed';
            }
            else $errors['status'] = 'success';
        }
        $this->jsonView->setVariables($errors);
        return $this->jsonView;
    }

    public function getAdsHistoryAction()
    {
        $user = $this->userManager->getCurrentUser();
        if(!$user->hasPermit($user::PERMIT_ADMIN_ACCESS)) {
            $this->jsonView->setVariable('error', 'no access');
        }
        $adsManager = $this->adsManager;
        $id = $this->request['id'];
        $start = $this->request['start'];
        $limit = $this->request['limit'];
        $adsArray = $adsManager->searchAdsByIds(array($id));
        $result = array();
        if(count($adsArray)) {
            $ads = $adsArray[0];
            $result = $ads->getAdsHistory($start, $limit);
        }
        $this->jsonView->setVariables($result);
        return $this->jsonView;
    }

    public function adsChangeStatusAction()
    {
        $user = $this->userManager->getCurrentUser();
        if(!$user->hasPermit($user::PERMIT_ADMIN_ACCESS)) {
            $this->jsonView->setVariable('error', 'no access');
        }
        $adsManager = $this->adsManager;
        $id = $this->request['id'];
        $status = $this->request['status'];
        $reason = $this->request['reason'];
        $adsArray = $adsManager->searchAdsByIds(array($id));
        $result = array();
        if(count($adsArray)) {
            $ads = $adsArray[0];
            switch(strtolower($status)) {
                case 'pending':
                    $ads->set('reviewVersion', uniqid());
                    $ads->set('status', Ads::STATUS_PENDING);
                    $ads->persistSelf();
                    $ads->flush();
                    $result = array('status' => 'success', 'request' => 'pending', 'id' => $id);
                    break;
                case 'approve':
                    break;
                case 'disapprove':
                    break;
            }
        }
        $this->jsonView->setVariables($result);
        return $this->jsonView;
    }

    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        $this->userManager = $this->serviceLocator->get('VigattinAds\DomainModel\UserManager');
        $this->adsManager = $this->serviceLocator->get('VigattinAds\DomainModel\AdsManager');
        if(strtolower($this->params('param1')) == 'post') $this->request = $this->getRequest()->getPost();
        else $this->request = $this->getRequest()->getQuery();
        return parent::onDispatch($e);
    }
}
