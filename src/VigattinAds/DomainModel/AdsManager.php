<?php
namespace VigattinAds\DomainModel;

use Zend\ServiceManager\ServiceManager;
use VigattinAds\DomainModel\Ads;
use VigattinAds\DomainModel\AdsUser;
use VigattinAds\DomainModel\SettingsManager;
use Doctrine\ORM\NoResultException;
use VigattinAds\DomainModel\AdsApproveLog;
use Zend\Cache\StorageFactory;

class AdsManager
{
    const SORT_BY_ID = 0;
    const SORT_BY_TITLE = 1;
    const SORT_BY_EMAIL = 2;
    const SORT_BY_USERNAME = 3;
    const SORT_BY_FIRST_NAME = 4;
    const SORT_BY_LAST_NAME = 5;
    const SORT_BY_STATUS = 6;

    const SORT_DIRECTION_ASC = 0;
    const SORT_DIRECTION_DESC = 1;

    const SEARCH_BY_ID = 0;
    const SEARCH_BY_TITLE = 1;
    const SEARCH_BY_EMAIL = 2;
    const SEARCH_BY_USERNAME = 3;
    const SEARCH_BY_FIRST_NAME = 4;
    const SEARCH_BY_LAST_NAME = 5;
    const SEARCH_BY_ALL = 6;
    const SEARCH_BY_STATUS = 7;

    const FILTER_BY_ID = 0;
    const FILTER_BY_TITLE = 1;
    const FILTER_BY_EMAIL = 2;
    const FILTER_BY_USERNAME = 3;
    const FILTER_BY_FIRST_NAME = 4;
    const FILTER_BY_LAST_NAME = 5;
    const FILTER_BY_ALL = 6;
    const FILTER_BY_STATUS = 7;

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

    /** @var  \Zend\Cache\Storage\Adapter\Filesystem */
    protected $cache;

    /** @var  \Zend\Cache\Storage\Adapter\Filesystem */
    protected $cacheQuickExpire;

    /**
     * @param ServiceManager $serviceManager
     */
    public function __construct(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        $this->settingsManager = new SettingsManager($this->serviceManager);
        $this->entityManager = $this->serviceManager->get('Doctrine\ORM\EntityManager');
        $this->cache = $this->serviceManager->get('VigattinAds\DomainModel\LongCache');
        $this->cacheQuickExpire = $this->serviceManager->get('VigattinAds\DomainModel\ShortCache');
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
            ->set('status', Ads::STATUS_PENDING)
            ->set('userUsername', $adsUser->get('username'))
            ->set('userEmail', $adsUser->get('email'))
            ->set('userFirstName', $adsUser->get('firstName'))
            ->set('userLastName', $adsUser->get('lastName'))
            ->set('createdTime', time());
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
        //echo $showIn.'-'.$template.'-'.$keyword;
        //exit();

        $success = false;
        $key = md5('global-rotate'.$showIn.$template.serialize($keyword));
        $start = $this->cache->getItem($key, $success);
        if(!$success) {
            $start = 0;
            $this->cache->setItem($key, $start);
        }
        //$start = 2;
        $total = $this->countAdsTotal($showIn, $template, $keyword);

        // Check if start is higher than total and if so, reset start
        if($start > $total) $start = $start - $total;

        $result = $this->searchAds($showIn, $template, $keyword, $start, $limit);
        $resultTotal = count($result);

        // Check if result is lower than limit output if so query again to the first row to fill the the remaining ads
        if(($resultTotal < $limit))
        {
            if($total < $limit) $result2 = $this->searchAds($showIn, $template, $keyword, 0, $total - $resultTotal);
            else $result2 = $this->searchAds($showIn, $template, $keyword, 0, $limit - $resultTotal);
            $start = $limit - $resultTotal;
            foreach($result2 as $tmpRes)
            {
                $result[] = $tmpRes;
            }
        }
        else $start = $start + $limit;
        $this->cache->setItem($key, $start);
        return $result;
    }

    /**
     * Count total of search result
     * @param $showIn
     * @param $template leave empty to count all
     * @param $keyword leave empty to count all
     * @return mixed
     */
    public function countAdsTotal($showIn, $template, $keyword)
    {
        $queryKey = md5('countAdsTotal'.$showIn.$template.serialize($keyword));
        $result = $this->cacheQuickExpire->getItem($queryKey);
        if($result) return $result;
        if($keyword)
        {
            $keywordsResult = $this->keywordArrayToLikeDql($keyword);
            $keywordDql = $keywordsResult[0];
            $keywordValue = $keywordsResult[1];
            if($template) {
                $params = array('showIn' => $showIn, 'template' => $template);
                $params = array_merge($params, $keywordValue);
                $query = $this->entityManager->createQuery("SELECT COUNT(a.id) FROM VigattinAds\DomainModel\Ads a WHERE a.deleted = 0 AND a.status = 1 AND a.showIn = :showIn AND a.template = :template AND $keywordDql AND a.viewLimit > 0");
                $query->setParameters($params);
            } else {
                $params = array('showIn' => $showIn);
                $params = array_merge($params, $keywordValue);
                $query = $this->entityManager->createQuery("SELECT COUNT(a.id) FROM VigattinAds\DomainModel\Ads a WHERE a.deleted = 0 AND a.status = 1 AND a.showIn = :showIn AND $keywordDql AND a.viewLimit > 0");
                $query->setParameters($params);
            }
        }
        else
        {
            if($template) {
                $query = $this->entityManager->createQuery("SELECT COUNT(a.id) FROM VigattinAds\DomainModel\Ads a WHERE a.deleted = 0 AND a.status = 1 AND a.showIn = :showIn AND a.template = :template AND a.viewLimit > 0");
                $query->setParameters(array('showIn' => $showIn, 'template' => $template));
            } else {
                $query = $this->entityManager->createQuery("SELECT COUNT(a.id) FROM VigattinAds\DomainModel\Ads a WHERE a.deleted = 0 AND a.status = 1 AND a.showIn = :showIn AND a.viewLimit > 0");
                $query->setParameters(array('showIn' => $showIn));
            }
        }

        $result = $query->getSingleScalarResult();
        $this->cacheQuickExpire->addItem($queryKey, $result);
        return $result;
    }

    /**
     * @param $showIn
     * @param $template leave empty to search all
     * @param $keyword leave empty to search all
     * @param int $start
     * @param int $limit
     * @return AdsEntity[]
     */
    public function searchAds($showIn, $template, $keyword, $start = 0, $limit = 10)
    {
        $queryKey = md5('searchAds'.$showIn.$template.serialize($keyword).$start.$limit);
        $result = $this->cacheQuickExpire->getItem($queryKey);
        if($result) return unserialize($result);
        if($keyword)
        {
            $keywordsResult = $this->keywordArrayToLikeDql($keyword);
            $keywordDql = $keywordsResult[0];
            $keywordValue = $keywordsResult[1];
            if($template) {
                $params = array('showIn' => $showIn, 'template' => $template);
                $params = array_merge($params, $keywordValue);
                $query = $this->entityManager->createQuery("SELECT a FROM VigattinAds\DomainModel\Ads a WHERE a.deleted = 0 AND a.status = 1 AND a.showIn = :showIn AND a.template = :template AND $keywordDql AND a.viewLimit > 0");
                $query->setParameters($params);
            } else {
                $params = array('showIn' => $showIn);
                $params = array_merge($params, $keywordValue);
                $query = $this->entityManager->createQuery("SELECT a FROM VigattinAds\DomainModel\Ads a WHERE a.deleted = 0 AND a.status = 1 AND a.showIn = :showIn AND $keywordDql AND a.viewLimit > 0");
                $query->setParameters($params);
            }
        }
        else
        {
            if($template) {
                $query = $this->entityManager->createQuery("SELECT a FROM VigattinAds\DomainModel\Ads a WHERE a.deleted = 0 AND a.status = 1 AND a.showIn = :showIn AND a.template = :template AND a.viewLimit > 0");
                $query->setParameters(array('showIn' => $showIn, 'template' => $template));
            } else {
                $query = $this->entityManager->createQuery("SELECT a FROM VigattinAds\DomainModel\Ads a WHERE a.deleted = 0 AND a.status = 1 AND a.showIn = :showIn AND a.viewLimit > 0");
                $query->setParameters(array('showIn' => $showIn));
            }
        }
        $query->setFirstResult(intval($start));
        $query->setMaxResults(intval($limit));
        try {
            $result = $query->getArrayResult();
        } catch(NoResultException $ex) {
            return array();
        }
        $this->cacheQuickExpire->addItem($queryKey, serialize($result));
        return $result;
    }

    /**
     * Search all ads used by admin panel
     * @param int $searchField
     * @param string $searchValue
     * @param int $sortBy
     * @param int $sortDirection
     * @param int $start
     * @param int $limit
     * @return array
     */
    public function adminSearchAds($searchField = self::SEARCH_BY_ALL, $searchValue = '', $sortBy = self::SORT_BY_ID, $sortDirection = self::SORT_DIRECTION_ASC, $start = 0, $limit = 30)
    {
        $searchField = intval($searchField);
        if(($searchField > 6) || ($searchField < 0)) $searchField = self::SEARCH_BY_ALL;

        $fieldName = array(
            'id',
            'adsTitle',
            'userEmail',
            'userUsername',
            'userFirstName',
            'userLastName'
        );
        $direction = array(
            'ASC',
            'DESC'
        );

        // list all
        if(($searchField == self::SEARCH_BY_ALL) && $searchValue == '') {
            $query = $this->entityManager->createQuery("SELECT a FROM VigattinAds\DomainModel\Ads a ORDER BY a.".$fieldName[$sortBy]." ".$direction[$sortDirection]);
        }
        // search match all field
        elseif($searchField == self::SEARCH_BY_ALL) {
            $query = $this->entityManager->createQuery("SELECT a FROM VigattinAds\DomainModel\Ads a WHERE a.adsTitle LIKE :searchValue OR a.userEmail LIKE :searchValue OR a.userUsername LIKE :searchValue OR a.userFirstName LIKE :searchValue OR a.userLastName LIKE :searchValue ORDER BY a.".$fieldName[$sortBy]." ".$direction[$sortDirection]);
            $query->setParameter('searchValue', $searchValue.'%');
        }
        // search by field
        else {
            $query = $this->entityManager->createQuery("SELECT a FROM VigattinAds\DomainModel\Ads a WHERE a.".$fieldName[$searchField]." LIKE :searchValue ORDER BY a.".$fieldName[$sortBy]." ".$direction[$sortDirection]);
            $query->setParameter('searchValue', $searchValue.'%');
        }
        $query->setFirstResult($start);
        $query->setMaxResults($limit);
        $result = $query->getResult();
        return $result;
    }

    public function adminSearchAds2($searchField = self::SEARCH_BY_ALL, $searchValue = '', $filterStatusBy = 100, $sortBy = self::SORT_BY_ID, $sortDirection = self::SORT_DIRECTION_ASC, $start = 0, $limit = 30)
    {
        $searchField = intval($searchField);
        $filterStatusBy = intval($filterStatusBy);
        if(($searchField > 6) || ($searchField < 0)) $searchField = self::SEARCH_BY_ALL;

        $fieldName = array(
            'id',
            'adsTitle',
            'userEmail',
            'userUsername',
            'userFirstName',
            'userLastName'
        );
        $direction = array(
            'ASC',
            'DESC'
        );

        // list all
        if(($searchField == self::SEARCH_BY_ALL) && $searchValue == '') {
            // no filter
            if($filterStatusBy > 3) {
                $query = $this->entityManager->createQuery("SELECT a FROM VigattinAds\DomainModel\Ads a ORDER BY a.".$fieldName[$sortBy]." ".$direction[$sortDirection]);
            }
            // with filter
            else {
                $query = $this->entityManager->createQuery("SELECT a FROM VigattinAds\DomainModel\Ads a WHERE a.status = $filterStatusBy ORDER BY a.".$fieldName[$sortBy]." ".$direction[$sortDirection]);
            }
        }
        // search match all field
        elseif($searchField == self::SEARCH_BY_ALL) {
            // no filter
            if($filterStatusBy > 3) {
                $query = $this->entityManager->createQuery("SELECT a FROM VigattinAds\DomainModel\Ads a WHERE a.adsTitle LIKE :searchValue OR a.userEmail LIKE :searchValue OR a.userUsername LIKE :searchValue OR a.userFirstName LIKE :searchValue OR a.userLastName LIKE :searchValue OR CONCAT(a.userFirstName, ' ', a.userLastName) LIKE :fullName ORDER BY a.".$fieldName[$sortBy]." ".$direction[$sortDirection]);
            }
            // with filter
            else {
                $query = $this->entityManager->createQuery("SELECT a FROM VigattinAds\DomainModel\Ads a WHERE a.status = $filterStatusBy AND (a.adsTitle LIKE :searchValue OR a.userEmail LIKE :searchValue OR a.userUsername LIKE :searchValue OR a.userFirstName LIKE :searchValue OR a.userLastName LIKE :searchValue OR CONCAT(a.userFirstName, ' ', a.userLastName) LIKE :fullName) ORDER BY a.".$fieldName[$sortBy]." ".$direction[$sortDirection]);
            }
            $query->setParameter('searchValue', $searchValue.'%');
            $query->setParameter('fullName', $searchValue.'%');
        }
        // search by field
        else {
            // no filter
            if($filterStatusBy > 3) {
                $query = $this->entityManager->createQuery("SELECT a FROM VigattinAds\DomainModel\Ads a WHERE a.".$fieldName[$searchField]." LIKE :searchValue ORDER BY a.".$fieldName[$sortBy]." ".$direction[$sortDirection]);
            }
            // with filter
            else {
                $query = $this->entityManager->createQuery("SELECT a FROM VigattinAds\DomainModel\Ads a WHERE a.status = $filterStatusBy AND (a.".$fieldName[$searchField]." LIKE :searchValue) ORDER BY a.".$fieldName[$sortBy]." ".$direction[$sortDirection]);
            }
            $query->setParameter('searchValue', $searchValue.'%');
        }
        $query->setFirstResult($start);
        $query->setMaxResults($limit);
        $result = $query->getResult();
        return $result;
    }

    public function adminCountAds($searchField = self::SEARCH_BY_ALL, $searchValue = '')
    {
        $searchField = intval($searchField);
        if(($searchField > 6) || ($searchField < 0)) $searchField = self::SEARCH_BY_ALL;

        $fieldName = array(
            'id',
            'adsTitle',
            'userEmail',
            'userUsername',
            'userFirstName',
            'userLastName'
        );

        // count all
        if(($searchField == self::SEARCH_BY_ALL) && $searchValue == '') {
            $query = $this->entityManager->createQuery("SELECT COUNT(a.id) FROM VigattinAds\DomainModel\Ads a");
        }
        // search match all field
        elseif($searchField == self::SEARCH_BY_ALL) {
            $query = $this->entityManager->createQuery("SELECT COUNT(a.id) FROM VigattinAds\DomainModel\Ads a WHERE a.adsTitle LIKE :searchValue OR a.userEmail LIKE :searchValue OR a.userUsername LIKE :searchValue OR a.userFirstName LIKE :searchValue OR a.userLastName LIKE :searchValue");
            $query->setParameter('searchValue', $searchValue.'%');
        }
        // search by field
        else {
            $query = $this->entityManager->createQuery("SELECT COUNT(a.id) FROM VigattinAds\DomainModel\Ads a WHERE a.".$fieldName[$searchField]." LIKE :searchValue");
            $query->setParameter('searchValue', $searchValue.'%');
        }
        $result = $query->getSingleScalarResult();
        return $result;
    }

    public function adminCountAds2($searchField = self::SEARCH_BY_ALL, $searchValue = '', $filterStatusBy = 100)
    {
        $searchField = intval($searchField);
        $filterStatusBy = intval($filterStatusBy);
        if(($searchField > 6) || ($searchField < 0)) $searchField = self::SEARCH_BY_ALL;

        $fieldName = array(
            'id',
            'adsTitle',
            'userEmail',
            'userUsername',
            'userFirstName',
            'userLastName'
        );

        // count all
        if(($searchField == self::SEARCH_BY_ALL) && $searchValue == '') {
            // no filter
            if($filterStatusBy > 3) {
                $query = $this->entityManager->createQuery("SELECT COUNT(a.id) FROM VigattinAds\DomainModel\Ads a");
            }
            // with filter
            else {
                $query = $this->entityManager->createQuery("SELECT COUNT(a.id) FROM VigattinAds\DomainModel\Ads a WHERE a.status = $filterStatusBy");
            }

        }
        // search match all field
        elseif($searchField == self::SEARCH_BY_ALL) {
            // no filter
            if($filterStatusBy > 3) {
                $query = $this->entityManager->createQuery("SELECT COUNT(a.id) FROM VigattinAds\DomainModel\Ads a WHERE a.adsTitle LIKE :searchValue OR a.userEmail LIKE :searchValue OR a.userUsername LIKE :searchValue OR a.userFirstName LIKE :searchValue OR a.userLastName LIKE :searchValue OR CONCAT(a.userFirstName, ' ', a.userLastName) LIKE :fullName");
            }
            // with filter
            else {
                $query = $this->entityManager->createQuery("SELECT COUNT(a.id) FROM VigattinAds\DomainModel\Ads a WHERE a.status = $filterStatusBy AND (a.adsTitle LIKE :searchValue OR a.userEmail LIKE :searchValue OR a.userUsername LIKE :searchValue OR a.userFirstName LIKE :searchValue OR a.userLastName LIKE :searchValue OR CONCAT(a.userFirstName, ' ', a.userLastName) LIKE :fullName)");
            }
            $query->setParameter('searchValue', $searchValue.'%');
            $query->setParameter('fullName', $searchValue.'%');
        }
        // search by field
        else {
            // no filter
            if($filterStatusBy > 3) {
                $query = $this->entityManager->createQuery("SELECT COUNT(a.id) FROM VigattinAds\DomainModel\Ads a WHERE a.".$fieldName[$searchField]." LIKE :searchValue");
            }
            else {
                $query = $this->entityManager->createQuery("SELECT COUNT(a.id) FROM VigattinAds\DomainModel\Ads a WHERE a.status = $filterStatusBy AND (a.".$fieldName[$searchField]." LIKE :searchValue)");
            }
            $query->setParameter('searchValue', $searchValue.'%');
        }
        $result = $query->getSingleScalarResult();
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
     * Get ads to review
     */
    public function fetchAdsToReview(AdsUser $approver)
    {
        $ads = $this->getCurrentReviewingAds($approver);
        if($ads instanceof Ads) return $ads;
        else
        {
            $ads = $this->getPendingAds();
            if(!($ads instanceof Ads)) return null;
            $this->createReviewLog($approver, $ads, $ads->get('reviewVersion'));
            $ads->flush();
            return $ads;
        }
    }

    public function changeAdsStatus($adsVersion, $status, $reason = '')
    {
        $ads = $this->getAdsByReviewVersion($adsVersion);
        $log = $this->getLogByReviewVersion($adsVersion);
        if(!($ads instanceof Ads)) return;
        switch($status)
        {
            case Ads::STATUS_APPROVED:
                $ads->set('status', Ads::STATUS_APPROVED);
                $ads->set('adsLastNote', $reason);
                $log->set('reviewReason', $reason);
                $log->set('reviewResult', Ads::STATUS_APPROVED);
                $log->set('approvedTime', time());
                $ads->persistSelf();
                $log->persistSelf();
                $log->flush();
                break;
            case Ads::STATUS_DISAPPROVED:
                $ads->set('status', Ads::STATUS_DISAPPROVED);
                $ads->set('adsLastNote', $reason);
                $log->set('reviewReason', $reason);
                $log->set('reviewResult', Ads::STATUS_DISAPPROVED);
                $log->set('approvedTime', time());
                $ads->persistSelf();
                $log->persistSelf();
                $log->flush();
                break;
        }
    }

    /**
     * @param AdsUser $approver
     * @param Ads $ads
     * @param $reviewVersion
     * @param int $reviewResult
     * @param string $reviewReason
     * @return AdsApproveLog
     */
    public  function createReviewLog(AdsUser $approver, Ads $ads, $reviewVersion, $reviewResult = Ads::STATUS_REVIEWING, $reviewReason = '')
    {
        $log = new AdsApproveLog();
        $log->set('entityManager', $this->entityManager);
        $log->set('approver', $approver);
        $log->set('ads', $ads);
        $log->set('reviewVersion', $reviewVersion);
        $log->set('reviewResult', $reviewResult);
        $log->set('reviewReason', $reviewReason);
        $log->set('approvedTime', time());
        $log->persistSelf();
        return $log;
    }

    /**
     * Flush to database all insert to view
     */
    public function flush()
    {
        $this->entityManager->flush();
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
        $query->setMaxResults(1);
        try {
            $result = $query->getSingleResult();
        } catch(NoResultException $ex) {
            $result = null;
        }
        return $result;
    }

    /**
     * @param $reviewVersion
     * @return \VigattinAds\DomainModel\AdsApproveLog|null
     */
    public function getLogByReviewVersion($reviewVersion)
    {
        $query = $this->entityManager->createQuery("SELECT l FROM VigattinAds\DomainModel\AdsApproveLog l WHERE l.reviewVersion = :version");
        $query->setParameter('version', $reviewVersion);
        $query->setMaxResults(1);
        try {
            $result = $query->getSingleResult();
        } catch(NoResultException $ex) {
            $result = null;
        }
        return $result;
    }

    public function countPendingAds()
    {
        $query = $this->entityManager->createQuery("SELECT COUNT(a.id) FROM VigattinAds\DomainModel\Ads a WHERE a.status = :status");
        $query->setParameter('status', Ads::STATUS_PENDING);
        $result = $query->getSingleScalarResult();
        return $result;
    }

    /**
     * @param string|array $keywords
     * @return array generated dql query at index 0 ex. (a.keywords LIKE :keyword0 OR a.keywords LIKE :keyword1 ...) and array value in index 1 ex. array('keyword0' => 'cars', 'keyword1' => 'houses' ...)
     */
    public function keywordArrayToLikeDql($keywords)
    {
        $dql = '';
        $value = array();
        if(!is_array($keywords)) $keywords = array($keywords);
        foreach($keywords as $key => $keyword) {
            $dql .= 'a.keywords LIKE :keyword'.$key.' OR ';
            $value['keyword'.$key] = "%$keyword%";
        }
        $dql = '('.rtrim($dql, ' OR').')';
        return array($dql, $value);
    }

    /**
     * Get log
     * @param AdsUser $approver
     */
    protected  function getCurrentReviewingLog(AdsUser $approver)
    {
        $query = $this->entityManager->createQuery("SELECT l FROM VigattinAds\DomainModel\AdsApproveLog l WHERE l.approver = :user AND l.reviewResult = :result");
        $query->setParameters(array('user' => $approver, 'result' => Ads::STATUS_REVIEWING));
        $query->setMaxResults(1);
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
    protected  function getCurrentReviewingAds(AdsUser $approver)
    {
        $log = $this->getCurrentReviewingLog($approver);
        if(!($log instanceof AdsApproveLog)) return null;
        $ads = $this->getAdsByReviewVersion($log->get('reviewVersion'));
        if(!($ads instanceof Ads))
        {
            $log->set('reviewResult', Ads::STATUS_VALUE_CHANGED);
            $log->persistSelf();
            $log->flush();
            return null;
        }
        return $ads;
    }

    /**
     * Get single pending ads waiting for review
     * @return Ads|null
     */
    protected  function getPendingAds()
    {
        $query = $this->entityManager->createQuery("SELECT a FROM VigattinAds\DomainModel\Ads a WHERE a.status = :status ORDER BY a.reviewVersion ASC");
        $query->setParameter('status', Ads::STATUS_PENDING);
        $query->setMaxResults(1);
        try {
            $result = $query->getSingleResult();
        } catch(NoResultException $ex) {
            $result = null;
        }
        if($result instanceof Ads)
        {
            $result->set('status', Ads::STATUS_REVIEWING);
            $result->persistSelf();
            return $result;
        }
        return null;
    }
}