<?php
namespace VigattinAds\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use VigattinAds\Entity\AdsView;
use VigattinAds\Entity\AdsUser;

/**
 * @ORM\Entity
 * @ORM\Table(name="ads", indexes={@ORM\Index(name="search_index", columns={"ads_name", "ads_url"})})
 */
class Ads {

    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_DISAPPROVED = -1;

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(name="ads_name", type="string", length=255)
     */
    protected $adsName;

    /**
     * @ORM\Column(name="ads_url", type="string", length=255)
     */
    protected $adsUrl;

    /**
     * @ORM\Column(name="ads_description", type="text")
     */
    protected $adsDescription;

    /**
     * @ORM\OneToMany(targetEntity="AdsView", mappedBy="ads")
     */
    protected $adsView = null;

    /**
     * @var string
     * @ORM\Column(name="show_in", type="string", length=255)
     */
    protected $showIn;

    /**
     * @var integer
     * @ORM\Column(name="status", type="smallint")
     */
    protected $status = 0;

    /**
     * @ORM\ManyToOne(targetEntity="AdsUser", inversedBy="ads")
     */
    protected $adsUser;

    //==================================================================================================

    public function __construct()
    {
        $this->adsView = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $adsView
     */
    public function addAdsView(AdsView $adsView)
    {
        $adsView->setAds($this);
        $this->adsView[] = $adsView;
    }

    /**
     * @param AdsUser $adsUser
     */
    public function setAdsUser(AdsUser $adsUser)
    {
        $this->adsUser = $adsUser;
    }

    /**
     * @param mixed $adsDescription
     */
    public function setAdsDescription($adsDescription)
    {
        $this->adsDescription = $adsDescription;
    }

    /**
     * @return mixed
     */
    public function getAdsDescription()
    {
        return $this->adsDescription;
    }

    /**
     * @param mixed $adsName
     */
    public function setAdsName($adsName)
    {
        $this->adsName = $adsName;
    }

    /**
     * @return mixed
     */
    public function getAdsName()
    {
        return $this->adsName;
    }

    /**
     * @param mixed $adsUrl
     */
    public function setAdsUrl($adsUrl)
    {
        $this->adsUrl = $adsUrl;
    }

    /**
     * @return mixed
     */
    public function getAdsUrl()
    {
        return $this->adsUrl;
    }

    /**
     * @param string $showIn
     */
    public function setShowIn($showIn)
    {
        $this->showIn = $showIn;
    }

    /**
     * @return string
     */
    public function getShowIn()
    {
        return $this->showIn;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }



}