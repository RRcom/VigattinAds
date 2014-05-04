<?php
namespace VigattinAds\DomainModel;

use VigattinAds\DomainModel\Settings;
use Doctrine\ORM\NoResultException;
use Zend\ServiceManager\ServiceManager;

/**
 * Class SettingsManager
 * @package VigattinAds\DomainModel
 */
class SettingsManager
{
    const CACHE_PREFIX = 'vigatttinads_settings';

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    protected $serviceManager;

    /**
     * @var  \Zend\Cache\Storage\Adapter\Filesystem
     */
    protected $cache;

    public function __construct(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        $this->entityManager = $this->serviceManager->get('Doctrine\ORM\EntityManager');
        $this->cache = $this->serviceManager->get('VigattinAds\DomainModel\LongCache');
    }

    /**
     * Create or update setting
     * @param string $key
     * @param $value
     */
    public function set($key, $value)
    {
        $cacheKey = $this->createCacheKey($key);
        $query = $this->entityManager->createQuery("SELECT s FROM VigattinAds\DomainModel\Settings s WHERE s.key = :key");
        $query->setParameter('key', $key);
        try {
            $result = $query->getSingleResult();
            $result->setValue($value);
        } catch(NoResultException $ex) {
            $result = new Settings();
            $result->setKey($key);
            $result->setValue($value);
        }
        $this->entityManager->persist($result);
        $this->entityManager->flush();
        $this->cache->setItem($cacheKey, $value);
    }

    /**
     * Get the value of setting based on key name
     * @param $key
     * @param string $defaultValue
     * @return mixed|string
     */
    public function get($key, $defaultValue = '')
    {
        $cacheKey = $this->createCacheKey($key);
        $result = $this->cache->getItem($cacheKey);
        if($result != '') return $result;
        $query = $this->entityManager->createQuery("SELECT s.value FROM VigattinAds\DomainModel\Settings s WHERE s.key = :key");
        $query->setParameter('key', $key);
        try {
            $result = $query->getSingleScalarResult();
        } catch(NoResultException $ex) {
            $this->set($key, $defaultValue);
            $result = $defaultValue;
        }
        $this->cache->addItem($cacheKey, $defaultValue);
        return $result;
    }

    /**
     * Check if the setting exist
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        $cacheKey = $this->createCacheKey($key);
        $result = $this->cache->getItem($cacheKey);
        return ($result != '') ? true : false;
    }

    /**
     * Create an incremental integer value that will persist in the database
     * @param string $key name of the incremental integer
     * @return int
     */
    public function getIncrement($key)
    {
        $increment = intval($this->get($key, 0));
        $this->set($key, $increment+1);
        return $increment;
    }

    /**
     * Delete setting from database and cache
     * @param $key
     * @return mixed
     */
    public function delete($key)
    {
        $cacheKey = $this->createCacheKey($key);
        $this->cache->removeItem($cacheKey);
        $query = $this->entityManager->createQuery("DELETE VigattinAds\DomainModel\Settings s WHERE s.key = :key");
        $query->setParameter('key', $key);
        return $query->execute();

    }

    /**
     * Generate md5 hash key
     * @param $key
     * @return string
     */
    public function createCacheKey($key)
    {
        return md5(self::CACHE_PREFIX.$key);
    }
}