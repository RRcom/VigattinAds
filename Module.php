<?php
namespace VigattinAds;

use Zend\Db\Sql\Predicate\Literal;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;
use VigattinAds\Events\OrmEventListener;
use VigattinAds\Events\OnViewRender;
use VigattinAds\Events\OnErrorViewRender;
use VigattinAds\Events\OnRoute;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        date_default_timezone_set('Asia/Manila');
        $this->initRenderEvents($e);
        $application = $e->getApplication();
        $eventManager = $application->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        $this->bootstrapSession($e);
        $this->dispatchRouter($e);
        $this->initDoctrineEvents($e);
        $this->catchVauthLogin($e);
    }

    public function catchVauthLogin(MvcEvent $e)
    {
        $e->getApplication()->getServiceManager()->get('Vigattin\Vauth\Vauth');
    }

    public function dispatchRouter(MvcEvent $e)
    {
        $e->getApplication()->getEventManager()->attach(MvcEvent::EVENT_ROUTE,function(MvcEvent $e)
        {
            new OnRoute($e);
        });
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function bootstrapSession($e)
    {
        $session = $e->getApplication()
            ->getServiceManager()
            ->get('Zend\Session\SessionManager');
        $session->start();

        $container = new Container('initialized');
        if (!isset($container->init)) {
            $session->regenerateId(true);
            $container->init = 1;
        }
    }

    public function getServiceConfig()
    {
        return array (
        );
    }

    public function initDoctrineEvents(MvcEvent $e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();
        /** @var $entityManager \Doctrine\ORM\EntityManager */
        $entityManager = $serviceManager->get('Doctrine\ORM\EntityManager');
        $ormEventListener = new OrmEventListener($serviceManager, $entityManager->getEventManager());
    }

    public function initRenderEvents(MvcEvent $e)
    {
        $e->getApplication()->getEventManager()->attach(MvcEvent::EVENT_RENDER, function(MvcEvent $e)
        {
            new OnViewRender($e);
        });
        $e->getApplication()->getEventManager()->attach(MvcEvent::EVENT_RENDER_ERROR, function(MvcEvent $e)
        {
            new OnErrorViewRender($e);
        });
    }
}