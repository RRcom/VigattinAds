<?php
namespace VigattinAdsTest\Controller;

use PHPUnit_Framework_TestCase;
use VigattinAds\DomainModel\AdsManager;
use VigattinAdsTest\Bootstrap;

class AdsManagerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var \VigattinAds\DomainModel\AdsManager
     */
    protected $adsManager;

    protected function setUp()
    {
        $serviceManager = Bootstrap::getServiceManager();
        $this->adsManager = new AdsManager($serviceManager);
    }

    public function testKeywordArrayToLikeDql()
    {
        $keywords = array(
            'cars',
            'houses',
            'appliances',
        );
        $result = $this->adsManager->keywordArrayToLikeDql($keywords);
        $this->assertTrue(is_array($result), '"keywordArrayToLikeDql" must return array');
        $this->assertEquals('(a.keywords LIKE :keyword0 OR a.keywords LIKE :keyword1 OR a.keywords LIKE :keyword2)', $result[0]);
        $this->assertTrue(count($result[1]) == 3);
        $count = 0;
        foreach($result[1] as $key => $value) {
            $this->assertEquals('keyword'.$count, $key);
            $this->assertEquals('%'.$keywords[$count].'%', $value);
            $count++;
        }
    }

    function testCountAdsTotal()
    {
        $showIn = 'vigattin-tourism-article';
        $template = '';
        $keywords = array('(tourism article header 36)', '(tourism article rightside 98)');

        $result = $this->adsManager->countAdsTotal($showIn, $template, $keywords);
        $this->assertTrue($result > 0, "total result is $result");
        return $result;
    }

    /**
     * @depends testCountAdsTotal
     */
    function testSearchAds($total)
    {
        $showIn = 'vigattin-tourism-article';
        $template = '';
        $keywords = array('(tourism article header 36)', '(tourism article rightside 98)');
        $start = 0;
        $limit = 10;
        $result = $this->adsManager->searchAds($showIn, $template, $keywords, $start, $limit);
        $this->assertEquals($total, count($result));
        foreach($result as $ads) {
            $this->assertTrue(is_array($ads));
            $this->assertTrue(count($ads) == 22);
            $this->assertTrue(($ads['id'] == 494) || ($ads['id'] == 484));
        }
    }

}