<?php
return array(
    'router' => array(
        'routes' => array(
            'vigattinads' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/vigattinads[/:controller][/:action][/:param1][/:param2]',
                    'constraints' => array(
                        'controller'    => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'param1'        => '[a-zA-Z0-9_-]*',
                        'param2'        => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'VigattinAds\Controller',
                        'controller'    => 'Dashboard',
                        'action'        => 'index',
                    ),
                ),
            ),
            'vigattinads_showads' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/vigattinads/showads[/:action]',
                    'constraints' => array(
                        'action'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller'    => 'VigattinAds\Controller\ShowAds',
                        'action'        => 'index',
                    ),
                ),
            ),
            'vigattinads_login' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/vigattinads/login[/]',
                    'defaults' => array(
                        'controller'    => 'VigattinAds\Controller\Login',
                        'action'        => 'index',
                    ),
                ),
            ),
            'vigattinads_logout' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/vigattinads/logout[/]',
                    'defaults' => array(
                        'controller'    => 'VigattinAds\Controller\Logout',
                        'action'        => 'index',
                    ),
                ),
            ),
            'vigattinads_dashboard' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/vigattinads/dashboard[/:param]',
                    'constraints' => array(
                        'param'    => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller'    => 'VigattinAds\Controller\Dashboard',
                        'action'        => 'index',
                        'param'         => 'vigattintrade',
                    ),
                ),
            ),
            'vigattinads_dashboard_ads_create' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/vigattinads/dashboard/ads/create[/:controller][/:action][/:param]',
                    'constraints' => array(
                        'controller'    => '[a-zA-Z0-9_-]*',
                        'action'        => '[a-zA-Z0-9_-]*',
                        'param'         => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'VigattinAds\Controller\Dashboard\Ads\Create',
                        'controller'    => 'Create',
                        'action'        => 'index',
                        'param'         => '',
                    ),
                ),
            ),
            'vigattinads_dashboard_ads' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/vigattinads/dashboard/ads[/][:page]',
                    'constraints' => array(
                        'controller'    => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page'          => '[0-9]',
                    ),
                    'defaults' => array(
                        'controller'    => 'VigattinAds\Controller\Dashboard\Ads',
                        'action'        => 'index',
                        'page'          => 1,
                    ),
                ),
            ),
            'vigattinads_dashboard_ads_edit' => array(
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
            'vigattinads_dashboard_ads_template' => array(
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
            'vigattinads_dashboard_ads_info' => array(
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
            'vigattinads_dashboard_ads_import' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/vigattinads/dashboard/ads/import[/][:name][/][:start]',
                    'constraints' => array(
                        'name'    => '[a-zA-Z0-9_-]*',
                        'start'    => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'VigattinAds\Controller\Dashboard\Ads',
                        'controller'    => 'Import',
                        'action'        => 'index',
                    ),
                ),
            ),
            'vigattinads_dashboard_profile' => array(
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
            'vigattinads_dashboard_admin' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/vigattinads/dashboard/admin[/]',
                    'defaults' => array(
                        'controller'    => 'VigattinAds\Controller\Dashboard\Admin',
                        'action'        => 'index',
                    ),
                ),
            ),
            'vigattinads_dashboard_admin_manageaccount' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/vigattinads/dashboard/admin/manageaccount[/:page][/:param1][/:param2]',
                    'constraints' => array(
                        'param1'        => '[a-zA-Z0-9_-]*',
                        'param2'        => '[a-zA-Z0-9_-]*',
                        'page'          => '[0-9]'
                    ),
                    'defaults' => array(
                        'controller'    => 'VigattinAds\Controller\Dashboard\Admin\ManageAccount',
                        'action'        => 'index',
                    ),
                ),
            ),
            'vigattinads_dashboard_admin_manageads' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/vigattinads/dashboard/admin/manageads[/:page][/:param1][/:param2]',
                    'constraints' => array(
                        'param1'        => '[a-zA-Z0-9_-]*',
                        'param2'        => '[a-zA-Z0-9_-]*',
                        'page'          => '[0-9]'
                    ),
                    'defaults' => array(
                        'controller'    => 'VigattinAds\Controller\Dashboard\Admin\ManageAds',
                        'action'        => 'index',
                    ),
                ),
            ),
            'vigattinads_dashboard_approval' => array(
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
            'vigattinads_pageblock' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/vigattinads/pageblock[/:controller]',
                    'constraints' => array(
                        'controller'    => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'VigattinAds\Controller\PageBlock',
                        'controller'    => 'BlockNoGold',
                        'action'        => 'index',
                    ),
                ),
            ),
            'vigattinads_comm' => array(
                'type'    => 'literal',
                'options' => array(
                    'route'    => '/vigattinads/comm',
                    'defaults' => array(
                        'controller'    => 'VigattinAds\Controller\Comm',
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
            'VigattinAds\Controller\Dashboard\Ads\Create\Create'   => 'VigattinAds\Controller\Dashboard\Ads\Create\CreateController',
            'VigattinAds\Controller\Dashboard\Ads\Create\ChooseWebsite'   => 'VigattinAds\Controller\Dashboard\Ads\Create\ChooseWebsiteController',
            'VigattinAds\Controller\Dashboard\Ads\Create\ChooseVigDirectory'   => 'VigattinAds\Controller\Dashboard\Ads\Create\ChooseVigDirectoryController',
            'VigattinAds\Controller\Dashboard\Ads\Create\ChooseDirectory'   => 'VigattinAds\Controller\Dashboard\Ads\Create\ChooseDirectoryController',
            'VigattinAds\Controller\Dashboard\Ads\Create\ChooseCategory'   => 'VigattinAds\Controller\Dashboard\Ads\Create\ChooseCategoryController',
            'VigattinAds\Controller\Dashboard\Ads\Edit'   => 'VigattinAds\Controller\Dashboard\Ads\AdsEditController',
            'VigattinAds\Controller\Dashboard\Ads\Template'   => 'VigattinAds\Controller\Dashboard\Ads\AdsWizardChooseTemplateController',
            'VigattinAds\Controller\Dashboard\Ads\Info'   => 'VigattinAds\Controller\Dashboard\Ads\AdsWizardEditInfoController',
            'VigattinAds\Controller\Dashboard\Ads\Import'   => 'VigattinAds\Controller\Dashboard\Ads\AdsImportController',
            'VigattinAds\Controller\Dashboard\Profile'   => 'VigattinAds\Controller\Dashboard\Profile\ProfileController',
            'VigattinAds\Controller\Dashboard\Admin'   => 'VigattinAds\Controller\Dashboard\Admin\AdminController',
            'VigattinAds\Controller\Dashboard\Admin\ManageAccount'   => 'VigattinAds\Controller\Dashboard\Admin\AdminManageAccountController',
            'VigattinAds\Controller\Dashboard\Admin\ManageAds'   => 'VigattinAds\Controller\Dashboard\Admin\AdminManageAdsController',
            'VigattinAds\Controller\Dashboard\Approval'   => 'VigattinAds\Controller\Dashboard\Approval\ApprovalController',
            'VigattinAds\Controller\Debug' => 'VigattinAds\Controller\DebugController',
            'VigattinAds\Controller\Cli'   => 'VigattinAds\Controller\CliController',
            'VigattinAds\Controller\JsonService'   => 'VigattinAds\Controller\JsonServiceController',
            'VigattinAds\Controller\ShowAds'   => 'VigattinAds\Controller\ShowAdsController',
            'VigattinAds\Controller\PageBlock\BlockNoGold'   => 'VigattinAds\Controller\PageBlock\BlockNoGoldController',
            'VigattinAds\Controller\Comm'   => 'VigattinAds\Controller\CommController',
        ),
    ),
    'view_manager' => array(
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'doctype'                  => 'HTML5',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/vigattinads/layout/default.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
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
                'vigattinads-update-searchads' => array(
                    'options' => array(
                        'route'    => 'vigattinads updatesearchads [--force=]',
                        'defaults' => array(
                            'controller' => 'VigattinAds\Controller\Cli',
                            'action'     => 'updatesearchads',
                        )
                    )
                ),
            )
        )
    ),
    'service_manager' => array(
        'factories' => array(
            'Vigattin\Vauth\Vauth' => 'VigattinAds\DomainModel\VauthServiceFactory',
        ),
    ),
);
