<?php
namespace VigattinAds\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Vigattin\Communicate\Communicate;
use VigattinAds\Events\Comm\OnUpdateFreeGold;


class CommController extends AbstractActionController
{
    protected $communicate;

    public function __construct()
    {
        $this->communicate = new Communicate();
        $this->communicate->registerOnCatchListener(new OnUpdateFreeGold(), 'update_free_gold');
    }

    public function indexAction()
    {
        $jasonModel = new JsonModel();
        $package = $this->communicate->catchMessage();
        $jasonModel->setVariable('package', $package);
        return $jasonModel;
    }
}
