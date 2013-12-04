<?php
namespace VigattinAds\Model\Helper;
use Zend\Validator\Regex;
use Zend\Validator\EmailAddress;
use Zend\Validator\StringLength;
use VigattinAds\Model\Helper\Image;

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

    static public function isTitleValid($name, $min = 6, $max = 32)
    {
        $regex = new Regex(array('pattern' => '#^[a-z-AZ0-9_-][a-zA-Z0-9_-\s]+$#'));
        $strlength = new StringLength();
        $strlength->setMin($min);
        $strlength->setMax($max);
        if(!$regex->isValid($name)) return 'Invalid Title! Must be alphanumeric, underscore or dashes';
        if(!$strlength->isValid($name)) return 'Title must be minimum of '.$min.' and maximum of '.$max.' character';
        return '';
    }

    static public function isKeywordValid($text, $min = 0, $max = 32)
    {
        $regex = new Regex(array('pattern' => '#^[a-zA-Z0-9\s\,]*$#'));
        $strlength = new StringLength();
        $strlength->setMin($min);
        $strlength->setMax($max);
        if(!$regex->isValid($text)) return 'Invalid keyword! Must be alphanumeric or spaces or comma';
        if(!$strlength->isValid($text)) return 'Keyword must be minimum of '.$min.' and maximum of '.$max.' character';
        return '';
    }

    static public function isDescriptionValid($text, $min = 0, $max = 320)
    {
        $regex = new Regex(array('pattern' => '#^[a-zA-Z0-9_-\s\.]*$#'));
        $strlength = new StringLength();
        $strlength->setMin($min);
        $strlength->setMax($max);
        if(!$regex->isValid($text)) return 'Invalid description! Must be alphanumeric or spaces or underscore or dashes';
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