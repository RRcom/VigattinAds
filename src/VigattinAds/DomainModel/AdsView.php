<?php
namespace VigattinAds\DomainModel;

use Zend\ServiceManager\ServiceManager;
use Doctrine\ORM\Mapping as ORM;
use VigattinAds\DomainModel\Ads;

/**
 * @ORM\Entity
 * @ORM\Table(name="ads_view", indexes={@ORM\Index(name="search_index", columns={"view_time", "clicked", "browser_id"})})
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
     * @var string;
     * @ORM\Column(name="browser_id", type="string", length=255)
     */
    protected $browserId;

    /**
     * @var \VigattinAds\DomainModel\Ads
     * @ORM\ManyToOne(targetEntity="VigattinAds\DomainModel\Ads", inversedBy="adsView")
     */
    protected $ads;

    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    //=================================================================================

    public function __construct(Ads $ads)
    {
        $this->ads = $ads;
    }

    /**
     * Get property value
     * @param string $propertyName List of properties are id, viewName, clicked, adsReferrer, browserId and ads.
     * @return mixed
     */
    public function get($propertyName)
    {
        return $this->$propertyName;
    }

    /**
     * Set property value
     * @param string $propertyName List of properties are viewName, clicked, adsReferrer and browserId.
     * @param mixed $value
     * @return AdsView
     */
    public function set($propertyName, $value)
    {
        if($propertyName == 'id') return $this;
        $this->$propertyName = $value;
        return $this;
    }
}