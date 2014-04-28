<?php
namespace VigattinAds\Events;

use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

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
            $this->event->getViewModel()->setVariable('config', $this->event->getApplication()->getServiceManager()->get('config'));
        }
    }
}