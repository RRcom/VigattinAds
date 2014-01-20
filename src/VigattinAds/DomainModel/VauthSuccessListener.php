<?php
namespace VigattinAds\DomainModel;

use Vigattin\Events\EventAwareInterface;
use Vigattin\Vauth\Vauth;

class VauthSuccessListener implements EventAwareInterface
{
    public function onEventTrigger(Vauth $vauth)
    {

    }
}