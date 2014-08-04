<?php
namespace VigattinAds\DomainModel\Tourism;

class ArticleCategory
{
    /** @var string String name of category */
    protected $categoryName;
    /** @var  int category id */
    protected $categoryId;

    function __construct($categoryId, $categoryName)
    {
        $this->categoryId = $categoryId;
        $this->categoryName = $categoryName;
    }

    /**
     * @return int
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * @return string
     */
    public function getCategoryName()
    {
        return $this->categoryName;
    }
}