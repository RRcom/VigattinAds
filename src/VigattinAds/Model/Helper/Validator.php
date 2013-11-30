<?php
namespace VigattinAds\Model\Helper;
use Zend\Validator\Regex;
use Zend\Validator\EmailAddress;
use Zend\Validator\StringLength;

class Validator
{
    static public function isNameValid($name, $min = 6, $max = 32)
    {
        $regex = new Regex(array('pattern' => '#^[a-z-AZ0-9_-][a-zA-Z0-9_-\s]+$#'));
        $strlength = new StringLength();
        $strlength->setMin($min);
        $strlength->setMax($max);
        if(!$regex->isValid($name)) return 'Invalid name! Must be alphanumeric, underscore or dashes';
        if(!$strlength->isValid($name)) return 'Name must be minimum of '.$min.' and maximum of '.$max.' character';
        return '';
    }

    static public function isDescriptionValid($text, $min = 0, $max = 32)
    {
        $regex = new Regex(array('pattern' => '#^[a-zA-Z0-9_-\s\.]*$#'));
        $strlength = new StringLength();
        $strlength->setMin($min);
        $strlength->setMax($max);
        if(!$regex->isValid($text)) return 'Invalid description! Must be alphanumeric, underscore or dashes';
        if(!$strlength->isValid($text)) return 'Description must be minimum of '.$min.' and maximum of '.$max.' character';
        return '';
    }

    static public function isUrlValid($url)
    {
        if(!filter_var($url, FILTER_VALIDATE_URL)) return 'The link you provided is not a valid url';
        return '';
    }

    static public function isDomainValid($domain)
    {

    }
}