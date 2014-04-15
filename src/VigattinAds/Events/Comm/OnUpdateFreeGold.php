<?php
namespace VigattinAds\Events\Comm;

use Vigattin\Communicate\MessageInterface;
use VigattinAds\DomainModel\VauthAccountLocator;

class OnUpdateFreeGold implements MessageInterface
{

    protected $allowedIp = array(
        '54.251.240.140',   // vigattindeals.com
        '54.251.34.3',      // vigattintrade.com
        '54.251.103.124',   // vigattin.com
        '127.0.0.1',        // localhost
    );

    protected $enableIpFilter = true;

    protected $message;

    protected $status;

    protected $reason;

    protected $accountLocator;

    /**
     * @var \Zend\Http\PhpEnvironment\Request
     */
    protected $request;

    /**
     * @var \VigattinAds\DomainModel\UserManager
     */
    protected $userManager;

    /** @var \Zend\ServiceManager\ServiceManager */
    protected $serviceManager;

    public function __construct()
    {
        $this->accountLocator = new VauthAccountLocator();
    }

    /**
     * @param $message mixed The actual message from remote server.
     */
    public function setMessage($message)
    {
        // TODO: Implement setMessage() method.
        $this->message = $message;
    }

    /**
     * @param $status int Status code of the received message.
     */
    public function setStatus($status)
    {
        // TODO: Implement setStatus() method.
        $this->status = $status;
    }

    /**
     * @param $reason string Description if message has error.
     */
    public function setReason($reason)
    {
        // TODO: Implement setReason() method.
        $this->reason = $reason;
    }

    /**
     * Trigger when message receiving is complete
     */
    public function onReceived()
    {
        // Create default response
        $response = array(
            'status' => $this->status,
            'beforeGold' => 0,
            'createAccountError' => '',
        );

        // Check ip
        $clientIp = $this->request->getServer('REMOTE_ADDR');
        if($this->enableIpFilter) {
            foreach($this->allowedIp as $ip) {
                if($ip == $clientIp) {
                    $response['status'] = $this->status;
                    break;
                }
                $response['status'] = 'Sorry your not allowed to access this API';
            }
        }

        // Check message status
        if($response['status'] == 'ok') {
            $adsUserId = $this->accountLocator->hasAccount($this->message['id']);

            // if already have account in VigattinAds
            if($adsUserId) {
                $user = $this->userManager->getUser($adsUserId);
                $response['beforeGold'] = $user->get('credit');
                $user->set('credit', $user->get('credit')+floatval($this->message['gold']));
                $user->persistSelf();
                $user->flush();
            }

            // create new account
            else {
                $user = $this->userManager->createUser(
                    $this->message['email'],
                    $this->message['username'] ? $this->message['username'] : uniqid(),
                    uniqid(),
                    $this->message['firstName'] ? $this->message['firstName'] : 'no first name',
                    $this->message['lastName'] ? $this->message['lastName'] : 'no last name'
                );
                if($user instanceof \VigattinAds\DomainModel\AdsUser) {
                    $this->accountLocator->addAccount($this->message['id'], $user->get('id'));;
                    $user->set('credit', $user->get('credit')+floatval($this->message['gold']));
                    $user->persistSelf();
                    $user->flush();
                }
                else $response['createAccountError'] = $user;
            }

            // create response
            $response['clientIp'] = $this->request->getServer('REMOTE_ADDR');
            $response['addedGold'] = $this->message['gold'];
            $response['newGold'] = $user->get('credit');
            $response['clientMessage'] = $this->message;
        }

        // return response
        return array('OnUpdateFreeGold' => $response);
    }

    /**
     * @param mixed $dependencies
     */
    public function injectDependencies($dependencies)
    {
        // TODO: Implement injectDependencies() method.
        $this->userManager = $dependencies['userManager'];
        $this->serviceManager = $dependencies['serviceManager'];
        $this->request = $this->serviceManager->get('request');
        $this->accountLocator->set('serviceManager', $dependencies['serviceManager']);
    }

    public function isIpAllowed()
    {

    }
}