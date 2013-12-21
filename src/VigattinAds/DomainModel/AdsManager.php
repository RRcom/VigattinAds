<?php
namespace VigattinAds\DomainModel;

use Zend\ServiceManager\ServiceManager;
use VigattinAds\DomainModel\Ads;
use VigattinAds\DomainModel\AdsUser;
use VigattinAds\DomainModel\SettingsManager;
use Doctrine\ORM\NoResultException;
use VigattinAds\DomainModel\AdsApproveLog;

class AdsManager
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /** @var \VigattinAds\DomainModel\SettingsManager */
    protected $settingsManager;

    /**
     * @param ServiceManager $serviceManager
     */
    public function __construct(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        $this->settingsManager = new SettingsManager($this->serviceManager);
        $this->entityManager = $this->serviceManager->get('Doctrine\ORM\EntityManager');
    }

    /**
     * @param AdsUser $adsUser
     * @param $adsTitle
     * @param $adsUrl
     * @param $adsImage
     * @param $adsDescription
     * @param $showIn
     * @param $template
     * @param string $keyword
     * @return Ads
     */
    public function createAds(AdsUser $adsUser, $adsTitle, $adsUrl, $adsImage, $adsDescription, $showIn, $template, $keyword = '')
    {
        $ads = new Ads($adsUser);
        $ads->set('adsTitle', $adsTitle)
            ->set('adsUrl', $adsUrl)
            ->set('adsImage', $adsImage)
            ->set('adsDescription', $adsDescription)
            ->set('showIn', $showIn)
            ->set('template', $template)
            ->set('keyword', $keyword)
            ->set('status', Ads::STATUS_PENDING);
        $this->entityManager->persist($ads);
        return $ads;
    }

    /**
     * Get list of ads to show, move the cursor to the next batch of ads for next retrieval (rotational fetch).
     * @param $showIn
     * @param $template
     * @param $keyword
     * @param int $limit
     * @return Ads[]
     */
    public function getRotationAds($showIn, $template, $keyword, $limit = 10)
    {
        $key = md5('global-rotate'.$showIn.$template.$keyword);
        $start = $this->settingsManager->get($key);
        $total = $this->countAdsTotal($showIn, $template, $keyword);

        // Check if start is higher than total and if so, reset start
        if($start > $total) $start = $start - $total;

        $result = $this->searchAds($showIn, $template, $keyword, $start, $limit);
        $resultTotal = count($result);

        // Check if result is lower than limit output if so query again to the first row to fill the the remaining ads
        if($resultTotal < $limit)
        {
            $result2 = $this->searchAds($showIn, $template, $keyword, 0, $limit - $resultTotal);
            $start = $limit - $resultTotal;
            foreach($result2 as $tmpRes)
            {
                $result[] = $tmpRes;
            }
        }
        else $start = $start + $limit;
        $this->settingsManager->set($key, $start);
        return $result;
    }

    /**
     * Count total of search result
     * @param $showIn
     * @param $template
     * @param $keyword
     * @return mixed
     */
    public function countAdsTotal($showIn, $template, $keyword)
    {
        if($keyword)
        {
            $query = $this->entityManager->createQuery("SELECT COUNT(a.id) FROM VigattinAds\DomainModel\Ads a WHERE a.status = 1 AND a.showIn = :showIn AND a.template = :template AND a.keywords LIKE :keyword AND a.viewLimit > 0");
            $query->setParameters(array('showIn' => $showIn, 'template' => $template, 'keyword' => '%'.$keyword.'%'));
        }
        else
        {
            $query = $this->entityManager->createQuery("SELECT COUNT(a.id) FROM VigattinAds\DomainModel\Ads a WHERE a.status = 1 AND a.showIn = :showIn AND a.template = :template AND a.viewLimit > 0");
            $query->setParameters(array('showIn' => $showIn, 'template' => $template));
        }
        return $query->getSingleScalarResult();
    }

    /**
     * @param $showIn
     * @param $template
     * @param $keyword
     * @param int $start
     * @param int $limit
     * @return AdsEntity[]
     */
    public function searchAds($showIn, $template, $keyword, $start = 0, $limit = 10)
    {
        if($keyword)
        {
            $query = $this->entityManager->createQuery("SELECT a FROM VigattinAds\DomainModel\Ads a WHERE a.status = 1 AND a.showIn = :showIn AND a.template = :template AND a.keywords LIKE :keyword AND a.viewLimit > 0");
            $query->setParameters(array('showIn' => $showIn, 'template' => $template, 'keyword' => '%'.$keyword.'%'));
        }
        else
        {
            $query = $this->entityManager->createQuery("SELECT a FROM VigattinAds\DomainModel\Ads a WHERE a.status = 1 AND a.showIn = :showIn AND a.template = :template AND a.viewLimit > 0");
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
     * Search multiple ads by array of ads ids
     * @param array $adsIds
     * @return array|bool
     */
    public function searchAdsByIds($adsIds)
    {
        $query = $this->entityManager->createQuery("SELECT a FROM VigattinAds\DomainModel\Ads a WHERE a.id IN (:adsIds)");
        $query->setParameter('adsIds', $adsIds);
        try {
            $result = $query->getResult();
        } catch(NoResultException $ex) {
            return false;
        }
        return $result;
    }

    /**
     * Get ads using review_version id
     * @param $reviewVersion
     * @return mixed|null
     */
    public function getAdsByReviewVersion($reviewVersion)
    {
        $query = $this->entityManager->createQuery("SELECT a FROM VigattinAds\DomainModel\Ads a WHERE a.reviewVersion = :version");
        $query->setParameter('version', $reviewVersion);
        try {
            $result = $query->getSingleResult();
        } catch(NoResultException $ex) {
            $result = null;
        }
        return $result;
    }

    /**
     * Get log
     * @param AdsUser $approver
     */
    public function getCurrentReviewingLog(AdsUser $approver)
    {
        $query = $this->entityManager->createQuery("SELECT l FROM VigattinAds\DomainModel\AdsApproveLog l WHERE l.approver = :user AND l.reviewResult = :result");
        $query->setParameters(array('user' => $approver, 'result' => Ads::STATUS_REVIEWING));
        try {
            $result = $query->getSingleResult();
        } catch(NoResultException $ex) {
            $result = null;
        }
        return $result;
    }

    /**
     * @param AdsUser $approver
     * @return Ads|null
     */
    public function getCurrentReviewingAds(AdsUser $approver)
    {
        $log = $this->getCurrentReviewingLog($approver);
        if(!($log instanceof AdsApproveLog)) return null;
        $ads = $this->getAdsByReviewVersion($log->get('reviewVersion'));
        if(!($ads instanceof Ads)) return null;
        return $ads;
    }

    /**
     * Get single pending ads waiting for review
     * @return Ads|null
     */
    public function getPendingAds()
    {
        $query = $this->entityManager->createQuery("SELECT a FROM VigattinAds\DomainModel\Ads a WHERE a.status = :status ORDER BY a.reviewVersion ASC");
        $query->setParameter('status', Ads::STATUS_PENDING);
        try {
            $result = $query->getSingleResult();
        } catch(NoResultException $ex) {
            $result = null;
        }
        return $result;
    }

    /**
     * @param AdsUser $approver
     * @param Ads $ads
     * @param $reviewVersion
     * @param int $reviewResult
     * @param string $reviewReason
     * @return AdsApproveLog
     */
    public function createReviewLog(AdsUser $approver, Ads $ads, $reviewVersion, $reviewResult = Ads::STATUS_REVIEWING, $reviewReason = '')
    {
        $log = new AdsApproveLog();
        $log->set('approver', $approver);
        $log->set('ads', $ads);
        $log->set('reviewVersion', $reviewVersion);
        $log->set('reviewResult', $reviewResult);
        $log->set('reviewReason', $reviewReason);
        $this->entityManager->persist($log);
        $this->entityManager->persist();
        return $log;
    }

    /**
     * Get ads to review
     */
    public function fetchAdsToReview(AdsUser $approver)
    {
        $ads = $this->getCurrentReviewingAds($approver);
        if(!($ads instanceof Ads))
        {
            $ads = $this->getPendingAds();
            if(!($ads instanceof Ads)) return null;
            $ads->set('status', Ads::STATUS_REVIEWING);
            $ads->persistSelf();
            //$this->createReviewLog($approver);


            $ads->flush();
        }
    }

    /**
     * Flush to database all insert to view
     */
    public function flush()
    {
        $this->entityManager->flush();
    }
}