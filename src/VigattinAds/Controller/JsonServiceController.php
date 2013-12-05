<?php
namespace VigattinAds\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use VigattinAds\Model\Helper;

class JsonServiceController extends AbstractActionController
{
    /**
     * @var \Zend\View\Model\JsonModel
     */
    protected $jsonView;

    /**
     * @var array;
     */
    protected $request;

    public function __construct()
    {
        $this->jsonView = new JsonModel();
    }

    public function indexAction()
    {
        $this->jsonView->setVariable('message', 'Welcome');
        return $this->jsonView;
    }

    public function verifyAction()
    {
        $action =  isset($this->request['action']) ? $this->request['action'] : '';
        $data = isset($this->request['data']) ? $this->request['data'] : '';
        switch($action)
        {
            case 'ads_title':
                $error = Helper\Validator::isTitleValid($data);
                break;
            case 'ads_url':
                $error = Helper\Validator::isUrlValid($data);
                break;
            case 'ads_description':
                $error = Helper\Validator::isDescriptionValid($data);
                break;
            case 'ads_keywords':
                $error = Helper\Validator::isKeywordValid($data);
                break;
        }
        $result = array(
            'status' => $error ? 'failed' : 'success',
            'reason' => $error,
        );
        $this->jsonView->setVariables($result);
        return $this->jsonView;
    }

    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        if(strtolower($this->params('param1')) == 'post') $this->request = $this->getRequest()->getPost();
        else $this->request = $this->getRequest()->getQuery();
        return parent::onDispatch($e);
    }
}
