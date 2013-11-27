<?php
namespace VigattinAds\Model\Ads;

use Doctrine\ORM\EntityManager;
use VigattinAds\Entity\AdsUser as UserEntity;
use VigattinAds\Entity\Ads as AdsEntity;
use VigattinAds\Entity\AdsView;
use Zend\ServiceManager\ServiceManager;

class Ads
{
    const ORDER_BY_ASC = 0;
    const ORDER_BY_DESC = 1;

    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @var \VigattinAds\Entity\AdsUser
     */
    protected $userEntity;

    public function __construct(ServiceManager $serviceManager, UserEntity $userEntity = null)
    {
        $this->serviceManager = $serviceManager;
        $this->entityManager = $this->serviceManager->get('Doctrine\ORM\EntityManager');
        if($userEntity instanceof UserEntity) $this->userEntity = $userEntity;
        else $this->userEntity = new UserEntity();
    }

    public function setUserEntity(UserEntity $userEntity)
    {
        $this->userEntity = $userEntity;
    }

    public function createAds($adsName, $adsUrl, $adsDescription)
    {
        $ads = new AdsEntity();
        $ads->setAdsUser($this->userEntity);
        $ads->setAdsName($adsName);
        $ads->setAdsUrl($adsUrl);
        $ads->setAdsDescription($adsDescription);
        $this->entityManager->persist($ads);
        $this->entityManager->flush($ads);
        return $ads;
    }

    public function listAds($start = 0, $limit = 30, $order = self::ORDER_BY_ASC, UserEntity $userEntity = null)
    {
        switch($order)
        {
            case self::ORDER_BY_ASC:
                $order = 'ASC';
                break;
            case self::ORDER_BY_DESC:
                $order = 'DESC';
                break;
            default:
                $order = 'ASC';
                break;
        }
        if($userEntity === null) $userEntity = $this->userEntity;
        $query = $this->entityManager->createQuery("SELECT a FROM VigattinAds\Entity\Ads a WHERE a.adsUser = :userEntity ORDER BY a.id $order");
        $query->setParameter('userEntity', $userEntity);
        $query->setFirstResult($start);
        $query->setMaxResults($limit);
        return $query->getResult();
    }

    public function deleteAds($ads)
    {
        if(!is_array($ads)) $ads = array($ads);
        $count = 0;
        foreach($ads as $ad)
        {
            if($ad instanceof AdsEntity)
            {
                $this->entityManager->createQuery("DELETE VigattinAds\Entity\Ads a WHERE a = :adsEntity")
                    ->setParameter('adsEntity', $ad)
                    ->execute();
                $count++;
            }
        }
        return $count;
    }
}