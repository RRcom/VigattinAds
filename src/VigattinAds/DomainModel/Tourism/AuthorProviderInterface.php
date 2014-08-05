<?php
namespace VigattinAds\DomainModel\Tourism;

use \Zend\ServiceManager\ServiceManager;

interface AuthorProviderInterface
{
    /**
     * @param ServiceManager $serviceManager
     */
    public function __construct(ServiceManager $serviceManager);

    /**
     * @param string $searchString The string to search for.
     * @param array $filter Search filter eg. array("gadget") to search for author that has a category of "gadget" in his article.
     * @param int $offset
     * @param int $limit
     * @return \VigattinAds\DomainModel\Tourism\AuthorCollection
     */
    public function searchAuthor($searchString = '', $filter = array(), $offset = 0, $limit = 10);

    /**
     * @param string $searchString The string to search for.
     * @param array $filter Search filter eg. array("gadget") to search for author that has a category of "gadget" in his article.
     * @return int Total of search author
     */
    public function totalAuthor($searchString = '', $filter = array());
}