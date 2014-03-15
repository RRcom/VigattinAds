<?php
namespace VigattinAds\Events\Comm;

use Vigattin\Communicate\MessageInterface;
use VigattinAds\DomainModel\VauthAccountLocator;

class OnUpdateFreeGold implements MessageInterface
{

    protected $message;

    protected $status;

    protected $reason;

    protected $accountLocator;

    /**
     * @var \VigattinAds\DomainModel\UserManager
     */
    protected $userManager;

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
        // TODO: Implement onReceived() method.
        if($this->status == 'ok') {
            $adsUserId = $this->accountLocator->hasAccount($this->message['id']);
            // if already have account in VigattinAds
            if($adsUserId) {
                $user = $this->userManager->getUser($adsUserId);
                $user->set('credit', $user->get('credit')+floatval($this->message['gold']));
                $user->persistSelf();
                $user->flush();
            }
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
            }
        }
    }

    /**
     * @param mixed $dependencies
     */
    public function injectDependencies($dependencies)
    {
        // TODO: Implement injectDependencies() method.
        $this->userManager = $dependencies['userManager'];
        $this->serviceManager = $dependencies['serviceManager'];
        $this->accountLocator->set('serviceManager', $dependencies['serviceManager']);
    }
}