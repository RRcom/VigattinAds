<?php
namespace VigattinAds\DomainModel\Tourism;

/**
 * Class BasicArticleCategoryProvider
 * @package VigattinAds\DomainModel\Tourism
 */
class BasicArticleCategoryProvider implements ArticleCategoryProviderInterface
{
    protected $apiUrl = "http://www.vigattintourism.com/service/filter";

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
        if(!is_array($result)) $result = array();
        return $result;
    }

}