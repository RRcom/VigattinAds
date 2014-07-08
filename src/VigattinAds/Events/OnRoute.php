<?php
namespace VigattinAds\Events;

use Zend\Mvc\MvcEvent;

class OnRoute
{
    /** @var \Zend\Mvc\MvcEvent */
    protected $event;

    public function __construct(MvcEvent $event = null)
    {
        if($event instanceof MvcEvent) $this->event = $event;
        else $this->event = new MvcEvent();
        $this->doEvent();
    }

    public function doEvent()
    {
        $this->doReRoute();
    }

    protected function doReRoute() {
        /* skip if call from command line */
        if($this->event->getRequest() instanceof \Zend\Console\Request) {
            return;
        }

        if(!preg_match('/^vigattinads*/', $this->event->getRouteMatch()->getMatchedRouteName())) return;
        switch(strtolower($this->event->getRouteMatch()->getParam('controller'))) {
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
                $userManager = $this->event->getApplication()->getServiceManager()->get('VigattinAds\DomainModel\UserManager');
                if($userManager->isLogin()) {
                    Header('Location: /vigattinads');
                    exit();
                }
                break;

            // if enter any controller
            default:
                $userManager = $this->event->getApplication()->getServiceManager()->get('VigattinAds\DomainModel\UserManager');
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
    }
}