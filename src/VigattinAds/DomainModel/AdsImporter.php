<?php
namespace VigattinAds\DomainModel;

use VigattinAds\DomainModel\Validator;

class AdsImporter
{
    const PAGE_VIGATTIN = 'http://www.vigattin.com';
    const PAGE_VIGATTINTRADE = 'http://www.vigattintrade.com';
    const PAGE_VIGATTINTOURISM = 'http://www.vigattintourism.com';
    const PAGE_VIGATTINDEALS = 'http://www.vigattindeals.com';
    const PAGE_TEST = 'http://www.vigattintrade.com';

    protected $validator;

    public function __construct()
    {
        $this->validator = new Validator();
    }

    /**
     * Use consume json data from import ads server
     * @param $page
     * @param int $offset
     * @param int $limit
     * @param string $id
     * @return array|mixed
     */
    public function importAds($page, $offset = 0, $limit = 10, $id = '')
    {
        //$id = 605;
        switch(strtolower($page)) {
            case 'vigattin':
                $serviceUrl = self::PAGE_VIGATTIN;
                break;
            case 'vigattintrade':
                $serviceUrl = self::PAGE_VIGATTINTRADE;
                break;
            case 'vigattindeals':
                $serviceUrl = self::PAGE_VIGATTINDEALS;
                break;
            case 'vigattintourism':
                $serviceUrl = self::PAGE_VIGATTINTOURISM;
                break;
            case 'test':
                $serviceUrl = self::PAGE_TEST;
                break;
            default:
                $serviceUrl = '';
                break;
        }
        $emptyData = array('total' => 0,  'list' => array());
        if($this->validator->isUrlValid($serviceUrl)) return $emptyData;
        $siteContent = file_get_contents($serviceUrl."/adsservice?page=$page&offset=$offset&limit=$limit&id=$id");
        $data = json_decode($siteContent, true);
        if(!is_array($data)) return $emptyData;
        return $data;
    }
}
