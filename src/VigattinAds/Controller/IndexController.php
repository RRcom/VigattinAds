<?php
namespace VigattinAds\Controller;

use Zend\Http\Headers;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        return $this->loginPage();
    }

    protected function loginPage()
    {
        $error = '';
        /** @var $post \Zend\Stdlib\Parameters */
        $post = $this->getRequest()->getPost();
        if($post->get('login') == 'true')
        {
            /** @var $user \VigattinAds\Model\User\User */
            $userManager = $this->serviceLocator->get('VigattinAds\DomainModel\UserManager');
            $result = $userManager->login($post->get('email'), $post->get('password'));
            if($result['status'] == 'success')
            {
                header('Location: /vigattinads/account-home');
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
        $this->layout()->setTemplate('vigattinads/layout/default');
        return parent::onDispatch($e);
    }
}
