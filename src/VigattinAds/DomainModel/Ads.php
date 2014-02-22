<?php
namespace VigattinAds\DomainModel;

use Doctrine\ORM\Mapping as ORM;
use VigattinAds\DomainModel\AdsUser;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use VigattinAds\DomainModel\AbstractEntity;
use VigattinAds\DomainModel\AdsView;

/**
 * @ORM\Entity
 * @ORM\Table(name="ads", indexes={@ORM\Index(name="search_index", columns={"ads_title", "show_in", "template", "keywords", "status"}), @ORM\Index(name="version_index", columns={"review_version"})})
 */
class Ads extends AbstractEntity
{

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
     * @ORM\Column(name="ads_title", type="string", length=255)
     */
    protected $adsTitle;

    /**
     * Back link of the ads (full URL to ads site).
     * @ORM\Column(name="ads_url", type="string", length=255)
     */
    protected $adsUrl;

    /**
     * @ORM\Column(name="ads_description", type="text")
     */
    protected $adsDescription;

    /**
     * @var integer
     * @ORM\Column(name="ads_price", type="float")
     */
    protected $adsPrice = 0;

    /**
     * Web site for the ads to show
     * @var string
     * @ORM\Column(name="show_in", type="string", length=255)
     */
    protected $showIn;
    
    /**
     * Position in the site where the ads will be placed.
     * @var string
     * @ORM\Column(name="template", type="string", length=255)
     */
    protected $template;

    /**
     * Keywords for the ads to show
     * @var string
     * @ORM\Column(name="keywords", type="string", length=512)
     */
    protected $keywords;

    /**
     * @var string
     * @ORM\Column(name="ads_image", type="string", length=255)
     */
    protected $adsImage;
    
    /**
     * Ads status can be 0 = pending, 1 = approved or -1 = disapproved.
     * @var integer
     * @ORM\Column(name="status", type="smallint")
     */
    protected $status = 0;

    /**
     * Used only when ads under review
     * @var String
     * @ORM\Column(name="review_version", type="string", length=255)
     */
    protected $reviewVersion = '0';

    /**
     * @var int
     * @ORM\Column(name="view_limit", type="integer", options={"unsigned"=true});
     */
    protected $viewLimit = 0;

    /**
     * @ORM\ManyToOne(targetEntity="VigattinAds\DomainModel\AdsUser", inversedBy="ads")
     */
    protected $adsUser;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="VigattinAds\DomainModel\AdsView", mappedBy="ads")
     */
    protected $adsView;

    /**
     * @var bool
     * @ORM\Column(name="deleted", type="boolean")
     */
    protected $deleted = false;

    /**
     * Category this ads belong
     * @var string
     * @ORM\Column(name="category", type="string", length=512)
     */
    protected $category = '';

    //==================================================================================================

    public function __construct(AdsUser $adsUser)
    {
        $this->adsUser = $adsUser;
        $this->adsView = new ArrayCollection();
    }

    /**
     * Get property value
     * @param string $propertyName List of properties are id, adsTitle, adsUrl, adsDescription, adsPrice, showIn, template, keywords, adsImage, status, adsUser, adsView, reviewVersion, category and ServiceManager.
     * @return mixed
     */
    public function get($propertyName)
    {
        $result = parent::get($propertyName);
        return $result;
    }

    /**
     * Set property value
     * @param string $propertyName List of properties are adsTitle, adsUrl, adsDescription, adsPrice, showIn, template, keywords, adsImage, status, reviewVersion, category and ServiceManager.
     * @param mixed $value
     * @return AdsUser
     */
    public function set($propertyName, $value)
    {
        if($propertyName == 'adsUser') return $this;
        if($propertyName == 'adsView') return $this;
        if($propertyName == 'adsPrice') $value = floatval($value);
        return parent::set($propertyName, $value);
    }

    /**
     * @param $adsReferrer
     * @param $browserId
     * @param bool $isClicked
     * @return AdsView
     */
    public function addView($adsReferrer, $browserId, $isClicked = false)
    {
        $adsView = new AdsView($this);
        $adsView->set('serviceManager', $this->serviceManager);
        $adsView->set('viewTime', time())
            ->set('adsReferrer', $adsReferrer)
            ->set('browserId', $browserId)
            ->set('clicked', $isClicked);
        $this->adsView->add($adsView);
        return $adsView;
    }

    /**
     * Delete this ads from database
     */
    public function deleteSelf()
    {
        $this->deleteAllLogs();
        $this->deleteAllViews();
        $this->entityManager->remove($this);
    }

    /**
     * Delete all views from database
     */
    public function deleteAllViews()
    {
        $query = $this->entityManager->createQuery("DELETE VigattinAds\DomainModel\AdsView v WHERE v.ads = :adsId");
        $query->setParameter('adsId', $this->id);
        $query->execute();
    }

    /**
     * Delete all logs from database
     */
    public function deleteAllLogs()
    {
        $query = $this->entityManager->createQuery("DELETE VigattinAds\DomainModel\AdsApproveLog l WHERE l.ads = :adsId");
        $query->setParameter('adsId', $this->id);
        $query->execute();
    }

}