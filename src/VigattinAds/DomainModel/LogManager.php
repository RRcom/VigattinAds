<?php
namespace VigattinAds\DomainModel;

use Zend\ServiceManager\ServiceManager;
use VigattinAds\DomainModel\CommonLog;
use VigattinAds\DomainModel\AdsUser;
use Doctrine\ORM\NoResultException;

class LogManager
{
    const SORT_DESC = 'DESC';
    const SORT_ASC = 'ASC';

    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @param ServiceManager $serviceManager
     */
    public function __construct(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        $this->entityManager = $this->serviceManager->get('Doctrine\ORM\EntityManager');
    }

    /**
     * Create new common log.
     * @param \VigattinAds\DomainModel\AdsUser $user User object currently committing the action.
     * @param const $logType CommonLog type constant eg. LOG_TYPE_ALTER_GOLD.
     * @param string $logMessage It is recommended to add the user full name email and username in the massage log followed by the message itself.
     * @param bool $autoFlush If true, will automatically call the orm flush method (write to database).
     * @return CommonLog CommonLog object
     */
    public function createCommonLog(AdsUser $user, $logType, $logMessage, $targetUserId, $autoFlush = false)
    {
        $commonLog = new CommonLog();
        $commonLog->set('serviceManager', $this->serviceManager);
        $commonLog
            ->set('userId', $user->get('id'))
            ->set('logType', $logType)
            ->set('logMessage', $logMessage)
            ->set('targetUserId', $targetUserId)
            ->set('timeCreated', time())
            ->persistSelf();
        if(!$autoFlush) return $commonLog;
        $commonLog->flushSelf();
        return $commonLog;
    }

    public function fetchCommonLogByUser(AdsUser $user, $start = 0, $limit = 10, $sortDirection = self::SORT_ASC)
    {
        $sortDirection = ($sortDirection == self::SORT_ASC || $sortDirection == self::SORT_DESC) ? $sortDirection : self::SORT_ASC;
        $dql = $this->entityManager->createQuery("SELECT l FROM VigattinAds\DomainModel\CommonLog l WHERE l.targetUserId = :targetUserId ORDER BY l.id $sortDirection");
        $dql->setParameter('targetUserId', $user->get('id'));
        $dql->setFirstResult($start);
        $dql->setMaxResults($limit);
        try {
            $result = $dql->getArrayResult();
        } catch(NoResultException $ex) {
            $result = array();
        }
        return $result;
    }

    public function fetchCommonLogByLogId($logId)
    {

    }
}