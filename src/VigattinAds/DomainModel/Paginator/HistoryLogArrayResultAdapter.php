<?php
namespace VigattinAds\DomainModel\Paginator;

use Zend\Paginator\Adapter\AdapterInterface;
use VigattinAds\DomainModel\LogManager;

class HistoryLogArrayResultAdapter implements AdapterInterface
{
    /** @var \VigattinAds\DomainModel\LogManager */
    protected $logManager;

    public function __construct(LogManager $logManager)
    {
        $this->logManager = $logManager;
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
        return $this->logManager->searchCommonLog('', $offset, $itemCountPerPage);
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
        return $this->logManager->totalSearchCommonLog('');
    }
}