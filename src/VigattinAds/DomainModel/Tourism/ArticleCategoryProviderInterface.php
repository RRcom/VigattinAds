<?php
namespace VigattinAds\DomainModel\Tourism;

use \Zend\ServiceManager\ServiceManager;

interface ArticleCategoryProviderInterface
{
    /**
     * @param ServiceManager $serviceManager
     */
    public function __construct(ServiceManager $serviceManager);

    /**
     * @param int $offset
     * @param int $limit
     * @return \VigattinAds\DomainModel\Tourism\ArticleCategoryCollection
     */
    public function getCategories($offset = 0, $limit = 10);

}