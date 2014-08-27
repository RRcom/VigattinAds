<?php
namespace VigattinAds\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use VigattinAds\DomainModel\UserManager;
use Zend\Mvc\MvcEvent;
use VigattinAds\DomainModel\Assets\AssetsGenerator;

/*
 * Command line system tools
 * Example: php index.php vigattinads
 * Example ads user: php index.php vigattinads newuser --email test@mail.com --username testuser --password 123456 --first-name myfirstname --last-name mylastname
 */

class CliController extends AbstractActionController
{
    /**
     * @var \Zend\ServiceManager\ServiceLocatorInterface
     */
    protected $serviceManager;

    /**
     * @var \VigattinAds\DomainModel\UserManager
     */
    protected $userManager;

    /**
     * @var \Zend\Stdlib\RequestInterface
     */
    protected $request;

    public function onDispatch(MvcEvent $e)
    {
        $this->serviceManager = $e->getApplication()->getServiceManager();
        $this->userManager = $this->serviceManager->get('VigattinAds\DomainModel\UserManager');
        $this->request = $this->getRequest();
        return parent::onDispatch($e);
    }

    public function indexAction()
    {
        return "vigattinads newuser\nvigattinads changepass\nupdatesearchads\nassets\n";
    }

    public function newuserAction()
    {
        $error = '';
        if(!$this->request->getParam('email')) $error .= "require email ex. --email myemail@mail.sample\n";
        if(!$this->request->getParam('username')) $error .= "require username ex. --username myusername\n";
        if(!$this->request->getParam('password')) $error .= "require password ex. --password mypassword\n";
        if(!$this->request->getParam('first-name')) $error .= "require first-name ex. --first-name myfirstname\n";
        if(!$this->request->getParam('last-name')) $error .= "require last-name ex. --last-name mylastname\n";
        if($error) return $error;
        $result = $this->userManager->createUser(
            $this->request->getParam('email'),
            $this->request->getParam('username'),
            $this->request->getParam('password'),
            $this->request->getParam('first-name'),
            $this->request->getParam('last-name')
        );
        if(is_array($result)) {
            foreach($result as $err)
            {
                $error .= $err."\n";
            }
        }
        else {
            return "success user id ".$result->get('id');
        }
        return $error;
    }

    public function changepassAction()
    {
        $email = $this->request->getParam('email', '');
        $username = $this->request->getParam('username', '');
        $password = $this->request->getParam('password', '');
        $msg = '';
        if((!$email && !$username) || !$password) return "require email or username\nex --email myemail@mail.sample or --username myusername\nrequire password ex. --password mypassword\n";
        $user = $email ? $email : $username;
        return $this->userManager->changePassword($user, $password);
    }

    public function updatesearchadsAction()
    {
        $batchStart = 0;
        $batchLimit = 30;

        $force = $this->request->getParam('force', 'false');
        if(strtolower($force) != 'true') {
            return "Tools to update all ads user info for search optimize\nex. vigattinads updatesearchads --force true\n";
        }
        echo "Updating ".$this->userManager->countUserList()." user(s) started\n";
        $updated = 0;
        while(count($users = $this->userManager->getUserList(UserManager::SORT_BY_ID, UserManager::SORT_DIRECTION_ASC, $batchStart, $batchLimit))) {
            foreach($users as $user) {
                $user->updateAdsSearch();
                $user->persistSelf();
                $batchStart++;
            }
            $this->userManager->flush();
        }
        echo "$batchStart user(s) successfully updated\n";
    }

    public function assetsAction()
    {
        $assetsGenerator = new AssetsGenerator();
        $assetsGenerator->generate();
    }

}
