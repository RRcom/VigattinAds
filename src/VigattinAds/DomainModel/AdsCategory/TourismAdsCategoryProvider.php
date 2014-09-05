<?php
namespace VigattinAds\DomainModel\AdsCategory;

class TourismAdsCategoryProvider extends GenericAdsCategoryProvider
{
    protected $categories = array(
        array('keyword' => '(homepage)',        'title' => 'Homepage',      'previewLink' => 'http://vigattintourism.com#preview', 'group' => '', 'disable' => false),
        array('keyword' => '(destinations)',     'title' => 'Destinations',   'previewLink' => 'http://vigattintourism.com/tourism/destinations#preview', 'group' => '', 'disable' => false),
        array('keyword' => '(articles)',        'title' => 'Articles',      'previewLink' => 'http://vigattintourism.com/tourism/articles?page=1#preview', 'group' => '', 'disable' => false),
        array('keyword' => '(tourist spots)',   'title' => 'Tourist Spots', 'previewLink' => 'http://vigattintourism.com/tourism/tourist_spots#preview', 'group' => '', 'disable' => false),
        array('keyword' => '(discussion)',      'title' => 'Discussion',    'previewLink' => 'http://vigattintourism.com/tourism/discussion#preview', 'group' => '', 'disable' => false),
        array('keyword' => '(directory)',      'title' => 'Directory',    'previewLink' => 'http://vigattintourism.com/tourism/destinations/91/directory#preview', 'group' => '', 'disable' => false),
    );
}