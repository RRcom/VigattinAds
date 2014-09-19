<?php
return array(
    'vigattinads' => array(
        'assets_version' => 27,
    ),
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
            'vigattinads_sampleads' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/vigattinads/sample-ads[/]',
                    'defaults' => array(
                        'controller'    => 'VigattinAds\Controller\SampleAds\SampleAds',
                        'action'        => 'index',
                    ),
                ),
            ),
            'vigattinads_showads2' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/vigattinads/showads2[/:controller][/:action]',
                    'constraints' => array(
                        'controller'    => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action'    => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'VigattinAds\Controller\ShowAds',
                        'controller'    => 'ShowAds',
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
                        'controller'    => 'VigattinAds\Controller\ShowAds\ShowAds',
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
                    'route'    => '/vigattinads/dashboard/ads/create[/]',
                    'defaults' => array(
                        'controller'    => 'VigattinAds\Controller\Dashboard\Ads\Create\Create',
                        'action'        => 'index',
                    ),
                ),
            ),
            'vigattinads_dashboard_ads_create_choose_website' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/vigattinads/dashboard/ads/create/choose-website[/:controller][/:action][/:param]',
                    'constraints' => array(
                        'controller'    => '[a-zA-Z0-9_-]*',
                        'action'        => '[a-zA-Z0-9_-]*',
                        'param'         => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'VigattinAds\Controller\Dashboard\Ads\Create\ChooseWebsite',
                        'controller'    => 'ChooseWebsite',
                        'action'        => 'index',
                        'param'         => '',
                    ),
                ),
            ),
            'vigattinads_dashboard_ads_create_choose_website_import' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/vigattinads/dashboard/ads/create/choose-website/import[/:param]',
                    'constraints' => array(
                        'param'         => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller'    => 'VigattinAds\Controller\Dashboard\Ads\Create\ChooseWebsite\Import',
                        'action'        => 'index',
                    ),
                ),
            ),
            'vigattinads_dashboard_ads_create_choose_website_blogger_import' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/vigattinads/dashboard/ads/create/choose-website/blogger-author/import[/:param]',
                    'constraints' => array(
                        'param'         => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller'    => 'VigattinAds\Controller\Dashboard\Ads\Create\ChooseWebsite\Import',
                        'action'        => 'index',
                    ),
                ),
            ),
            'vigattinads_dashboard_ads_create_choose_website_import_edit' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/vigattinads/dashboard/ads/create/choose-website/import/edit[/]',
                    'defaults' => array(
                        'controller'    => 'VigattinAds\Controller\Dashboard\Ads\Create\ChooseWebsite\Import\CreateDraft',
                        'action'        => 'index',
                    ),
                ),
            ),
            'vigattinads_dashboard_ads_create_choose_website_blogger_import_edit' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/vigattinads/dashboard/ads/create/choose-website/blogger-author/import/edit[/]',
                    'defaults' => array(
                        'controller'    => 'VigattinAds\Controller\Dashboard\Ads\Create\ChooseWebsite\Import\CreateDraft',
                        'action'        => 'index',
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
                        'page'          => '[0-9]*',
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
                    'route'    => '/vigattinads/dashboard/ads/edit[/:controller][/:param1][/:param2]',
                    'constraints' => array(
                        'controller'    => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'param1'        => '[a-zA-Z0-9_-]*',
                        'param2'        => '[a-zA-Z0-9_.-]*',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'VigattinAds\Controller\Dashboard\Ads\Edit',
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
                        'page'          => '[0-9]*'
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
                        'page'          => '[0-9]*'
                    ),
                    'defaults' => array(
                        'controller'    => 'VigattinAds\Controller\Dashboard\Admin\ManageAds',
                        'action'        => 'index',
                    ),
                ),
            ),
            'vigattinads_dashboard_admin_historylog' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/vigattinads/dashboard/admin/historylog[/:page][/:param1][/:param2]',
                    'constraints' => array(
                        'param1'        => '[a-zA-Z0-9_-]*',
                        'param2'        => '[a-zA-Z0-9_-]*',
                        'page'          => '[0-9]*'
                    ),
                    'defaults' => array(
                        'controller'    => 'VigattinAds\Controller\Dashboard\Admin\HistoryLog',
                        'action'        => 'index',
                    ),
                ),
            ),
            'vigattinads_dashboard_approval' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/vigattinads/dashboard/approval[/:controller][/:action][/:page]',
                    'constraints' => array(
                        'controller'    => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page'          => '[0-9]*',
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
            'VigattinAds\Controller\SampleAds\SampleAds'   => 'VigattinAds\Controller\SampleAds\SampleAdsController',
            'VigattinAds\Controller\Login'   => 'VigattinAds\Controller\LoginController',
            'VigattinAds\Controller\Logout'   => 'VigattinAds\Controller\LogoutController',
            'VigattinAds\Controller\Dashboard' => 'VigattinAds\Controller\Dashboard\DashboardController',
            'VigattinAds\Controller\Dashboard\Ads'   => 'VigattinAds\Controller\Dashboard\Ads\AdsController',
            'VigattinAds\Controller\Dashboard\Ads\Create\Create'   => 'VigattinAds\Controller\Dashboard\Ads\Create\CreateController',
            'VigattinAds\Controller\Dashboard\Ads\Create\ChooseWebsite\ChooseWebsite'   => 'VigattinAds\Controller\Dashboard\Ads\Create\ChooseWebsite\ChooseWebsiteController',
            'VigattinAds\Controller\Dashboard\Ads\Create\ChooseWebsite\ChooseVigDirectory'   => 'VigattinAds\Controller\Dashboard\Ads\Create\ChooseWebsite\ChooseVigDirectoryController',
            'VigattinAds\Controller\Dashboard\Ads\Create\ChooseWebsite\ChooseDirectory'   => 'VigattinAds\Controller\Dashboard\Ads\Create\ChooseWebsite\ChooseDirectoryController',
            'VigattinAds\Controller\Dashboard\Ads\Create\ChooseWebsite\ChooseCategory'   => 'VigattinAds\Controller\Dashboard\Ads\Create\ChooseWebsite\ChooseCategoryController',
            'VigattinAds\Controller\Dashboard\Ads\Create\ChooseWebsite\BloggerAuthor'   => 'VigattinAds\Controller\Dashboard\Ads\Create\ChooseWebsite\ChooseTourismBloggerAuthorController',
            'VigattinAds\Controller\Dashboard\Ads\Create\ChooseWebsite\Import' => 'VigattinAds\Controller\Dashboard\Ads\Create\ChooseWebsite\Import\ImportController',
            'VigattinAds\Controller\Dashboard\Ads\Create\ChooseWebsite\Import\Edit' => 'VigattinAds\Controller\Dashboard\Ads\Create\ChooseWebsite\Import\Edit\EditController',
            'VigattinAds\Controller\Dashboard\Ads\Create\ChooseWebsite\Import\CreateDraft' => 'VigattinAds\Controller\Dashboard\Ads\Create\ChooseWebsite\Import\CreateDraft\CreateDraftController',
            'VigattinAds\Controller\Dashboard\Ads\Edit\Edit'   => 'VigattinAds\Controller\Dashboard\Ads\Edit\EditController',
            'VigattinAds\Controller\Dashboard\Ads\Edit\ChangeSite'   => 'VigattinAds\Controller\Dashboard\Ads\Edit\EditController',
            'VigattinAds\Controller\Dashboard\Ads\Template'   => 'VigattinAds\Controller\Dashboard\Ads\AdsWizardChooseTemplateController',
            'VigattinAds\Controller\Dashboard\Ads\Info'   => 'VigattinAds\Controller\Dashboard\Ads\AdsWizardEditInfoController',
            'VigattinAds\Controller\Dashboard\Ads\Import'   => 'VigattinAds\Controller\Dashboard\Ads\AdsImportController',
            'VigattinAds\Controller\Dashboard\Profile'   => 'VigattinAds\Controller\Dashboard\Profile\ProfileController',
            'VigattinAds\Controller\Dashboard\Admin'   => 'VigattinAds\Controller\Dashboard\Admin\AdminController',
            'VigattinAds\Controller\Dashboard\Admin\ManageAccount'   => 'VigattinAds\Controller\Dashboard\Admin\AdminManageAccountController',
            'VigattinAds\Controller\Dashboard\Admin\ManageAds'   => 'VigattinAds\Controller\Dashboard\Admin\AdminManageAdsController',
            'VigattinAds\Controller\Dashboard\Admin\HistoryLog'   => 'VigattinAds\Controller\Dashboard\Admin\AdminHistoryLogController',
            'VigattinAds\Controller\Dashboard\Approval'   => 'VigattinAds\Controller\Dashboard\Approval\ApprovalController',
            'VigattinAds\Controller\Debug' => 'VigattinAds\Controller\DebugController',
            'VigattinAds\Controller\Cli'   => 'VigattinAds\Controller\CliController',
            'VigattinAds\Controller\JsonService'   => 'VigattinAds\Controller\JsonServiceController',
            'VigattinAds\Controller\ShowAds\ShowAds'   => 'VigattinAds\Controller\ShowAds\ShowAdsController',
            'VigattinAds\Controller\ShowAds\TourismArticle'   => 'VigattinAds\Controller\ShowAds\TourismArticleController',
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
                'vigattinads-assets' => array(
                    'options' => array(
                        'route'    => 'vigattinads assets',
                        'defaults' => array(
                            'controller' => 'VigattinAds\Controller\Cli',
                            'action'     => 'assets',
                        )
                    )
                ),
            )
        )
    ),
    'service_manager' => array(
        'factories' => array(
            'Zend\Session\SessionManager' => 'VigattinAds\DomainModel\SessionManagerServiceFactory',
            'Vigattin\Vauth\Vauth' => 'VigattinAds\DomainModel\VauthServiceFactory',
            'VigattinAds\DomainModel\UserManager' => 'VigattinAds\DomainModel\UserManagerServiceFactory',
            'VigattinAds\DomainModel\AdsManager' => 'VigattinAds\DomainModel\AdsManagerServiceFactory',
            'VigattinAds\DomainModel\LongCache' => 'VigattinAds\DomainModel\LongCacheServiceFactory',
            'VigattinAds\DomainModel\ShortCache' => 'VigattinAds\DomainModel\ShortCacheServiceFactory',
            'VigattinAds\DomainModel\LogManager' => 'VigattinAds\DomainModel\LogManagerServiceFactory',
        ),
    ),
);
