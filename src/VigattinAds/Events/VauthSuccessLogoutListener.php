<?php
namespace VigattinAds\Events;

use Vigattin\Events\EventAwareInterface;
use Vigattin\Vauth\Vauth;
use VigattinAds\DomainModel\UserManager;
use Zend\ServiceManager\ServiceLocatorInterface;

class VauthSuccessLogoutListener implements EventAwareInterface
{
    /**
     * @var \Zend\ServiceManager\ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var \VigattinAds\DomainModel\UserManager
     */
    protected $userManager;

    public function __construct(ServiceLocatorInterface $serviceLocator = null)
    {
        $this->serviceLocator = $serviceLocator;
        $this->userManager = $this->serviceLocator->get('VigattinAds\DomainModel\UserManager');
    }

    public function onEventTrigger(Vauth $vauth)
    {
        $this->userManager->logout();
        echo "Success logout event triggered\n";
    }
}