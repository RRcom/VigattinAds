<?php
namespace VigattinAds\DomainModel\AdsCategory;

class TourismAdsCategoryProvider extends GenericAdsCategoryProvider
{
    protected $categories = array(
        array('keyword' => '(homepage)',        'title' => 'Homepage',      'previewLink' => 'http://vigattintourism.com#preview'),
        array('keyword' => '(destination)',     'title' => 'Destination',   'previewLink' => 'http://vigattintourism.com/tourism/destinations#preview'),
        array('keyword' => '(articles)',        'title' => 'Articles',      'previewLink' => 'http://vigattintourism.com/tourism/articles?page=1#preview'),
        array('keyword' => '(tourist spots)',   'title' => 'Tourist Spots', 'previewLink' => 'http://vigattintourism.com/tourism/tourist_spots#preview'),
        array('keyword' => '(discussion)',      'title' => 'Discussion',    'previewLink' => 'http://vigattintourism.com/tourism/discussion#preview'),
        array('keyword' => '(destinations)',    'title' => 'Destinations',  'previewLink' => 'http://vigattintourism.com/tourism/destinations/91/directory#preview'),
    );
}