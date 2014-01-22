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
                'image' => 'https://www.google.com.ph/images/srpr/logo11w.png',
                'description' => 'This is a sample ads description.',
            ),
            array(
                'title' => 'Test Title 2',
                'image' => 'https://www.google.com.ph/images/srpr/logo11w.png',
                'description' => 'This is a sample ads description.',
            ),
            array(
                'title' => 'Test Title 3',
                'image' => 'https://www.google.com.ph/images/srpr/logo11w.png',
                'description' => 'This is a sample ads description.',
            ),
            array(
                'title' => 'Test Title 4',
                'image' => 'https://www.google.com.ph/images/srpr/logo11w.png',
                'description' => 'This is a sample ads description.',
            ),
            array(
                'title' => 'Test Title 5',
                'image' => 'https://www.google.com.ph/images/srpr/logo11w.png',
                'description' => 'This is a sample ads description.',
            ),
            array(
                'title' => 'Test Title 6',
                'image' => 'https://www.google.com.ph/images/srpr/logo11w.png',
                'description' => 'This is a sample ads description.',
            ),
            array(
                'title' => 'Test Title 7',
                'image' => 'https://www.google.com.ph/images/srpr/logo11w.png',
                'description' => 'This is a sample ads description.',
            ),
            array(
                'title' => 'Test Title 8',
                'image' => 'https://www.google.com.ph/images/srpr/logo11w.png',
                'description' => 'This is a sample ads description.',
            ),
            array(
                'title' => 'Test Title 9',
                'image' => 'https://www.google.com.ph/images/srpr/logo11w.png',
                'description' => 'This is a sample ads description.',
            ),
            array(
                'title' => 'Test Title 10',
                'image' => 'https://www.google.com.ph/images/srpr/logo11w.png',
                'description' => 'This is a sample ads description.',
            ),
        );

        $name = $this->params('name', '');
        $start = $this->params('start', 0);
        $limit = 5;
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
