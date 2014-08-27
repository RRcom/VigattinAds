<?php
namespace VigattinAds\DomainModel\AdsCategory;

class TradeAdsCategoryProvider extends GenericAdsCategoryProvider
{
    protected $categories = array(
        array('title' => 'Left Side',  'categories' => array(
            array('keyword' => '(homepage)',    'title' => 'All Category',  'previewLink' => 'http://vigattin.com#preview'),
            array('keyword' => '(homepage general category)',    'title' => 'General Category',  'previewLink' => 'http://vigattin.com#preview'),
            array('keyword' => '(homepage general category playthings and collectibles)',    'title' => 'Playthings And Collectibles',  'previewLink' => 'http://vigattin.com#preview'),
        )),
        array('title' => 'Featured Ads',  'categories' => array(
            array('keyword' => '(featured ads vehicles cars, sedans & minivans)',    'title' => 'Cars and Sedans',  'previewLink' => 'http://vigattin.com#preview'),
        )),
        array('title' => 'Ads Listing',  'categories' => array(
            array('keyword' => '(ads listing homepage)',    'title' => 'All Category',  'previewLink' => 'http://vigattin.com#preview'),
            array('keyword' => '(ads listing homepage general category)',    'title' => 'General Category',  'previewLink' => 'http://vigattin.com#preview'),
            array('keyword' => '(ads listing homepage general category playthings and collectibles)',    'title' => 'Playthings And Collectibles',  'previewLink' => 'http://vigattin.com#preview'),
        )),
    );
}