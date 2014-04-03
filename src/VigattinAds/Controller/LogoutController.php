<?php
namespace VigattinAds\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class LogoutController extends AbstractActionController
{
    public function indexAction()
    {
        /** @var \VigattinAds\DomainModel\UserManager $userManager */
        $userManager = $this->serviceLocator->get('VigattinAds\DomainModel\UserManager');
        $userManager->logout();
        header('Location: http://www.vigattin.com/signin/deactivating?redirect=http%3A%2F%2Fwww.service.vigattin.com%2Fvigattinads');
        exit();
    }
}
