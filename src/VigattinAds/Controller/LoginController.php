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

    public function indexAction()
    {
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
        return parent::onDispatch($e);
    }
}
