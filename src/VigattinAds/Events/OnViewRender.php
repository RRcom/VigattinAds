<?php
namespace VigattinAds\Events;

use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;
use VigattinAds\DomainModel\VauthAccountLocator;

class OnViewRender
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
        if(get_class($this->event->getViewModel()) == 'Zend\View\Model\ViewModel') {
            $userManager = $this->event->getApplication()->getServiceManager()->get('VigattinAds\DomainModel\UserManager');

            // set config var
            $this->event->getViewModel()->setVariable('config', $this->event->getApplication()->getServiceManager()->get('config'));

            //set vauth ID var
            if($userManager->isLogin()) {
                $cache = $this->event->getApplication()->getServiceManager()->get('VigattinAds\DomainModel\LongCache');
                $user = $userManager->getCurrentUser();
                $cacheKey = md5('vauthId_'.$user->get('id'));
                $vauthId = $cache->getItem($cacheKey);
                if($vauthId) {
                    $this->event->getViewModel()->setVariable('vauthId', $vauthId);
                }
                else {
                    $vauthAccountLocator = $userManager->getVauthAccountLocator($user->get('id'));
                    if($vauthAccountLocator instanceof VauthAccountLocator) {
                        $vauthId = $vauthAccountLocator->get('vauthId');
                    }
                    else $vauthId = 0;

                    $cache->addItem($cacheKey, $vauthId);
                    $this->event->getViewModel()->setVariable('vauthId', $vauthId);
                }
            }
        }
    }
}