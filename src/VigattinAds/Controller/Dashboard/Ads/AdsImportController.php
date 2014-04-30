<?php
namespace VigattinAds\Controller\Dashboard\Ads;

use Zend\View\Model\JsonModel;
use VigattinAds\DomainModel\AdsImporter;

class AdsImportController extends AdsController
{
    public function indexAction()
    {
        $adsImporter = new AdsImporter();
        $name = $this->params('name', '');
        $start = $this->params('start', 0);
        $limit = 10;
        $data = $adsImporter->importAds($name, $start, $limit, $this->adsUser->getVauthId());

        $total = !empty($data['total']) ? $data['total'] : 0;
        $list = (!empty($data['list']) && is_array($data['list'])) ? $data['list'] : array();
        $next = $start + $limit;

        $jsonModel = new JsonModel();
        $jsonModel->setVariable('list', $list);
        $jsonModel->setVariable('total', $total);
        $jsonModel->setVariable('limit', $limit);
        $jsonModel->setVariable('next', $next);
        return $jsonModel;
    }
}
