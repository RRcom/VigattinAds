<?php
namespace VigattinAds\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Vigattin\Communicate\Communicate;
use Zend\Mvc\MvcEvent;


class CommController extends AbstractActionController
{
    protected $communicate;

    protected $userManager;

    public function indexAction()
    {
        $jasonModel = new JsonModel();
        $package = $this->communicate->catchMessage();
        $jasonModel->setVariable('package', $package);
        return $jasonModel;
    }

    public function onDispatch(MvcEvent $e)
    {
        $this->userManager = $this->serviceLocator->get('VigattinAds\DomainModel\UserManager');
        $this->communicate = new Communicate();
        $events = array(
            array('class' => 'VigattinAds\Events\Comm\OnUpdateFreeGold', 'name' =>'update_free_gold', 'dependencies' => array('userManager' => $this->userManager, 'serviceManager' => $this->serviceLocator)),
        );
        foreach($events as $event) {
            $this->communicate->registerOnCatchListener($event['class'], $event['name'], $event['dependencies']);
        }
        return parent::onDispatch($e);
    }
}
