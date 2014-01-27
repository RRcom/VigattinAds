<?php
namespace VigattinAds;

use Zend\Db\Sql\Predicate\Literal;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Session\SessionManager;
use Zend\Session\Container;
use VigattinAds\DomainModel\OrmEventListener;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
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
            if(!preg_match('/^vigattinads*/', $e->getRouteMatch()->getMatchedRouteName())) return;
            switch(strtolower($e->getRouteMatch()->getParam('controller'))) {
                // if show ads only
                case strtolower('VigattinAds\Controller\ShowAds'):
                    break;

                // if show ads only
                case strtolower('VigattinAds\Controller\PageBlock\BlockNoGold'):
                    break;

                // if enter accounthome controller
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
                        if(!$currentUser->get('credit')) {
                            Header('Location: /vigattinads/pageblock');
                            exit();
                        }
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
        return array(
            'factories' => array(
                'Zend\Session\SessionManager' => function ($sm) {
                    $config = $sm->get('config');
                    if (isset($config['session'])) {
                        $session = $config['session'];

                        $sessionConfig = null;
                        if (isset($session['config'])) {
                            $class = isset($session['config']['class'])  ? $session['config']['class'] : 'Zend\Session\Config\SessionConfig';
                            $options = isset($session['config']['options']) ? $session['config']['options'] : array();
                            $sessionConfig = new $class();
                            $sessionConfig->setOptions($options);
                        }

                        $sessionStorage = null;
                        if (isset($session['storage'])) {
                            $class = $session['storage'];
                            $sessionStorage = new $class();
                        }

                        $sessionSaveHandler = null;
                        if (isset($session['save_handler'])) {
                            // class should be fetched from service manager since it will require constructor arguments
                            $sessionSaveHandler = $sm->get($session['save_handler']);
                        }

                        $sessionManager = new SessionManager($sessionConfig, $sessionStorage, $sessionSaveHandler);

                        if (isset($session['validator'])) {
                            $chain = $sessionManager->getValidatorChain();
                            foreach ($session['validator'] as $validator) {
                                $validator = new $validator();
                                $chain->attach('session.validate', array($validator, 'isValid'));

                            }
                        }
                    } else {
                        $sessionManager = new SessionManager();
                    }
                    Container::setDefaultManager($sessionManager);
                    return $sessionManager;
                },
                'VigattinAds\DomainModel\UserManager' => function($sm) {
                        return new \VigattinAds\DomainModel\UserManager($sm);
                },
                'VigattinAds\DomainModel\AdsManager' => function($sm) {
                    return new \VigattinAds\DomainModel\AdsManager($sm);
                }
            ),
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