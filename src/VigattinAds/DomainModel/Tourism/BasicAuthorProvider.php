<?php
namespace VigattinAds\DomainModel\Tourism;
use Zend\ServiceManager\ServiceManager;

/**
 * Class BasicAuthorProvider
 * @package VigattinAds\DomainModel\Tourism
 */
class BasicAuthorProvider implements AuthorProviderInterface
{
    protected $total = 0;
    protected $apiUrl = "http://www.vigattintourism.com/service/author";

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
     * @param string $searchString The string to search for.
     * @param array $filter Search filter eg. array("gadget") to search for author that has a category of "gadget" in his article.
     * @param int $offset
     * @param int $limit
     * @return \VigattinAds\DomainModel\Tourism\AuthorCollection
     */
    public function searchAuthor($searchString = '', $filters = array(), $offset = 0, $limit = 10)
    {
        $strFilter = '';
        if(count($filters)) $strFilter = implode(',', $filters);
        $result = $this->apiCall($searchString, $strFilter, $offset, $limit);
        $this->total = $result['total'];
        $authors = new AuthorCollection();
        foreach($result['authors'] as $key => $value) {
            $authors->add(new Author($value['id'], $value['firstName'], $value['lastName'], $value['photoUrl']));
        }
        return $authors;
    }

    /**
     * @param string $searchString The string to search for.
     * @param array $filter Search filter eg. array("gadget") to search for author that has a category of "gadget" in his article.
     * @return int Total of search author
     */
    public function totalAuthor($searchString = '', $filter = array())
    {
        return $this->total;
    }

    /**
     * Call server api
     * @param string $searchString
     * @param array $filter
     * @param int $offset
     * @param int $limit
     * @return array|mixed
     */
    public function apiCall($searchString = '', $filter = array(), $offset = 0, $limit = 10)
    {
        $strFilter = serialize($filter);
        $key = md5($this->apiUrl."v1?string=$strFilter&filter=$filter&offset=$offset&limit=$limit");
        $result = unserialize($this->cache->getItem($key, $success));
        if(!$success) {
            $config = array(
                'adapter'   => 'Zend\Http\Client\Adapter\Curl',
                'curloptions' => array(CURLOPT_FOLLOWLOCATION => true),
            );
            $client = new \Zend\Http\Client($this->apiUrl, $config);
            $client->setParameterGet(array(
                'string'    => $searchString,
                'filter'    => $filter,
                'offset'    => $offset,
                'limit'     => $limit,
            ));
            $client->setMethod('GET');
            $response = $client->send();
            $result = json_decode($response->getBody(), true);
            $this->cache->setItem($key, serialize($result));
        }
        if(!is_array($result) || !isset($result['total'])) $result = array('total' => 0, 'authors' => array());
        return $result;
    }

}