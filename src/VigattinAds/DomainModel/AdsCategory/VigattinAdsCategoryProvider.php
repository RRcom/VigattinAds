<?php
namespace VigattinAds\DomainModel\AdsCategory;

class VigattinAdsCategoryProvider extends GenericAdsCategoryProvider
{
    protected $categories = array(
        array('keyword' => '(homepage)',    'title' => 'Homepage',  'previewLink' => 'http://vigattin.com#preview', 'group' => '', 'disable' => false),
    );
}