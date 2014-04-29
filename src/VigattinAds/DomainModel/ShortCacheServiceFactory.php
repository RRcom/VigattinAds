<?php
namespace VigattinAds\DomainModel;

use Zend\Cache\StorageFactory;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ShortCacheServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return StorageFactory::factory(
            array(
                'adapter' => array(
                    'name' => 'filesystem',
                    'options' => array('ttl' => 60),
                ),
                'plugins' => array(
                    'exception_handler' => array(
                        'throw_exceptions' => false
                    ),
                )
            )
        );
    }
}