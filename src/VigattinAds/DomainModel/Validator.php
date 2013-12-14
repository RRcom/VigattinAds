<?php
namespace VigattinAds\DomainModel;

use Zend\Validator\Regex;
use Zend\Validator\EmailAddress;
use Zend\Validator\StringLength;
use Zend\Validator\Digits;
use VigattinAds\DomainModel\Image;

class Validator
{
    static public function isDigitValid($digit)
    {
        $digitValidator = new Digits();
        if(!$digitValidator->isValid($digitValidator)) return 'Not a digit number';
        return '';
    }

    static public function isEmailValid($emailAddress)
    {
        $emailValidator = new EmailAddress();
        if(!$emailValidator->isValid($emailAddress)) return 'Invalid email address';
        return '';
    }

    static public function isUsernameValid($name, $min = 4, $max = 48)
    {
        $regex = new Regex(array('pattern' => '#^[a-zA-Z0-9_-][a-zA-Z0-9_]+$#'));
        $strlength = new StringLength();
        $strlength->setMin($min);
        $strlength->setMax($max);
        if(!$regex->isValid($name)) return 'Invalid username! Must be alphanumeric and underscore';
        if(!$strlength->isValid($name)) return 'Username must be minimum of '.$min.' and maximum of '.$max.' character';
        return '';
    }

    static public function isNameValid($name, $min = 6, $max = 48)
    {
        $regex = new Regex(array('pattern' => '#^[a-zA-Z0-9_-][a-zA-Z0-9_\s-]+$#'));
        $strlength = new StringLength();
        $strlength->setMin($min);
        $strlength->setMax($max);
        if(!$regex->isValid($name)) return 'Invalid name! Must be alphanumeric, underscore or dashes';
        if(!$strlength->isValid($name)) return 'Name must be minimum of '.$min.' and maximum of '.$max.' character';
        return '';
    }

    static public function isTitleValid($name, $min = 6, $max = 48)
    {
        $pattern = '#^[a-zA-Z0-9_-\s.\'"/,&()]*$#';
        $regex = new Regex(array('pattern' => $pattern));
        $strlength = new StringLength();
        $strlength->setMin($min);
        $strlength->setMax($max);
        if(!$regex->isValid($name)) return 'Title has invalid character';
        if(!$strlength->isValid($name)) return 'Title must be minimum of '.$min.' and maximum of '.$max.' character';
        return '';
    }

    static public function isKeywordValid($text, $min = 0, $max = 48)
    {
        $regex = new Regex(array('pattern' => '#^[a-zA-Z0-9\s\,]*$#'));
        $strlength = new StringLength();
        $strlength->setMin($min);
        $strlength->setMax($max);
        if(!$regex->isValid($text)) return 'Keyword has invalid character';
        if(!$strlength->isValid($text)) return 'Keyword must be minimum of '.$min.' and maximum of '.$max.' character';
        return '';
    }

    static public function isDescriptionValid($text, $min = 0, $max = 160)
    {
        $pattern = '#^[a-zA-Z0-9_-\s.\'"/,&()]*$#';
        $regex = new Regex(array('pattern' => $pattern));
        $strlength = new StringLength();
        $strlength->setMin($min);
        $strlength->setMax($max);
        if(!$regex->isValid($text)) return 'Description has invalid character';
        if(!$strlength->isValid($text)) return 'Description must be minimum of '.$min.' and maximum of '.$max.' character';
        return '';
    }

    static public function isUrlValid($url, $min = 0, $max = 256)
    {
        $strlength = new StringLength();
        $strlength->setMin($min);
        $strlength->setMax($max);
        if(!filter_var($url, FILTER_VALIDATE_URL)) return 'The link you provided is not a valid url';
        if(!$strlength->isValid($url)) return 'Url must be maximum of '.$max.' character';
        return '';
    }

    static public function isDomainValid($domain)
    {

    }

    static public function isImage($imageFile)
    {
        $maxSize = 10000000;
        $size = $imageFile['size'];
        if($size > $maxSize) return 'Image max size limit exceed. Must be 10mb or lower';
        $image = new Image();
        if(!is_resource($image->create_source_from_img($imageFile['tmp_name']))) return 'Invalid image or empty image. Valid types are jpeg, gif, png and bitmap';
        return '';
    }
}