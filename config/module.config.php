<?php
return array(
    'router' => array(
        'routes' => array(
            'vigattinads' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/vigattinads[/]',
                    'defaults' => array(
                        'controller'    => 'VigattinAds\Controller\Dashboard',
                        'action'        => 'index',
                    ),
                ),
            ),
            'vigattinads/login' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/vigattinads/login[/]',
                    'defaults' => array(
                        'controller'    => 'VigattinAds\Controller\Login',
                        'action'        => 'index',
                    ),
                ),
            ),
            'vigattinads/logout' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/vigattinads/logout[/]',
                    'defaults' => array(
                        'controller'    => 'VigattinAds\Controller\Logout',
                        'action'        => 'index',
                    ),
                ),
            ),
            'vigattinads/dashboard' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/vigattinads/dashboard[/]',
                    'defaults' => array(
                        'controller'    => 'VigattinAds\Controller\Dashboard',
                        'action'        => 'index',
                    ),
                ),
            ),
            'vigattinads/dashboard/ads' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/vigattinads/dashboard/ads[/][:controller][/][:action][/][:param1][/][:param2]',
                    'constraints' => array(
                        'controller'    => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'param1'        => '[a-zA-Z0-9_-]*',
                        'param2'        => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'VigattinAds\Controller\Dashboard',
                        'controller'    => 'Ads',
                        'action'        => 'index',
                    ),
                ),
            ),
            'vigattinads/dashboard/ads/edit' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/vigattinads/dashboard/ads/edit[/][:controller][/][:param1][/][:param2]',
                    'constraints' => array(
                        'controller'    => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'param1'        => '[a-zA-Z0-9_-]*',
                        'param2'        => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'VigattinAds\Controller\Dashboard\Ads',
                        'controller'    => 'Edit',
                        'action'        => 'index',
                    ),
                ),
            ),
            'vigattinads/dashboard/ads/template' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/vigattinads/dashboard/ads/template[/][:controller]',
                    'constraints' => array(
                        'controller'    => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'VigattinAds\Controller\Dashboard\Ads',
                        'controller'    => 'Template',
                        'action'        => 'index',
                    ),
                ),
            ),
            'vigattinads/dashboard/ads/info' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/vigattinads/dashboard/ads/info[/][:controller]',
                    'constraints' => array(
                        'controller'    => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'VigattinAds\Controller\Dashboard\Ads',
                        'controller'    => 'Info',
                        'action'        => 'index',
                    ),
                ),
            ),
            'vigattinads/dashboard/profile' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/vigattinads/dashboard/profile[/][:controller][/][:action][/][:param1][/][:param2]',
                    'constraints' => array(
                        'controller'    => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'param1'        => '[a-zA-Z0-9_-]*',
                        'param2'        => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'VigattinAds\Controller\Dashboard',
                        'controller'    => 'Profile',
                        'action'        => 'index',
                    ),
                ),
            ),
            'vigattinads/dashboard/admin' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/vigattinads/dashboard/admin[/][:controller][/][:action][/][:param1][/][:param2]',
                    'constraints' => array(
                        'controller'    => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'param1'        => '[a-zA-Z0-9_-]*',
                        'param2'        => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'VigattinAds\Controller\Dashboard',
                        'controller'    => 'Admin',
                        'action'        => 'index',
                    ),
                ),
            ),
            'vigattinads/dashboard/approval' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/vigattinads/dashboard/approval[/][:controller][/][:action][/][:param1][/][:param2]',
                    'constraints' => array(
                        'controller'    => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'param1'        => '[a-zA-Z0-9_-]*',
                        'param2'        => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'VigattinAds\Controller\Dashboard',
                        'controller'    => 'Approval',
                        'action'        => 'index',
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'VigattinAds\Controller\Login'   => 'VigattinAds\Controller\LoginController',
            'VigattinAds\Controller\Logout'   => 'VigattinAds\Controller\LogoutController',
            'VigattinAds\Controller\Dashboard' => 'VigattinAds\Controller\Dashboard\DashboardController',
            'VigattinAds\Controller\Dashboard\Ads'   => 'VigattinAds\Controller\Dashboard\Ads\AdsController',
            'VigattinAds\Controller\Dashboard\Ads\Edit'   => 'VigattinAds\Controller\Dashboard\Ads\AdsEditController',
            'VigattinAds\Controller\Dashboard\Ads\Template'   => 'VigattinAds\Controller\Dashboard\Ads\AdsWizardChooseTemplateController',
            'VigattinAds\Controller\Dashboard\Ads\Info'   => 'VigattinAds\Controller\Dashboard\Ads\AdsWizardEditInfoController',
            'VigattinAds\Controller\Dashboard\Profile'   => 'VigattinAds\Controller\Dashboard\Profile\ProfileController',
            'VigattinAds\Controller\Dashboard\Admin'   => 'VigattinAds\Controller\Dashboard\Admin\AdminController',
            'VigattinAds\Controller\Dashboard\Approval'   => 'VigattinAds\Controller\Dashboard\Approval\ApprovalController',

            'VigattinAds\Controller\Index' => 'VigattinAds\Controller\IndexController',
            'VigattinAds\Controller\Debug' => 'VigattinAds\Controller\DebugController',
            'VigattinAds\Controller\AccountHome' => 'VigattinAds\Controller\AccountHomeController',
            'VigattinAds\Controller\Cli'   => 'VigattinAds\Controller\CliController',
            'VigattinAds\Controller\JsonService'   => 'VigattinAds\Controller\JsonServiceController',
            'VigattinAds\Controller\ShowAds'   => 'VigattinAds\Controller\ShowAdsController',
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'vigattinads/layout/default'    => __DIR__ . '/../view/vigattinads/layout/default.phtml',
            'vigattinads/layout/active'    => __DIR__ . '/../view/vigattinads/layout/active.phtml',
            'vigattinads/view/index' => __DIR__ . '/../view/vigattinads/view/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            'vigattinads_domain_model' => array(
                'class' =>'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__ . '/../src/VigattinAds/DomainModel',
                ),
            ),
            'orm_default' => array(
                'drivers' => array(
                    'VigattinAds\DomainModel' => 'vigattinads_domain_model',
                ),
            ),
        ),
    ),
    'console' => array(
        'router' => array(
            'routes' => array(
                'vigattinads-menu' => array(
                    'options' => array(
                        'route'    => 'vigattinads',
                        'defaults' => array(
                            'controller' => 'VigattinAds\Controller\Cli',
                            'action'     => 'index',
                        )
                    )
                ),
                'vigattinads-create-user' => array(
                    'options' => array(
                        'route'    => 'vigattinads newuser [--email=] [--username=] [--password=] [--first-name=] [--last-name=]',
                        'defaults' => array(
                            'controller' => 'VigattinAds\Controller\Cli',
                            'action'     => 'newuser',
                        )
                    )
                ),
                'vigattinads-change-password' => array(
                    'options' => array(
                        'route'    => 'vigattinads changepass [--email=] [--username=] [--password=]',
                        'defaults' => array(
                            'controller' => 'VigattinAds\Controller\Cli',
                            'action'     => 'changepass',
                        )
                    )
                ),
            )
        )
    ),
);
