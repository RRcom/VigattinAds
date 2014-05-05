<?php
namespace VigattinAds\DomainModel;

use Doctrine\ORM\Mapping as ORM;
use VigattinAds\DomainModel\AbstractEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="common_log", indexes={@ORM\Index(name="search_index", columns={"user_id", "log_type", "time_created", "target_user_id"})})
 */
class CommonLog extends AbstractEntity
{

    const LOG_TYPE_ALTER_GOLD = 1;
    const LOG_TYPE_ALTER_EMAIL = 2;
    const LOG_TYPE_ALTER_USERNAME = 3;
    const LOG_TYPE_ALTER_PASSWORD = 4;
    const LOG_TYPE_ALTER_FIRST_NAME = 5;
    const LOG_TYPE_ALTER_LAST_NAME = 6;
    const LOG_TYPE_ALTER_PRIVILEGE = 7;

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @var int
     * @ORM\Column(name="user_id", type="integer", options={"unsigned"=true})
     */
    protected $userId;

    /**
     * @var int Ads Constant
     * @ORM\Column(name="log_type", type="smallint")
     */
    protected $logType;

    /**
     * @var String
     * @ORM\Column(name="log_message", type="text")
     */
    protected $logMessage = '';

    /**
     * @var int
     * @ORM\Column(name="time_created", type="integer", options={"unsigned"=true})
     */
    protected $timeCreated;

    /**
     * @var int
     * @ORM\Column(name="target_user_id", type="integer", options={"unsigned"=true})
     */
    protected $targetUserId;

    /**
     * @param string $propertyName Value can be set are unsigned int userId, CommonLog::constant logType, string logMessage, unsigned int timeCreated, unsigned int targetUserId.
     * @param mixed $value
     * @return CommonLog
     */
    public function set($propertyName, $value)
    {
        return parent::set($propertyName, $value);
    }

    public function flushSelf()
    {
        $this->entityManager->flush($this);
    }
}