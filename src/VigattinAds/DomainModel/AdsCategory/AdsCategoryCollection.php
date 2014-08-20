<?php
namespace VigattinAds\DomainModel\AdsCategory;

class AdsCategoryCollection implements \Iterator
{
    /**
     * @var array
     */
    protected $var = array();

    public function __construct($array = null) {
        if (is_array($array)) {
            foreach($array as $object) {
                if(!($object instanceof AdsCategory)) throw new Exception('object need to be instance of VigattinAds\DomainModel\AdsCategory\AdsCategory');
                $this->var[] = $object;
            }
        }
    }

    /**
     * @param \VigattinAds\DomainModel\AdsCategory\AdsCategory $single push 1 object to the collection
     * @param string|int $key key for the current data else just push to the end of array
     */
    public function add(AdsCategory $single, $key = null) {
        if($key !== null) $this->var[$key] = $single;
        else $this->var[] = $single;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return \VigattinAds\DomainModel\AdsCategory\AdsCategory
     */
    public function current()
    {
        $var = current($this->var);
        return $var;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        next($this->var);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        $var = key($this->var);
        return $var;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        $key = key($this->var);
        $var = ($key !== NULL && $key !== FALSE);
        return $var;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        reset($this->var);
    }
}