<?php
namespace VigattinAds\Model;

use VigattinAds\Entity\Settings;
use Doctrine\ORM\NoResultException;
use Zend\ServiceManager\ServiceManager;

class SettingsManager
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    protected $serviceManager;

    public function __construct(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        $this->entityManager = $this->serviceManager->get('Doctrine\ORM\EntityManager');
    }

    public function set($key, $value)
    {
        $query = $this->entityManager->createQuery("SELECT s FROM VigattinAds\Entity\Settings s WHERE s.key = :key");
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
    }

    public function get($key, $defaultValue = '')
    {
        $query = $this->entityManager->createQuery("SELECT s.value FROM VigattinAds\Entity\Settings s WHERE s.key = :key");
        $query->setParameter('key', $key);
        try {
            $result = $query->getSingleScalarResult();
        } catch(NoResultException $ex) {
            $result = $defaultValue;
        }
        return $result;
    }

    public function has($key)
    {
        $query = $this->entityManager->createQuery("SELECT COUNT(s.id) FROM VigattinAds\Entity\Settings s WHERE s.key = :key");
        $query->setParameter('key', $key);
        return $query->getSingleScalarResult() ? true : false;
    }

    public function delete($key)
    {
        $query = $this->entityManager->createQuery("DELETE VigattinAds\Entity\Settings s WHERE s.key = :key");
        $query->setParameter('key', $key);
        return $query->execute();
    }
}