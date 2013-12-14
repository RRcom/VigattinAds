<?php
namespace VigattinAds\DomainModel;

use Zend\ServiceManager\ServiceManager;
use VigattinAds\DomainModel\AdsUser;
use VigattinAds\DomainModel\Validator;
use Zend\Crypt\Password\Bcrypt;
use Zend\Math\Rand;

class UserManager
{

    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @var \Zend\Session\SessionManager
     */
    protected $sessionManager;

    /**
     * @var \VigattinAds\DomainModel\AdsUser
     */
    protected $user;

    public function __construct(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        $this->entityManager = $this->serviceManager->get('Doctrine\ORM\EntityManager');
        $this->sessionManager = $this->serviceManager->get('Zend\Session\SessionManager');
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
        if(!Validator::isEmailValid($user))
        {
            $query = $this->entityManager->createQuery("SELECT u FROM VigattinAds\DomainModel\AdsUser u WHERE u.email = :user");
        }
        elseif(!Validator::isDigitValid($user))
        {
            $query = $this->entityManager->createQuery("SELECT u FROM VigattinAds\DomainModel\AdsUser u WHERE u.id = :user");
        }
        elseif(!Validator::isUsernameValid($user))
        {
            $query = $this->entityManager->createQuery("SELECT u FROM VigattinAds\DomainModel\AdsUser u WHERE u.username = :user");
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
            /** @var $result \VigattinAds\DomainModel\AdsUser */
            $result = $query->getSingleResult();
        }catch(NoResultException $ex)
        {
            return array
            (
                'status' => 'error',
                'reason' => 'not a register user '.$user,
            );
        }
        if($this->checkPassword($result->get('passHash'), $password, $result->get('passSalt'))) {
            $this->user = $result;
            $this->user->set('serviceManager', null);
            $this->entityManager->detach($this->user);
            $this->sessionManager->getStorage()->user = $this->user;
            $this->user = $this->getEntityFromSession();
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
     * @return bool
     */
    public function isLogin()
    {
        if($this->user instanceof AdsUser) return true;
        return false;
    }

    /**
     * Destroy user session
     */
    public function logout()
    {
        $this->user = null;
        $this->sessionManager->getStorage()->user = null;
    }

    /**
     * @return \VigattinAds\DomainModel\AdsUser
     */
    public function getCurrentUser()
    {
        return $this->user;
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
        if(Validator::isEmailValid($email)) $error[] = 'invalid email';
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
        return $user;
    }

    /**
     * Flush to database all insert to view
     */
    public function flush()
    {
        $this->entityManager->flush();
    }

    /**
     * Generate random salt
     * @param $password string
     * @return string
     */
    protected function makePassSalt($password)
    {
        return md5(Rand::getBytes(strlen($password), true));
    }

    /**
     * Generate secure password
     * @param $password string
     * @param $salt string
     * @return string
     */
    protected function createPassHash($password, $salt)
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
     * Reinitialize serialized entity from session
     * @return object active entity
     */
    protected function getEntityFromSession()
    {
        $user = $this->sessionManager->getStorage()->user;
        $user = $this->entityManager->merge($user);
        $user->set('serviceManager', $this->serviceManager);
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