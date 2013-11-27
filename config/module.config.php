<?php
return array(
    'router' => array(
        'routes' => array(
            'vigattinads' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/vigattinads[/][:controller][/][:action]',
                    'constraints' => array(
                        'controller'    => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'VigattinAds\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'VigattinAds\Controller\Index' => 'VigattinAds\Controller\IndexController',
            'VigattinAds\Controller\Debug' => 'VigattinAds\Controller\DebugController',
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'vigattinads/layout/default'    => __DIR__ . '/../view/vigattinads/layout/default.phtml',
            'vigattinads/view/index' => __DIR__ . '/../view/vigattinads/view/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            'vigattinads_entities' => array(
                'class' =>'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__ . '/../src/VigattinAds/Entity',
                ),
            ),
            'orm_default' => array(
                'drivers' => array(
                    'VigattinAds\Entity' => 'vigattinads_entities',
                ),
            ),
        ),
    ),
);
