<?php
namespace VigattinAds;

use Zend\Db\Sql\Predicate\Literal;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;
use VigattinAds\DomainModel\OrmEventListener;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        date_default_timezone_set('Asia/Manila');
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
            /* skip if call from command line */
            if($e->getRequest() instanceof \Zend\Console\Request) {
                return;
            }

            if(!preg_match('/^vigattinads*/', $e->getRouteMatch()->getMatchedRouteName())) return;
            switch(strtolower($e->getRouteMatch()->getParam('controller'))) {
                // if show ads only
                case strtolower('VigattinAds\Controller\ShowAds'):
                    break;

                // if comm controller call
                case strtolower('VigattinAds\Controller\Comm'):
                    break;

                // if show ads only
                case strtolower('VigattinAds\Controller\PageBlock\BlockNoGold'):
                    break;

                // if enter account home controller
                case strtolower('VigattinAds\Controller\Login'):
                    /** @var \VigattinAds\DomainModel\UserManager $userManager */
                    $userManager = $e->getApplication()->getServiceManager()->get('VigattinAds\DomainModel\UserManager');
                    if($userManager->isLogin()) {
                        Header('Location: /vigattinads');
                        exit();
                    }
                    break;

                // if enter any controller
                default:
                    $userManager = $e->getApplication()->getServiceManager()->get('VigattinAds\DomainModel\UserManager');
                    if(!$userManager->isLogin()) {
                        Header('Location: /vigattinads/login');
                        exit();
                    }

                    $currentUser = $userManager->getCurrentUser();
                    // if basic user only
                    if(!$currentUser->hasPermit($currentUser::PERMIT_ADMIN_ACCESS) && !$currentUser->hasPermit($currentUser::PERMIT_TO_APPROVE_ADS)) {
                        // if no gold redirect to no gold page block
                        /*
                        if(!$currentUser->get('credit')) {
                            Header('Location: /vigattinads/pageblock');
                            exit();
                        }
                        */
                    }

                    break;
            }
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
}