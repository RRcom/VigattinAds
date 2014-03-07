<?php
namespace VigattinAds\DomainModel\Paginator;

use Zend\Paginator\Adapter\AdapterInterface;
use VigattinAds\DomainModel\UserManager;

class ArrayResultAdapter implements AdapterInterface
{
    /** @var \VigattinAds\DomainModel\UserManager */
    protected $userManager;

    protected $searchFiled = UserManager::SEARCH_BY_ALL;

    protected $searchValue = '';

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
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
        $result = $this->userManager->getUserList(UserManager::SORT_BY_ID, UserManager::SORT_DIRECTION_ASC, $offset, $itemCountPerPage, $this->searchFiled, $this->searchValue);
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
        $result = (int) $this->userManager->countUserList($this->searchFiled, $this->searchValue);
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
}