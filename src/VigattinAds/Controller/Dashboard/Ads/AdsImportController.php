<?php
namespace VigattinAds\Controller\Dashboard\Ads;

use Zend\View\Model\JsonModel;

class AdsImportController extends AdsController
{

    public function indexAction()
    {
        $test = array(
            array(
                'title' => 'Test Title 1',
                'image' => 'http://vigattintourism.com/assets/article_main_photos/trending_article/1349248246OgBbZuJy.jpg',
                'description' => 'This is a sample ads description.',
                'url' => 'http://google.com.ph',
            ),
            array(
                'title' => 'Test Title 2',
                'image' => 'http://vigattintourism.com/assets/article_main_photos/trending_article/1349248246OgBbZuJy.jpg',
                'description' => 'This is a sample ads description.',
                'url' => 'http://google.com.ph',
            ),
            array(
                'title' => 'Test Title 3',
                'image' => 'http://vigattintourism.com/assets/article_main_photos/trending_article/1349248246OgBbZuJy.jpg',
                'description' => 'This is a sample ads description.',
                'url' => 'http://google.com.ph',
            ),
            array(
                'title' => 'Test Title 4',
                'image' => 'http://vigattintourism.com/assets/article_main_photos/trending_article/1349248246OgBbZuJy.jpg',
                'description' => 'This is a sample ads description.',
                'url' => 'http://google.com.ph',
            ),
            array(
                'title' => 'Test Title 5',
                'image' => 'http://vigattintourism.com/assets/article_main_photos/trending_article/1349248246OgBbZuJy.jpg',
                'description' => 'This is a sample ads description.',
                'url' => 'http://google.com.ph',
            ),
            array(
                'title' => 'Test Title 6',
                'image' => 'http://vigattintourism.com/assets/article_main_photos/trending_article/1349248246OgBbZuJy.jpg',
                'description' => 'This is a sample ads description.',
                'url' => 'http://google.com.ph',
            ),
            array(
                'title' => 'Test Title 7',
                'image' => 'http://vigattintourism.com/assets/article_main_photos/trending_article/1349248246OgBbZuJy.jpg',
                'description' => 'This is a sample ads description.',
                'url' => 'http://google.com.ph',
            ),
            array(
                'title' => 'Test Title 8',
                'image' => 'http://vigattintourism.com/assets/article_main_photos/trending_article/1349248246OgBbZuJy.jpg',
                'description' => 'This is a sample ads description.',
                'url' => 'http://google.com.ph',
            ),
            array(
                'title' => 'Test Title 9',
                'image' => 'http://vigattintourism.com/assets/article_main_photos/trending_article/1349248246OgBbZuJy.jpg',
                'description' => 'This is a sample ads description.',
                'url' => 'http://google.com.ph',
            ),
            array(
                'title' => 'Test Title 10',
                'image' => 'http://vigattintourism.com/assets/article_main_photos/trending_article/1349248246OgBbZuJy.jpg',
                'description' => 'This is a sample ads description.',
                'url' => 'http://google.com.ph',
            ),
            array(
                'title' => 'Test Title 11',
                'image' => 'http://vigattintourism.com/assets/article_main_photos/trending_article/1349248246OgBbZuJy.jpg',
                'description' => 'This is a sample ads description.',
                'url' => 'http://google.com.ph',
            ),
            array(
                'title' => 'Test Title 12',
                'image' => 'http://vigattintourism.com/assets/article_main_photos/trending_article/1349248246OgBbZuJy.jpg',
                'description' => 'This is a sample ads description.',
                'url' => 'http://google.com.ph',
            ),
            array(
                'title' => 'Test Title 13',
                'image' => 'http://vigattintourism.com/assets/article_main_photos/trending_article/1349248246OgBbZuJy.jpg',
                'description' => 'This is a sample ads description.',
                'url' => 'http://google.com.ph',
            ),
            array(
                'title' => 'Test Title 14',
                'image' => 'http://vigattintourism.com/assets/article_main_photos/trending_article/1349248246OgBbZuJy.jpg',
                'description' => 'This is a sample ads description.',
                'url' => 'http://google.com.ph',
            ),
            array(
                'title' => 'Test Title 15',
                'image' => 'http://vigattintourism.com/assets/article_main_photos/trending_article/1349248246OgBbZuJy.jpg',
                'description' => 'This is a sample ads description.',
                'url' => 'http://google.com.ph',
            ),
            array(
                'title' => 'Test Title 16',
                'image' => 'http://vigattintourism.com/assets/article_main_photos/trending_article/1349248246OgBbZuJy.jpg',
                'description' => 'This is a sample ads description.',
                'url' => 'http://google.com.ph',
            ),
            array(
                'title' => 'Test Title 17',
                'image' => 'http://vigattintourism.com/assets/article_main_photos/trending_article/1349248246OgBbZuJy.jpg',
                'description' => 'This is a sample ads description.',
                'url' => 'http://google.com.ph',
            ),
            array(
                'title' => 'Test Title 18',
                'image' => 'http://vigattintourism.com/assets/article_main_photos/trending_article/1349248246OgBbZuJy.jpg',
                'description' => 'This is a sample ads description.',
                'url' => 'http://google.com.ph',
            ),
            array(
                'title' => 'Test Title 19',
                'image' => 'http://vigattintourism.com/assets/article_main_photos/trending_article/1349248246OgBbZuJy.jpg',
                'description' => 'This is a sample ads description.',
                'url' => 'http://google.com.ph',
            ),
            array(
                'title' => 'Test Title 20',
                'image' => 'http://vigattintourism.com/assets/article_main_photos/trending_article/1349248246OgBbZuJy.jpg',
                'description' => 'This is a sample ads description.',
                'url' => 'http://google.com.ph',
            ),
        );

        $name = $this->params('name', '');
        $start = $this->params('start', 0);
        $limit = 3;
        $total = count($test);
        $next = $start + $limit;

        $list = array_slice($test, $start, $limit);

        $jsonModel = new JsonModel();
        $jsonModel->setVariable('list', $list);
        $jsonModel->setVariable('total', $total);
        $jsonModel->setVariable('limit', $limit);
        $jsonModel->setVariable('next', $next);
        return $jsonModel;
    }
}
