<?php
namespace VigattinAds\Controller;

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
        $sm = $this->getServiceLocator();
        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $sm->get('Doctrine\ORM\EntityManager');

        /** @var $userManager \VigattinAds\DomainModel\UserManager */
        $userManager = $sm->get('VigattinAds\DomainModel\UserManager');

        /** @var $user \VigattinAds\DomainModel\AdsUser; */
        $user = $userManager->getCurrentUser();

        $adsManager = new AdsManager($sm);

        $ads = $adsManager->fetchAdsToReview($user);

        if($ads instanceof Ads)
        {
            echo $ads->get('adsTitle');
        }


        $viewModel = new ViewModel();
        $viewModel->setTemplate('vigattinads/view/index');
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
}
