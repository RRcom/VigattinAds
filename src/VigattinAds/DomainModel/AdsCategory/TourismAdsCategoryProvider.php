<?php
namespace VigattinAds\DomainModel\AdsCategory;

class TourismAdsCategoryProvider extends GenericAdsCategoryProvider
{
    protected $categories = array(
        array('keyword' => '(homepage)',        'title' => 'Homepage',      'previewLink' => 'http://vigattintourism.com#preview', 'group' => ''),
        array('keyword' => '(destination)',     'title' => 'Destination',   'previewLink' => 'http://vigattintourism.com/tourism/destinations#preview', 'group' => ''),
        array('keyword' => '(articles)',        'title' => 'Articles',      'previewLink' => 'http://vigattintourism.com/tourism/articles?page=1#preview', 'group' => ''),
        array('keyword' => '(tourist spots)',   'title' => 'Tourist Spots', 'previewLink' => 'http://vigattintourism.com/tourism/tourist_spots#preview', 'group' => ''),
        array('keyword' => '(discussion)',      'title' => 'Discussion',    'previewLink' => 'http://vigattintourism.com/tourism/discussion#preview', 'group' => ''),
        array('keyword' => '(destinations)',    'title' => 'Destinations',  'previewLink' => 'http://vigattintourism.com/tourism/destinations/91/directory#preview', 'group' => ''),
    );
}