<?php
namespace VigattinAds\Model\Ads;

use Doctrine\ORM\EntityManager;
use VigattinAds\Entity\AdsUser as UserEntity;
use VigattinAds\Entity\Ads as AdsEntity;
use VigattinAds\Entity\AdsView;
use Zend\ServiceManager\ServiceManager;
use Doctrine\ORM\NoResultException;

class Ads
{
    const ORDER_BY_ASC = 0;
    const ORDER_BY_DESC = 1;
    const TRANSLATE_SATATUS = 0;

    protected $dictionary = array(
        self::TRANSLATE_SATATUS => array(
            AdsEntity::STATUS_DISAPPROVED => '<span class="text-danger"></spa><span class="glyphicon glyphicon-ban-circle"></span> Disapprove</span>',
            AdsEntity::STATUS_PENDING => '<span class="text-warning"></spa><span class="glyphicon glyphicon-warning-sign"></span> Pending</span>',
            AdsEntity::STATUS_APPROVED => '<span class="text-success"></spa><span class="glyphicon glyphicon-ok"></span> Approved</span>',
        ),
    );

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

    public function createAds($adsTitle, $adsUrl, $adsImage, $adsDescription, $showIn, $template, $keyword = '')
    {
        $ads = new AdsEntity();
        $ads->setAdsUser($this->userEntity);
        $ads->setAdsTitle($adsTitle);
        $ads->setAdsUrl($adsUrl);
        $ads->setAdsDescription($adsDescription);
        $ads->setShowIn($showIn);
        $ads->setTemplate($template);
        $ads->setKeywords($keyword);
        $ads->setAdsImage($adsImage);
        $this->entityManager->persist($ads);
        $this->entityManager->flush($ads);
        return $ads;
    }

    /**
     * @param int $start
     * @param int $limit
     * @param int $order
     * @param UserEntity $userEntity
     * @return \VigattinAds\Entity\Ads[]
     */
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
        try {
            $result = $query->getResult();
        } catch(NoResultException $ex) {
            return array();
        }
        return $result;

    }

    /**
     * @param int $id Ads Id
     * @return bool|\VigattinAds\Entity\Ads
     */
    public function getAds($id)
    {
        $query = $this->entityManager->createQuery("SELECT a FROM VigattinAds\Entity\Ads a WHERE a.id = :adsId AND a.adsUser = :userEntity");
        $query->setParameters(array(
            'adsId' => intval($id),
            'userEntity' => $this->userEntity,
        ));
        try {
            $result = $query->getSingleResult();
        } catch(NoResultException $ex) {
            return false;
        }
        return $result;
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

    public function translateStatus($result)
    {
        return $this->dictionary[self::TRANSLATE_SATATUS][$result];
    }
}