<?php
namespace VigattinAds\Entity;

use Doctrine\ORM\Mapping as ORM;
use VigattinAds\Entity\Ads;

/**
 * @ORM\Entity
 * @ORM\Table(name="ads_view", indexes={@ORM\Index(name="search_index", columns={"view_time", "clicked", "ads_referrer"})})
 */
class AdsView {

    /**
     * Unique ID of ads view
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * The time in epoch where view was created
     *
     * @ORM\Column(name="view_time", type="integer", options={"unsigned"=true})
     */
    protected $viewTime;

    /**
     * @ORM\Column(name="clicked", type="boolean")
     */
    protected $clicked;

    /**
     * @ORM\Column(name="ads_referrer", type="string", length=255)
     */
    protected $adsReferrer;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Ads", inversedBy="adsView")
     */
    protected $ads;

    //=================================================================================

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param Ads $ads
     */
    public function setAds(Ads $ads)
    {
        $this->ads = $ads;
    }

    /**
     * @param mixed $viewTime
     */
    public function setViewTime($viewTime)
    {
        $this->viewTime = $viewTime;
    }

    /**
     * @return mixed
     */
    public function getViewTime()
    {
        return $this->viewTime;
    }

    /**
     * @param mixed $clicked
     */
    public function setClicked($clicked)
    {
        $this->clicked = $clicked;
    }

    /**
     * @return mixed
     */
    public function getClicked()
    {
        return $this->clicked;
    }

    /**
     * @param mixed $adsReferrer
     */
    public function setAdsReferrer($adsReferrer)
    {
        $this->adsReferrer = $adsReferrer;
    }

    /**
     * @return mixed
     */
    public function getAdsReferrer()
    {
        return $this->adsReferrer;
    }



}