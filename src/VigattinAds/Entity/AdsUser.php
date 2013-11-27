<?php
namespace VigattinAds\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use VigattinAds\Entity\Ads;

/**
 * @ORM\Entity
 * @ORM\Table(name="ads_user", uniqueConstraints={@ORM\UniqueConstraint(name="unique_email", columns={"email"}), @ORM\UniqueConstraint(name="unique_username", columns={"username"})}, indexes={@ORM\Index(name="search_index", columns={"pass_hash"})})
 */
class AdsUser {

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
     * @var arraycollection
     * @ORM\OneToMany(targetEntity="Ads", mappedBy="adsUser")
     */
    protected $ads = null;

    //==================================================================================================

    public function __construct()
    {
        $this->ads = new ArrayCollection();
    }

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
    public function addAds(Ads $ads)
    {
        $ads->setAdsUser($this);
        $this->ads[] = $ads;
    }

    /**
     * @return ArrayCollection
     */
    public function getAds()
    {
        return $this->ads;
    }

    /**
     * @param string $emil
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $passHash
     */
    public function setPassHash($passHash)
    {
        $this->passHash = $passHash;
    }

    /**
     * @return string
     */
    public function getPassHash()
    {
        return $this->passHash;
    }

    /**
     * @param string $passSalt
     */
    public function setPassSalt($passSalt)
    {
        $this->passSalt = $passSalt;
    }

    /**
     * @return string
     */
    public function getPassSalt()
    {
        return $this->passSalt;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param boolean $verified
     */
    public function setVerified($verified)
    {
        $this->verified = $verified;
    }

    /**
     * @return boolean
     */
    public function getVerified()
    {
        return $this->verified;
    }



}