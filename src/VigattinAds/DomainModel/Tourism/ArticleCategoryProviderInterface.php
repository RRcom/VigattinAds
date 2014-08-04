<?php
namespace VigattinAds\DomainModel\Tourism;

interface ArticleCategoryProviderInterface
{
    /**
     * @param int $offset
     * @param int $limit
     * @return \VigattinAds\DomainModel\Tourism\ArticleCategoryCollection
     */
    public function getCategories($offset = 0, $limit = 10);

}