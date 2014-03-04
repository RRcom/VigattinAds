<?php
namespace VigattinAds\DomainModel;

use Doctrine\ORM\Mapping as ORM;
use VigattinAds\DomainModel\AbstractEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="ads_approve_log", indexes={@ORM\Index(name="search_index", columns={"approved_time", "review_result", "review_version"})})
 */
class AdsApproveLog extends AbstractEntity
{
    const STATUS_GONE = -2;
    const STATUS_DISAPPROVED = -1;
    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_PAUSED = 2;
    const STATUS_REVIEWING = 3;

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @var \VigattinAds\DomainModel\AdsUser
     * @ORM\ManyToOne(targetEntity="VigattinAds\DomainModel\AdsUser")
     */
    protected $approver;

    /**
     * @var \VigattinAds\DomainModel\AdsUser
     * @ORM\ManyToOne(targetEntity="VigattinAds\DomainModel\Ads", inversedBy="adsApproveLog")
     */
    protected $ads;

    /**
     * @var String
     * @ORM\Column(name="review_version", type="string", length=255)
     */
    protected $reviewVersion;

    /**
     * @var int Ads Constant
     * @ORM\Column(name="review_result", type="smallint")
     */
    protected $reviewResult;

    /**
     * @var String
     * @ORM\Column(name="review_reason", type="text")
     */
    protected $reviewReason = '';

    /**
     * @var int
     * @ORM\Column(name="approved_time", type="integer", options={"unsigned"=true})
     */
    protected $approvedTime;

    public function __construct()
    {
        $this->approvedTime = time();
    }
}