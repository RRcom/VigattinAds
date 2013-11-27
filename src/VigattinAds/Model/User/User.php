<?php
namespace VigattinAds\Model\User;

use Zend\ServiceManager\ServiceManager;
use Zend\Validator\EmailAddress;
use Zend\Validator\Digits;
use Zend\Validator\Regex;
use Zend\Crypt\Password\Bcrypt;
use Zend\Math\Rand;
use Doctrine\ORM\NoResultException;
use VigattinAds\Entity\AdsUser as UserEntity;
use VigattinAds\Model\Ads\Ads;

class User
{
    const MIN_USERNAME_LENGTH = 4;
    const MAX_USERNAME_LENGTH = 32;
    const MIN_PASSWORD_LENGTH = 6;
    const MAX_PASSWORD_LENGTH = 32;
    const MIN_NAME_LENGTH = 2;
    const MAX_NAME_LENGTH = 64;

    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    /**
     * @var \Zend\Session\SessionManager
     */
    protected $sessionManager;

    /**
     * @var \VigattinAds\Entity\AdsUser
     */
    protected $user = null;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @var \Zend\Validator\EmailAddress
     */
    protected $emailValidator;

    /**
     * @var \Zend\Validator\Digits
     */
    protected $digitValidator;

    /**
     * @var \Zend\Validator\Regex
     */
    protected $alphaNumericValidator;

    /**
     * @var \Zend\Validator\Regex
     */
    protected $alphaValidator;

    /**
     * @var \VigattinAds\Model\Ads\Ads
     */
    protected $ads = null;

    /*----------- Public method -------------*/

    /**
     * @param ServiceManager $serviceManager
     */
    public function __construct(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        $this->entityManager = $this->serviceManager->get('Doctrine\ORM\EntityManager');
        $this->sessionManager = $this->serviceManager->get('Zend\Session\SessionManager');
        $this->emailValidator = new EmailAddress();
        $this->digitValidator = new Digits();
        $this->alphaNumericValidator = new Regex('#^[a-zA-Z][a-zA-Z0-9_]*#');
        $this->alphaValidator = new Regex('#^[a-zA-Z]*#');
        if($this->sessionManager->getStorage()->user) $this->user = $this->getEntityFromSession();
    }

    /**
     * Create new user session<br>
     * return array of result, if if success the value of status will be success and reason will be empty.
     * If login was failed the status will be error and reason will contain description of the failed login
     * <pre>
     * array(
     *  'status' => 'success' | 'error',
     *  'reason' => 'invalid user' | 'not a register user' | 'user/password combination do not match'
     * )
     * </pre>
     * @param $user string|integer Can be email username or id
     * @param $password string user password
     * @return array
     */
    public function login($user, $password)
    {
        if($this->emailValidator->isValid($user))
        {
            $query = $this->entityManager->createQuery("SELECT u FROM UserEntity u WHERE u.email = :user");
        }
        elseif($this->digitValidator->isValid($user))
        {
            $query = $this->entityManager->createQuery("SELECT u FROM UserEntity u WHERE u.id = :user");
        }
        elseif($this->alphaNumericValidator->isValid($user))
        {
            $query = $this->entityManager->createQuery("SELECT u FROM VigattinAds\Entity\AdsUser u WHERE u.username = :user");
        }
        else
        {
            return array
            (
                'status' => 'error',
                'reason' => 'invalid user '.$user,
            );
        }
        $query->setParameter('user', $user);
        try{
            $result = $query->getSingleResult();
        }catch(NoResultException $ex)
        {
            return array
            (
                'status' => 'error',
                'reason' => 'not a register user '.$user,
            );
        }
        if($this->checkPassword($result->getPassHash(), $password, $result->getPassSalt())) {
            $this->user = $result;
            $this->entityManager->detach($result);
            $this->sessionManager->getStorage()->user = $result;
            return array
            (
                'status' => 'success',
                'reason' => '',
            );
        }
        return array
        (
            'status' => 'error',
            'reason' => 'user/password combination do not match',
        );
    }

    /**
     * @return UserEntity
     */
    public function getUserEntity()
    {
        return $this->user;
    }

    /**
     * @return bool
     */
    public function isLogin()
    {
        if($this->user instanceof UserEntity) return true;
        return false;
    }

    /**
     * Create new user<br>
     * if an error occurs return an array of error string otherwise return an object of instanceof \VigattinAds\Entity\AdsUser<br>
     * sample error return<br><pre>
     * array(
     *  [0] => 'email already exist',
     *  [1] => 'invalid username must be a-z 0-9 and _ character only',
     *  [2] => 'invalid last name must be a-z character only',
     *  ....
     * )
     * @param $email string
     * @param $username string
     * @param $password string
     * @param $firstName string
     * @param $lastName string
     * @return object|array
     */
    public function createUser($email, $username, $password, $firstName, $lastName)
    {
        $password = strval($password);
        $error = array();
        if(!$this->emailValidator->isValid($email)) $error[] = 'invalid email';
        if($this->isTableExist('email', $email)) $error[] = 'email already exist';
        if(strlen($username) < self::MIN_USERNAME_LENGTH) $error[] = 'username must be minimum of '.self::MIN_USERNAME_LENGTH.' character';
        if(strlen($username) > self::MAX_USERNAME_LENGTH) $error[] = 'username to long max require is '.self::MAX_USERNAME_LENGTH.' character';
        if(!$this->alphaNumericValidator->isValid($username)) $error[] = 'invalid username must be a-z 0-9 and _ character only';
        if($this->isTableExist('username', $username)) $error[] = 'username already exist';
        if(strlen($password) < self::MIN_PASSWORD_LENGTH) $error[] = 'password must be minimum of '.self::MIN_PASSWORD_LENGTH.' character';
        if(strlen($password) > self::MAX_PASSWORD_LENGTH) $error[] = 'password to long max require is '.self::MAX_PASSWORD_LENGTH.' character';
        if(strlen($firstName) < self::MIN_NAME_LENGTH) $error[] = 'first name must be minimum of '.self::MIN_NAME_LENGTH.' character';
        if(strlen($firstName) > self::MAX_NAME_LENGTH) $error[] = 'first name to long max require is '.self::MAX_NAME_LENGTH.' character';
        if(strlen($lastName) < self::MIN_NAME_LENGTH) $error[] = 'last name must be minimum of '.self::MIN_NAME_LENGTH.' character';
        if(strlen($lastName) > self::MAX_NAME_LENGTH) $error[] = 'last name to long max require is '.self::MAX_NAME_LENGTH.' character';
        if(!$this->alphaValidator->isValid($firstName)) $error[] = 'invalid first name must be a-z character only';
        if(!$this->alphaValidator->isValid($lastName)) $error[] = 'invalid last name must be a-z character only';
        if(count($error)) return $error;
        $user = new UserEntity();
        $user->setEmail($email);
        $user->setUsername($username);
        $salt = $this->makePassSalt($password);
        $user->setPassSalt($salt);
        $user->setPassHash($this->createPassHash($password, $salt));
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $this->entityManager->persist($user);
        $this->entityManager->flush($user);
        return $user;
    }

    /**
     * Update database and session data
     * @param UserEntity $user
     */
    public function updateUser(UserEntity $userEntity) {
        $this->entityManager->persist($userEntity);
        $this->entityManager->flush($userEntity);
        $this->user = $userEntity;
        $this->entityManager->detach($userEntity);
        $this->sessionManager->getStorage()->user = $userEntity;
    }

    /**
     * Generate random salt
     * @param $password string
     * @return string
     */
    public function makePassSalt($password)
    {
        return md5(Rand::getBytes(strlen($password), true));
    }

    /**
     * Generate secure password
     * @param $password string
     * @param $salt string
     * @return string
     */
    public function createPassHash($password, $salt)
    {
        $bcrypt = new Bcrypt(
            array(
                'salt' => $salt,
                'cost' => 13,
            )
        );
        $hash = $bcrypt->create($password);
        return $hash;
    }

    /**
     * @return null|Ads
     */
    public function getAds()
    {
        if($this->ads instanceof Ads) return $this->ads;
        if($this->user instanceof UserEntity)
        {
            $ads = new Ads($this->serviceManager, $this->getUserEntity());
            $this->ads = $ads;
            return $this->ads;
        }
        return null;
    }

    /*----------- Protected method -------------*/

    /**
     * Reinitialize serialized entity from session
     * @return object active entity
     */
    protected function getEntityFromSession()
    {
        $user = $this->sessionManager->getStorage()->user;
        $user = $this->entityManager->merge($user);
        return $user;
    }

    /**
     * Check if a table exist
     * @param $columnName string
     * @param $value string|integer
     * @return bool
     */
    protected function isTableExist($columnName, $value)
    {
        $query = $this->entityManager->createQuery("SELECT u FROM VigattinAds\Entity\AdsUser u WHERE u.$columnName = :value");
        $query->setParameter(':value', $value);
        try{
            $query->getSingleResult();
        } catch(NoResultException $ex) {
            return false;
        }
        return true;
    }

    /**
     * Check if password and password hash match
     * @param $hash string
     * @param $password string
     * @param $salt string
     * @return bool
     */
    protected function checkPassword($hash, $password, $salt)
    {
        $bcrypt = new Bcrypt(
            array(
                'salt' => $salt,
                'cost' => 13,
            )
        );
        return $bcrypt->verify($password, $hash);
    }

}