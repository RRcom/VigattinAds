<?php
namespace VigattinAds\DomainModel\Tourism;

class Author
{
    /** @var int */
    protected $id;
    /** @var string */
    protected $firstName;
    /** @var string */
    protected $lastName;
    /** @var string */
    protected $photoUrl;

    /**
     * @param int $id
     * @param string $firstName
     * @param string $lastName
     */
    function __construct($id, $firstName, $lastName, $photoUrl)
    {
        $this->firstName = $firstName;
        $this->id = $id;
        $this->lastName = $lastName;
        $this->photoUrl = $photoUrl;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getPhotoUrl()
    {
        return $this->photoUrl;
    }
}