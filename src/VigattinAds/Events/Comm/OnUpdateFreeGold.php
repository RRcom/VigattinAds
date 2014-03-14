<?php
namespace VigattinAds\Events\Comm;

use Vigattin\Communicate\MessageInterface;

class OnUpdateFreeGold implements MessageInterface
{

    /**
     * @param $message mixed The actual message from remote server.
     */
    public function setMessage($message)
    {
        // TODO: Implement setMessage() method.
    }

    /**
     * @param $status int Status code of the received message.
     */
    public function setStatus($status)
    {
        // TODO: Implement setStatus() method.
    }

    /**
     * @param $reason string Description if message has error.
     */
    public function setReason($reason)
    {
        // TODO: Implement setReason() method.
    }

    /**
     * Trigger when message receiving is complete
     */
    public function onReceived()
    {
        // TODO: Implement onReceived() method.
    }
}