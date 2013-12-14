<?php
namespace VigattinAds\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use VigattinAds\DomainModel\UserManager;

class CliController extends AbstractActionController
{
    public function indexAction()
    {
        $error = '';
        $request = $this->getRequest();
        $userManager = new UserManager($this->serviceLocator);
        if(!$request->getParam('email')) $error .= "require email ex. --email myemail@mail.sample\n";
        if(!$request->getParam('username')) $error .= "require username ex. --username myusername\n";
        if(!$request->getParam('password')) $error .= "require password ex. --password mypassword\n";
        if(!$request->getParam('first-name')) $error .= "require first-name ex. --first-name myfirstname\n";
        if(!$request->getParam('last-name')) $error .= "require last-name ex. --last-name mylastname\n";
        if($error) return $error;
        $result = $userManager->createUser(
            $request->getParam('email'),
            $request->getParam('username'),
            $request->getParam('password'),
            $request->getParam('first-name'),
            $request->getParam('last-name')
        );
        if(is_array($result)) {
            foreach($result as $err)
            {
                $error .= $err."\n";
            }
        }
        else {
            $userManager->flush();
            return "success user id ".$result->getId();
        }
        return $error;
    }
}
