<?php
namespace VigattinAds\DomainModel\Paginator;

use Zend\Paginator\Adapter\AdapterInterface;
use VigattinAds\DomainModel\AdsManager;

class AdsSearchArrayResultAdapter implements AdapterInterface
{
    /** @var \VigattinAds\DomainModel\AdsManager */
    protected $adsManager;

    protected $searchFiled = AdsManager::SEARCH_BY_ALL;

    protected $searchValue = '';

    protected $filterStatusBy = 100;

    public function __construct(AdsManager $adsManager)
    {
        $this->adsManager = $adsManager;
    }

    /**
     * Returns an collection of items for a page.
     *
     * @param  int $offset Page offset
     * @param  int $itemCountPerPage Number of items per page
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        // TODO: Implement getItems() method.
        $result = $this->adsManager->adminSearchAds2($this->searchFiled, $this->searchValue, $this->filterStatusBy, AdsManager::SORT_BY_ID, AdsManager::SORT_DIRECTION_ASC, $offset, $itemCountPerPage);
        return $result;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count()
    {
        // TODO: Implement count() method.
        $result = (int) $this->adsManager->adminCountAds2($this->searchFiled, $this->searchValue, $this->filterStatusBy);
        return $result;
    }

    public function setSearchFiled($value)
    {
        $this->searchFiled = $value;
    }

    public function setSearchValue($value)
    {
        $this->searchValue = $value;
    }

    public function setSearchFilter($value)
    {
        $this->filterStatusBy = $value;
    }
}