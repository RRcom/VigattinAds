<?php
namespace VigattinAds\DomainModel;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Vigattin\Vauth\Vauth;
use Vigattin\Events\Events;
use VigattinAds\DomainModel\VauthSuccessListener;
use VigattinAds\DomainModel\VauthSuccessLogoutListener;

class VauthServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $events = new Events();
        $events->register(Events::EVENT_SUCCESS_LOGIN, new VauthSuccessListener($serviceLocator));
        $events->register(Events::EVENT_SUCCESS_LOGOUT, new VauthSuccessLogoutListener($serviceLocator));
        return new Vauth($events);
    }

}