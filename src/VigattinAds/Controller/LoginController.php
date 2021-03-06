<?php
namespace VigattinAds\Controller;

use Zend\Http\Headers;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;

class LoginController extends AbstractActionController
{
    /** @var \VigattinAds\DomainModel\UserManager */
    protected $userManager;

    /** @var \Vigattin\Vauth\Vauth */
    protected $vauth;

    /** @var null|array */
    protected $createAccountError = null;

    public function indexAction()
    {
        // Redirect to vigattin login remove this code to go back to local login
        if($this->createAccountError === null) {
            header('Location: http://www.vigattin.com/signin/activating?redirect=http%3A%2F%2Fwww.service.vigattin.com%2Fvigattinads');
            exit();
        }
        else {
            $viewModel = new ViewModel();
            $viewModel->setVariable('errors', $this->createAccountError);
            $viewModel->setTemplate('vigattinads/view/error');
            return $viewModel;
        }

        $error = '';
        /** @var $post \Zend\Stdlib\Parameters */
        $post = $this->getRequest()->getPost();
        if($post->get('login') == 'true')
        {
            $result = $this->userManager->login($post->get('email'), $post->get('password'));
            if($result['status'] == 'success')
            {
                header('Location: /vigattinads/dashboard');
                exit();
            }
            $error = $result['reason'];
        }
        $viewModel = new ViewModel();
        $viewModel->setVariable('error', $error);
        $viewModel->setTemplate('vigattinads/view/login');
        return $viewModel;
    }

    public function onDispatch(MvcEvent $e) {
        $this->userManager = $this->serviceLocator->get('VigattinAds\DomainModel\UserManager');
        $this->layout()->setTemplate('vigattinads/layout/default');
        $this->vauth = $this->serviceLocator->get('Vigattin\Vauth\Vauth');
        $this->ProcessVauthLogin();
        return parent::onDispatch($e);
    }

    public function ProcessVauthLogin()
    {
        if($this->vauth->is_login()) {
            $vauthLocator = new \VigattinAds\DomainModel\VauthAccountLocator();
            $vauthLocator->set('serviceManager', $this->serviceLocator);
            $adsUserId = $vauthLocator->hasAccount($this->vauth->get_vigid());
            if($adsUserId) {
                $result = $this->userManager->login($adsUserId, '', true);
                if($result['status'] == 'success')
                {
                    header('Location: /vigattinads/dashboard');
                    exit();
                }
            }
            else {
                $user = $this->userManager->createUser(
                    $this->vauth->get_email(),
                    $this->vauth->get_username() ? trim($this->vauth->get_username()) : uniqid(),
                    uniqid(),
                    $this->vauth->get_first_name() ? trim($this->vauth->get_first_name()) : 'no first name',
                    $this->vauth->get_last_name() ? trim($this->vauth->get_last_name()) : 'no last name'
                );
                if($user instanceof \VigattinAds\DomainModel\AdsUser) {
                    $vauthLocator->addAccount($this->vauth->get_vigid(), $user->get('id'));
                    $result = $this->userManager->login($user->get('id'), '', true);
                    if($result['status'] == 'success')
                    {
                        header('Location: /vigattinads/dashboard');
                        exit();
                    }
                }
                else {
                    $this->createAccountError = $user;
                }
            }
        }
    }
}
