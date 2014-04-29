<?php
namespace VigattinAds\DomainModel;

use Zend\Cache\StorageFactory;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LongCacheServiceFactory implements FactoryInterface
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