<?php
namespace VigattinAds\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class LogoutController extends AbstractActionController
{
    public function indexAction()
    {
        /** @var $user \VigattinAds\Model\User\User */
        $user = $this->serviceLocator->get('VigattinAds\Model\User\User');
        $user->logout();
        header('Location: /vigattinads');
        exit();
    }
}
