<?php
namespace VigattinAds\Controller;

use VigattinAds\DomainModel\CommonLog;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Mvc\MvcEvent;
use VigattinAds\DomainModel\Ads;
use VigattinAds\DomainModel\SettingsManager;
use VigattinAds\DomainModel\AdsManager;


class DebugController extends AbstractActionController
{
    public function indexAction()
    {
        $viewModel = new ViewModel();
        $viewModel->setTemplate('vigattinads/view/debugView');
        $viewModel->setVariable('logArray', json_decode(file_get_contents('http://www.vigattin.com/api/member?id=1'), true));
        return $viewModel;
    }

    public function testVauthAction()
    {
        $vauthLocator = new \VigattinAds\DomainModel\VauthAccountLocator();
        $vauthLocator->set('serviceManager', $this->serviceLocator);
        echo "vauth test<pre>\n";
        /** @var \Vigattin\Vauth\Vauth $vauth */
        $vauth = $this->serviceLocator->get('Vigattin\Vauth\Vauth');

        print_r($vauth->get_user_full_info())."\n";
        //echo $vauthLocator->addAccount(123, 3456)."\n";
        echo $vauthLocator->hasAccount(123)."\n";

        return new JsonModel();
    }

    public function onDispatch(MvcEvent $e) {
        $this->layout()->setTemplate('vigattinads/layout/default');
        return parent::onDispatch($e);
    }

    public function cacheTestAction()
    {
        /** @var \Zend\Cache\Storage\Adapter\Filesystem $cache */
        $cache = $this->serviceLocator->get('VigattinAds\DomainModel\LongCache');

        $viewModel = new ViewModel();
        $viewModel->setTemplate('vigattinads/view/debugView');
        $viewModel->setVariable('logArray', array(
            'cache_size' => $cache->getAvailableSpace(),
            'cache_dir' => $cache->getOptions()->getCacheDir(),
            'cache_ttl' => $cache->getOptions()->getTtl(),
        ));
        return $viewModel;
    }

    public function testErrorAction()
    {
        runError();
    }
}
