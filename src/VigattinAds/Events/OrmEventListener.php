<?php
namespace VigattinAds\Events;

use Zend\ServiceManager\ServiceManager;
use Doctrine\Common\EventManager;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\LifecycleEventArgs;

class OrmEventListener
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    public $serviceManager;

    public function __construct(ServiceManager $serviceManager,EventManager $em)
    {
        $this->serviceManager = $serviceManager;
        $em->addEventListener(Events::postLoad, $this);
    }

    public function postLoad(LifecycleEventArgs $eArg)
    {
        $entity = $eArg->getEntity();
        if(method_exists($entity, 'set')) {
            $entity->set('serviceManager', $this->serviceManager);
        }
    }
}