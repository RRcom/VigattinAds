<?php
namespace VigattinAds\Controller;

use VigattinAds\DomainModel\AdsManager;
use VigattinAds\DomainModel\AdsUser;
use VigattinAds\DomainModel\CommonLog;
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

    /** @var \VigattinAds\DomainModel\LogManager */
    protected $logManager;

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
        $targetUser = $this->userManager->getUser($id);
        $targetUserEmail = ($targetUser instanceof AdsUser) ? $targetUser->get('email') : '';
        $result = $this->userManager->updateUser($id, $email, $username, $firstName, $lastName, $gold, $privilege);

        if($result['status'] == 'success') {
            // Create log info
            $logInfo = $user->get('username').','.$user->get('firstName').' '.$user->get('lastName').','.$user->get('email').',';

            // Log edit email
            if($result['old_email'] != $result['new_email']) {
                $logMessage = $logInfo.'User email altered. Old value '.$result['old_email'].' new value '.$result['new_email'].','.$targetUserEmail;
                $this->logManager->createCommonLog($user, CommonLog::LOG_TYPE_ALTER_EMAIL, $logMessage, $id, true);
            }

            // Log edit username
            if($result['old_username'] != $result['new_username']) {
                $logMessage = $logInfo.'User username altered. Old value '.$result['old_username'].' new value '.$result['new_username'].','.$targetUserEmail;
                $this->logManager->createCommonLog($user, CommonLog::LOG_TYPE_ALTER_USERNAME, $logMessage, $id, true);
            }

            // Log edit firstName
            if($result['old_firstName'] != $result['new_firstName']) {
                $logMessage = $logInfo.'User first name altered. Old value '.$result['old_firstName'].' new value '.$result['new_firstName'].','.$targetUserEmail;
                $this->logManager->createCommonLog($user, CommonLog::LOG_TYPE_ALTER_FIRST_NAME, $logMessage, $id, true);
            }

            // Log edit lastName
            if($result['old_lastName'] != $result['new_lastName']) {
                $logMessage = $logInfo.'User last name altered. Old value '.$result['old_lastName'].' new value '.$result['new_lastName'].','.$targetUserEmail;
                $this->logManager->createCommonLog($user, CommonLog::LOG_TYPE_ALTER_LAST_NAME, $logMessage, $id, true);
            }

            // Log edit privilege
            if(strtolower($result['old_privilege']) != strtolower($result['new_privilege'])) {
                $logMessage = $logInfo.'User privilege altered. Old value '.$result['old_privilege'].' new value '.$result['new_privilege'].','.$targetUserEmail;
                $this->logManager->createCommonLog($user, CommonLog::LOG_TYPE_ALTER_PRIVILEGE, $logMessage, $id, true);
            }

            // Log edit credit
            if($result['old_credit'] != $result['new_credit']) {
                $logMessage = $logInfo.'User gold altered. Old value '.$result['old_credit'].' new value '.$result['new_credit'].','.$targetUserEmail;
                $this->logManager->createCommonLog($user, CommonLog::LOG_TYPE_ALTER_GOLD, $logMessage, $id, true);
            }
        }

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
                else {
                    // Log edit password
                    $logMessage = $logInfo.'User password altered.'.','.$targetUserEmail;
                    $this->logManager->createCommonLog($user, CommonLog::LOG_TYPE_ALTER_PASSWORD, $logMessage, $id, true);
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

    public function getAccountHistoryAction()
    {
        $user = $this->userManager->getCurrentUser();
        if(!$user->hasPermit($user::PERMIT_ADMIN_ACCESS)) {
            $this->jsonView->setVariable('error', 'no access');
            return $this->jsonView;
        }
        $targetUser = $this->userManager->getUser($this->request['id']);
        if(!($targetUser instanceof AdsUser)) {
            $this->jsonView->setVariable('error', 'cant find target user');
            return $this->jsonView;
        }
        $historyArray = $this->logManager->fetchCommonLogByUser($targetUser, $this->request['start'], $this->request['limit']);
        $this->jsonView->setVariable('result', $historyArray);
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
                    $version = uniqid();
                    $ads->set('reviewVersion', $version);
                    $ads->set('status', Ads::STATUS_PENDING);
                    $ads->persistSelf();
                    $ads->flush();
                    $result = array('status' => 'success', 'request' => 'pending', 'id' => $id);
                    break;
                case 'approve':
                    $version = uniqid();
                    $ads->set('reviewVersion', $version);
                    $ads->set('status', Ads::STATUS_REVIEWING);
                    $ads->persistSelf();
                    $ads->flush();

                    $adsManager->createReviewLog($user, $ads, $version, Ads::STATUS_REVIEWING, '');
                    $adsManager->flush();

                    $adsManager->changeAdsStatus($version, Ads::STATUS_APPROVED, $reason);
                    $result = array('status' => 'success', 'request' => 'approve', 'id' => $id);
                    break;
                case 'disapprove':
                    $version = uniqid();
                    $ads->set('reviewVersion', $version);
                    $ads->set('status', Ads::STATUS_REVIEWING);
                    $ads->persistSelf();
                    $ads->flush();

                    $adsManager->createReviewLog($user, $ads, $version, Ads::STATUS_REVIEWING, '');
                    $adsManager->flush();

                    $adsManager->changeAdsStatus($version, Ads::STATUS_DISAPPROVED, $reason);
                    $result = array('status' => 'success', 'request' => 'disapprove', 'id' => $id);
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
        $this->logManager = $this->serviceLocator->get('VigattinAds\DomainModel\LogManager');
        if(strtolower($this->params('param1')) == 'post') $this->request = $this->getRequest()->getPost();
        else $this->request = $this->getRequest()->getQuery();
        return parent::onDispatch($e);
    }
}
