<?php
namespace VigattinAds\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class LogoutController extends AbstractActionController
{
    public function indexAction()
    {
        /** @var $user \VigattinAds\Model\User\User */
        $userManager = $this->serviceLocator->get('VigattinAds\DomainModel\UserManager');
        $userManager->logout();
        header('Location: /vigattinads');
        exit();
    }
}
