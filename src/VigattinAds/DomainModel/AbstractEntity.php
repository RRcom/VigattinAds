<?php
namespace VigattinAds\DomainModel;

abstract class AbstractEntity
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * Get property value
     * @param string $propertyName
     * @return mixed
     */
    public function get($propertyName)
    {
        return $this->$propertyName;
    }

    /**
     * Set property value
     * @param string $propertyName
     * @param mixed $value
     * @return Ads
     */
    public function set($propertyName, $value)
    {
        if($propertyName == 'id') return $this;
        if($propertyName == 'serviceManager')
        {
            if($value instanceof \Zend\ServiceManager\ServiceManager)
            {
                $this->entityManager = $value->get('Doctrine\ORM\EntityManager');
            }
        }
        $this->$propertyName = $value;
        return $this;
    }

    /**
     * Persist self entity
     */
    public function persistSelf()
    {
        $this->entityManager->persist($this);
    }

    /**
     * Flush all persist entity
     */
    public function flush()
    {
        $this->entityManager->flush();
    }

    /**
     * Reload fresh data from database
     */
    public function refresh()
    {
        $this->entityManager->refresh($this);
    }
}