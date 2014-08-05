<?php
namespace VigattinAds\DomainModel\Tourism;
use Zend\ServiceManager\ServiceManager;

/**
 * Class BasicArticleCategoryProvider
 * @package VigattinAds\DomainModel\Tourism
 */
class BasicArticleCategoryProvider implements ArticleCategoryProviderInterface
{
    protected $apiUrl = "http://www.vigattintourism.com/service/filter";

    /** @var  ServiceManager */
    protected $serviceManager;

    /** @var \Zend\Cache\Storage\Adapter\Filesystem */
    protected $cache;

    /**
     * @param ServiceManager $serviceManager
     */
    public function __construct(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        $this->cache = $this->serviceManager->get('VigattinAds\DomainModel\LongCache');
    }


    /**
     * @param int $offset
     * @param int $limit
     * @return \VigattinAds\DomainModel\Tourism\ArticleCategoryCollection
     */
    public function getCategories($offset = 0, $limit = 10)
    {
        $result = $this->apiCall($offset, $limit);
        $categories = new ArticleCategoryCollection();
        foreach($result as $key => $value) {
            $categories->add(new ArticleCategory($value['category_id'], $value['category_name']));
        }
        return $categories;
    }

    /**
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function apiCall($offset = 0, $limit = 10)
    {
        $key = md5($this->apiUrl."v1?offset=$offset&limit=$limit");
        $result = unserialize($this->cache->getItem($key, $success));
        if(!$success) {
            $config = array(
                'adapter'   => 'Zend\Http\Client\Adapter\Curl',
                'curloptions' => array(CURLOPT_FOLLOWLOCATION => true),
            );
            $client = new \Zend\Http\Client($this->apiUrl, $config);
            $client->setParameterGet(array(
                'offset'    => $offset,
                'limit'     => $limit,
            ));
            $client->setMethod('GET');
            $response = $client->send();
            $result = json_decode($response->getBody(), true);
            $this->cache->setItem($key, serialize($result));
        }
        if(!is_array($result)) $result = array();
        return $result;
    }

}