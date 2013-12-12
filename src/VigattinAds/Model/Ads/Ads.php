<?php
namespace VigattinAds\Model\Ads;

use Doctrine\ORM\EntityManager;
use VigattinAds\Entity\AdsUser as UserEntity;
use VigattinAds\Entity\Ads as AdsEntity;
use VigattinAds\Entity\AdsView;
use VigattinAds\Entity\Settings;
use Zend\ServiceManager\ServiceManager;
use Doctrine\ORM\NoResultException;
use VigattinAds\Model\SettingsManager;

class Ads
{
    const ORDER_BY_ASC = 0;
    const ORDER_BY_DESC = 1;
    const TRANSLATE_SATATUS = 0;
    const TRANSLATE_SATATUS_2 = 1;

    /**
     * @var array
     */
    protected $dictionary = array(
        self::TRANSLATE_SATATUS => array(
            AdsEntity::STATUS_DISAPPROVED => '<span class="text-danger"><span class="glyphicon glyphicon-ban-circle"></span> Disapproved</span>',
            AdsEntity::STATUS_PENDING => '<span class="text-warning"><span class="glyphicon glyphicon-warning-sign"></span> Pending</span>',
            AdsEntity::STATUS_APPROVED => '<span class="text-success"><span class="glyphicon glyphicon-ok"></span> Approved</span>',
        ),
        self::TRANSLATE_SATATUS_2 => array(
            AdsEntity::STATUS_DISAPPROVED => '<span title="Disapproved" class="text-danger glyphicon glyphicon-ban-circle big-font-3em ads-view-info-col margin-right-30px"></span>',
            AdsEntity::STATUS_PENDING => '<span title="Pending" class="text-warning glyphicon glyphicon-warning-sign big-font-3em ads-view-info-col margin-right-30px"></span>',
            AdsEntity::STATUS_APPROVED => '<span title="Approved" class="text-success glyphicon glyphicon-ok big-font-3em ads-view-info-col margin-right-30px"></span>',
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

    /**
     * @var \VigattinAds\Model\SettingsManager
     */
    protected $settingsManager;

    /**
     * Create new instance of Ads model require service manager
     * @param ServiceManager $serviceManager
     * @param UserEntity $userEntity optional
     */
    public function __construct(ServiceManager $serviceManager, UserEntity $userEntity = null)
    {
        $this->serviceManager = $serviceManager;
        $this->entityManager = $this->serviceManager->get('Doctrine\ORM\EntityManager');
        if($userEntity instanceof UserEntity) $this->userEntity = $userEntity;
        else $this->userEntity = new UserEntity();
        $this->settingsManager = new SettingsManager($this->serviceManager);
    }

    /**
     * Set the current owner of this Ads
     * @param UserEntity $userEntity
     */
    public function setUserEntity(UserEntity $userEntity)
    {
        $this->userEntity = $userEntity;
    }

    /**
     * Create new ads to current user (userEntity must be set).
     * @param $adsTitle Title for the ads to be created.
     * @param $adsUrl Back link to your ads.
     * @param $adsImage Image to show with your ads.
     * @param $adsDescription Describe your ads here
     * @param $showIn Which website to show your ads
     * @param $template Which part of the site the ads will appear.
     * @param string $keyword Your target keyword where your ads will appear
     * @return AdsEntity Newly created ads entity based on the current setting of this method.
     */
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
     * @return AdsEntity[]
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
     * @return bool|AdsEntity
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

    /**
     * @param AdsEntity[] $ads Array of ads entity to be deleted;
     * @return int Count of deleted ads;
     */
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

    /**
     * Translate Ads status table field to html
     * @param $result
     * @return mixed
     */
    public function translateStatus($result, $mode = self::TRANSLATE_SATATUS)
    {
        return $this->dictionary[$mode][$result];
    }

    /**
     * Get ads without the need of user entity. Use to show ads on iFrame.
     * @param array $ids Array of ads id to be fetch
     * @return \VigattinAds\Entity\Ads[]|bool Array of user entity, false if no entity found.
     */
    public function publicGetAds($ids)
    {
        $query = $this->entityManager->createQuery("SELECT a FROM VigattinAds\Entity\Ads a WHERE a.id IN (:ids)");
        $query->setParameter('ids', $ids);
        try {
            $result = $query->getResult();
        } catch(NoResultException $ex) {
            return false;
        }
        return $result;
    }

    /**
     * @param $showIn
     * @param $template
     * @param $keyword
     * @param int $start
     * @param int $limit
     * @return AdsEntity[]
     */
    public function publicSearchAds($showIn, $template, $keyword, $start = 0, $limit = 10)
    {
        if($keyword)
        {
            $query = $this->entityManager->createQuery("SELECT a FROM VigattinAds\Entity\Ads a WHERE a.status = 1 AND a.showIn = :showIn AND a.template = :template AND a.keywords LIKE :keyword");
            $query->setParameters(array('showIn' => $showIn, 'template' => $template, 'keyword' => '%'.$keyword.'%'));
        }
        else
        {
            $query = $this->entityManager->createQuery("SELECT a FROM VigattinAds\Entity\Ads a WHERE a.status = 1 AND a.showIn = :showIn AND a.template = :template");
            $query->setParameters(array('showIn' => $showIn, 'template' => $template));
        }
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
     * Count total of search result
     * @param $showIn
     * @param $template
     * @param $keyword
     * @return mixed
     */
    public function publicSearchAdsTotal($showIn, $template, $keyword)
    {
        if($keyword)
        {
            $query = $this->entityManager->createQuery("SELECT COUNT(a.id) FROM VigattinAds\Entity\Ads a WHERE a.status = 1 AND a.showIn = :showIn AND a.template = :template AND a.keywords LIKE :keyword");
            $query->setParameters(array('showIn' => $showIn, 'template' => $template, 'keyword' => '%'.$keyword.'%'));
        }
        else
        {
            $query = $this->entityManager->createQuery("SELECT COUNT(a.id) FROM VigattinAds\Entity\Ads a WHERE a.status = 1 AND a.showIn = :showIn AND a.template = :template");
            $query->setParameters(array('showIn' => $showIn, 'template' => $template));
        }
        return $query->getSingleScalarResult();
    }

    /**
     * Get list of ads to show, move the cursor to the next batch of ads for next retrieval (rotational fetch).
     * @param $showIn
     * @param $template
     * @param $keyword
     * @param int $limit
     * @return array|\VigattinAds\Entity\Ads[]
     */
    public function publicGetRotationAds($showIn, $template, $keyword, $limit = 10)
    {
        $key = md5('global-rotate'.$showIn.$template.$keyword);
        $start = $this->settingsManager->get($key);
        $total = $this->publicSearchAdsTotal($showIn, $template, $keyword);

        // Check if start is higher than total and if so, reset start
        if($start > $total) $start = $start - $total;

        $result = $this->publicSearchAds($showIn, $template, $keyword, $start, $limit);
        $resultTotal = count($result);

        // Check if result is lower than limit output if so query again to the first row to fill the the remaining ads
        if($resultTotal < $limit)
        {
            $result2 = $this->publicSearchAds($showIn, $template, $keyword, 0, $limit - $resultTotal);
            $start = $limit - $resultTotal;
            foreach($result2 as $tmpres)
            {
                $result[] = $tmpres;
            }
        }
        else $start = $start + $limit;
        $this->settingsManager->set($key, $start);
        return $result;
    }

    public function countViews($adsId)
    {
        $query = $this->entityManager->createQuery("SELECT COUNT(v.id) FROM VigattinAds\Entity\AdsView v WHERE v.ads = :adsId");
        $query->setParameter('adsId', $adsId);
        return $query->getSingleScalarResult();
    }

    /**
     * @return array|EntityManager|object
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }
}