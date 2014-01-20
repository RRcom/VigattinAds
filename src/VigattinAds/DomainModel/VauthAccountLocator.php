<?php
namespace VigattinAds\DomainModel;

use Doctrine\ORM\Mapping as ORM;
use VigattinAds\DomainModel\AbstractEntity;
use Doctrine\ORM\NoResultException;

/**
 * @ORM\Entity
 * @ORM\Table(name="vauth_account_locator", uniqueConstraints={@ORM\UniqueConstraint(name="unique_number", columns={"vauth_id", "ads_user_id"})})
 */
class VauthAccountLocator extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @var int
     * @ORM\Column(name="vauth_id", type="integer", options={"unsigned"=true});
     */
    protected $vauthId;

    /**
     * @var int
     * @ORM\Column(name="ads_user_id", type="integer", options={"unsigned"=true});
     */
    protected $adsUserId;

    //==================================================================================================

    public function hasAccount($vauthId)
    {
        $query = $this->entityManager->createQuery("SELECT v.adsUserId FROM VigattinAds\DomainModel\VauthAccountLocator v WHERE v.vauthId = :vauthId");
        $query->setParameter('vauthId', $vauthId);
        try {
            $result = $query->getSingleScalarResult();
        }
        catch(NoResultException $ex) {
            $result = null;
        }
        return $result;
    }

    public function addAccount($vauthId, $adsUserId)
    {
        $locator = new VauthAccountLocator();
        $locator->set('serviceManager', $this->serviceManager);
        $locator->set('vauthId', $vauthId);
        $locator->set('adsUserId', $adsUserId);
        $locator->persistSelf();
        $locator->flush();
        return $locator->get('id');
    }
}