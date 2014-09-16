<?php
namespace VigattinAds\DomainModel;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use VigattinAds\DomainModel\AbstractEntity;
use VigattinAds\DomainModel\Ads;
use VigattinAds\DomainModel\VauthAccountLocator;

/**
 * @ORM\Entity
 * @ORM\Table(name="ads_user", uniqueConstraints={@ORM\UniqueConstraint(name="unique_email", columns={"email"}), @ORM\UniqueConstraint(name="unique_username", columns={"username"})}, indexes={@ORM\Index(name="search_index", columns={"pass_hash"})})
 */
class AdsUser extends AbstractEntity
{
    const PERMIT_ALL = 's';
    const PERMIT_BASIC_ACCESS = 'b';
    const PERMIT_TO_APPROVE_ADS = 'p';
    const PERMIT_ADMIN_ACCESS = 'a';
    const PERMIT_VIEW_ADMIN = 'v';

    /**
     * Table primary key
     *
     * @var integer
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * User email
     *
     * @var string
     * @ORM\Column(name="email", type="string", length=255)
     */
    protected $email;

    /**
     * @var string
     * @ORM\Column(name="username", type="string", length=255)
     */
    protected $username;

    /**
     * @var string
     * @ORM\Column(name="pass_hash", type="string", length=255)
     */
    protected $passHash;

    /**
     * @var string
     * @ORM\Column(name="pass_salt", type="string", length=255)
     */
    protected $passSalt;

    /**
     * @var string
     * @ORM\Column(name="first_name", type="string", length=255)
     */
    protected $firstName;

    /**
     * @var string
     * @ORM\Column(name="last_name", type="string", length=255)
     */
    protected $lastName;

    /**
     * @var bool
     * @ORM\Column(name="verified", type="boolean")
     */
    protected $verified = false;

    /**
     * @var string
     * @ORM\Column(name="privilege", type="string", length=255)
     */
    protected $privilege = 'b';

    /**
     * @var int
     * @ORM\Column(name="credit", type="float")
     */
    protected $credit = 0;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="VigattinAds\DomainModel\Ads", mappedBy="adsUser")
     */
    protected $ads = null;

    /**
     * @var bool
     * @ORM\Column(name="deleted", type="boolean")
     */
    protected $deleted = false;

    //==================================================================================================

    public function __construct()
    {
        $this->ads = new ArrayCollection();
    }

    /**
     * Get property value
     * @param string $propertyName List of properties are 1d, email, username, passHash, passSalt, firstName, lastName, verified, privilege and ads.
     * @return mixed
     */
    public function get($propertyName)
    {
        return parent::get($propertyName);
    }

    /**
     * Set property value
     * @param string $propertyName List of properties are email, username, passHash, passSalt, firstName, lastName, verified and privilege.
     * @param mixed $value
     * @return Ads
     */
    public function set($propertyName, $value)
    {
        if($propertyName == 'ads') return $this;
        return parent::set($propertyName, $value);
    }

    /**
     * Get single ads of the user using ads database primary key
     * @param $adsId ads primary key
     * @return \VigattinAds\DomainModel\Ads;
     */
    public function getSingleAds($adsId)
    {
        $criteria = Criteria::create()->where(Criteria::expr()->eq('id', $adsId));
        $ads = $this->ads->matching($criteria);
        return $ads->first();
    }

    /**
     * Search the ads array collection object by using criteria object
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getApprovedAds()
    {
        $criteria = Criteria::create()->where(Criteria::expr()->eq('status', Ads::STATUS_APPROVED));
        $ads = $this->ads->matching($criteria);
        return $ads;
    }

    /**
     * Search the ads array collection object by using criteria object
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPendingAds()
    {
        $criteria = Criteria::create()->where(Criteria::expr()->eq('status', Ads::STATUS_PENDING));
        $ads = $this->ads->matching($criteria);
        return $ads;
    }

    /**
     * Search the ads array collection object by using criteria object
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getDisapprovedAds()
    {
        $criteria = Criteria::create()->where(Criteria::expr()->eq('status', Ads::STATUS_DISAPPROVED));
        $ads = $this->ads->matching($criteria);
        return $ads;
    }

    /**
     * Create own ads
     * @param $adsTitle
     * @param $adsUrl
     * @param $adsImage
     * @param $adsDescription
     * @param $showIn
     * @param $template
     * @param string $keyword
     * @return Ads
     */
    public function createAds($adsTitle, $adsUrl, $adsImage, $adsDescription, $showIn, $template, $keyword = '', $adsPrice = 0, $category = '', $date = '')
    {
        $date = strtotime($date) ? $date : '';
        $ads = new Ads($this);
        $ads->set('adsTitle', $adsTitle)
            ->set('adsUrl', $adsUrl)
            ->set('adsImage', $adsImage)
            ->set('adsDescription', $adsDescription)
            ->set('showIn', $showIn)
            ->set('template', $template)
            ->set('keywords', $keyword)
            ->set('adsPrice', $adsPrice)
            ->set('status', Ads::STATUS_DRAFT)
            ->set('category', $category)
            ->set('reviewVersion', uniqid())
            ->set('userUsername', $this->get('username'))
            ->set('userEmail', $this->get('email'))
            ->set('userFirstName', $this->get('firstName'))
            ->set('userLastName', $this->get('lastName'))
            ->set('createdTime', time())
            ->set('date', $date);
        $this->ads->add($ads);
        $this->entityManager->persist($ads);
        return $ads;
    }

    /**
     * Update user info in ads, used for search optimize
     */
    public function updateAdsSearch()
    {
        $query = $this->entityManager->createQuery("UPDATE VigattinAds\DomainModel\Ads a SET a.userUsername = :username, a.userEmail = :email, a.userFirstName = :firstName, a.userLastName = :lastName WHERE a.adsUser = :id");
        $query->setParameter('username', $this->get('username'));
        $query->setParameter('email', $this->get('email'));
        $query->setParameter('firstName', $this->get('firstName'));
        $query->setParameter('lastName', $this->get('lastName'));
        $query->setParameter('id', $this->get('id'));
        $query->execute();
    }

    /**
     * Check if user has privilege to access site functionality
     * @param $permit Permit constant
     * @return bool
     */
    public function hasPermit($permit)
    {
        if(strpos($this->privilege, $permit) === false) return false;
        return true;
    }

    /**
     * Get Vauth ID from database or cache
     * @return int vauth ID
     */
    public function getVauthId()
    {
        // from cache
        $cacheKey = md5('vauthId_'.$this->id);
        $vauthId = $this->serviceManager->get('VigattinAds\DomainModel\LongCache')->getItem($cacheKey);
        if($vauthId) return $vauthId;

        // from database
        $vauthAccountLocator = $this->serviceManager->get('VigattinAds\DomainModel\UserManager')->getVauthAccountLocator($this->id);
        if($vauthAccountLocator instanceof VauthAccountLocator) {
            $vauthId = $vauthAccountLocator->get('vauthId');
        }
        else $vauthId = 0;
        $this->serviceManager->get('VigattinAds\DomainModel\LongCache')->addItem($cacheKey, $vauthId);
        return $vauthId;
    }

}