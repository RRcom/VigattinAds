<?php
namespace VigattinAds\DomainModel;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ads_settings", uniqueConstraints={@ORM\UniqueConstraint(name="unique_key", columns={"key"})})
 */
class Settings {

    /**
     * @ORM\Id
     * @ORM\Column(name="`id`", type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="`key`", type="string", length=255)
     */
    protected $key;

    /**
     * @ORM\Column(name="`value`", type="string", length=255)
     */
    protected $value;

    /**
     * @ORM\Column(name="`int_value`", type="integer")
     */
    protected $intValue = 0;

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    //==================================================================================================



}