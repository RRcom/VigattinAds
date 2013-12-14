<?php
namespace VigattinAds\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;
use VigattinAds\DomainModel\Ads;


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

        $ads = $user->get('ads');

        echo $ads->count();

        $viewModel = new ViewModel();
        $viewModel->setTemplate('vigattinads/view/index');
        return $viewModel;
    }

    public function createAction()
    {
        $sm = $this->getServiceLocator();
        $em = $sm->get('Doctrine\ORM\EntityManager');
        $ads = new Ads();
        $ads->setAdsName('testname');
        $ads->setAdsDescription('this is a sample adds description');
        $ads->setAdsUrl('http://testads.ads');
        $em->persist($ads);
        $em->flush();
        return 'success';
    }

    public function addviewAction()
    {
        $sm = $this->getServiceLocator();
        $em = $sm->get('Doctrine\ORM\EntityManager');

        /*
        $ads = $em->find('VigattinAds\Entity\Ads', 1);
        $adsView = new AdsView();
        $adsView->setViewTime(time());
        $ads->addAdsView($adsView);

        $em->persist($ads);
        $em->flush();
        */


        $ads = new Ads();
        $ads->setAdsDescription('another ads');
        $ads->setAdsName('ads 2');
        $ads->setAdsUrl('http://anotherads.com');
        $adsView1 = new AdsView();
        $adsView1->setViewTime(time());
        $adsView1->setClicked(false);
        $adsView1->setAdsReferrer('http://6778.003');
        $ads->addAdsView($adsView1);
        $em->persist($adsView1);
        $adsView2 = new AdsView();
        $adsView2->setViewTime(time());
        $adsView2->setClicked(false);
        $adsView2->setAdsReferrer('http://6778.004');
        $ads->addAdsView($adsView2);
        $em->persist($adsView2);
        $em->persist($ads);
        $em->flush();

        return 'success';
    }

    public function viewstatusAction()
    {
        $sm = $this->getServiceLocator();
        $em = $sm->get('Doctrine\ORM\EntityManager');
        $viewModel = new ViewModel();
        $viewModel->setTemplate('vigattinads/view/index');

        //$query = $em->createQuery("SELECT a, v FROM VigattinAds\\Entity\\Ads a JOIN a.adsView v WHERE a.id = :id");
        $query = $em->createQuery("SELECT v, a FROM VigattinAds\\Entity\\AdsView v JOIN v.ads a WHERE v.ads = :id");
        $query->setParameter('id', 1);
        $viewModel->setVariable('debug', $query->getResult($query::HYDRATE_ARRAY));

        return $viewModel;
    }

    public function testsessionAction()
    {
        $sm = $this->getServiceLocator();
        $sessMan = $sm->get('Zend\Session\SessionManager');
        $sessMan = new SessionManager();
        $storage = $sessMan->getStorage();
        //$storage->setMetadata()
        $viewModel = new ViewModel();
        $viewModel->setTemplate('vigattinads/view/index');
        //$_SESSION['test'] = 12345;
        //$storage->test2 ='hello';
        //$storage->setMetadata('test2', array(1234, 'dfdf'));
        $viewModel->setVariable('debug', $storage->test2);
        return $viewModel;
    }

    public function testmodelAction()
    {
        $data = array();
        $viewModel = new ViewModel();
        $viewModel->setTemplate('vigattinads/view/index');

        /* @var $user \VigattinAds\Model\User\User */
        $user = $this->serviceLocator->get('VigattinAds\Model\User\User');
        //$user = new User();
        //$data['login'] = $user->login('rrcom', '12345678');
        //$data['tableExist'] = $user->isTableExist('email', 'resty_rizal@live.com');
        //$data['createUser'] = $user->createUser('resty_rizal@gmail.com', 'rrcom', '12345678', 'Resty', 'Rizal');
        //$data['getuser'] = $user->getUser();
        //$userEntity = $user->getUser();
        //$userEntity->setUsername('Mikmik');
        //$user->updateUser($userEntity);
        //$data['islogin'] = $user->isLogin() ? 'true' : 'false';
        //$data['name'] = $user->getUser()->getFirstName().' '.$user->getUser()->getLastName();
        //$data['username'] = $user->getUser()->getUsername();
        //$data['isVerified'] = $user->getUser()->getVerified() ? 'true' : 'false';
        //$user = $this->serviceLocator->get('VigattinAds\Model\User\User');
        $data['islogin'] = 'false';
        if($user->isLogin())
        {
            $data['islogin'] = 'true';

            //$user->getAds()->createAds('vigattin main ads', 'http://vigattin.com', 'ads about photo');
            $adsList = $user->getAds()->listAds(0, 30, AdsModel::ORDER_BY_ASC);
            $user->getAds()->deleteAds(array($adsList[2], $adsList[3], $adsList[4]));

            $adsArray = array();
            foreach($user->getAds()->listAds(0, 30, AdsModel::ORDER_BY_ASC) as $list)
            {
                $adsArray[] = $list->getAdsName();
            }
            $data['ads'] = $adsArray;

        }


        $viewModel->setVariable('debug', $data);
        return $viewModel;
    }

    public function onDispatch(MvcEvent $e) {
        $this->layout()->setTemplate('vigattinads/layout/default');
        return parent::onDispatch($e);
    }
}
