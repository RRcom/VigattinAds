<?php
namespace VigattinAds\DomainModel\Assets;

use VigattinAds\DomainModel\Minify\JSMin;
use VigattinAds\DomainModel\Minify\CSSmin;

/**
 * Use to generate single minify assets file. You need to use commandline interface to generate assets ex. php index.php vigattinads assets
 * Class AssetsGenerator
 * @package VigattinAds\DomainModel\Assets
 */
class AssetsGenerator
{
    const JS_NAME = 'global';
    const CSS_NAME = 'default';

    // use by service.vigattin.com
    protected $css = array(
        'global',
        'default',
    );

    // use by service.vigattin.com ads service
    protected $adsCss = array(
        'ads',
    );

    // use by service.vigattin.com
    protected $script = array(
        'global',
        'tools',
        'all',
        'tourismAuthor',
    );

    // use by service.vigattin.com ads service
    protected $adsJs = array(
        'ads',
    );

    // use by vigattintourism.com
    protected $tourismAdsJs = array(
        'tourismads',
    );

    // use by vigattintrade.com
    protected $tradeAdsJs = array(
        'tradeads',
    );

    // use by vigattin.com
    protected $vigattinAdsJs = array(
        'vigattinads',
    );

    protected $assetsDir;

    public function __construct()
    {
        $this->assetsDir = getcwd().'/public/assets/vigattinads/';
    }

    public function generate()
    {
        $byte = file_put_contents($this->assetsDir.'js/'.self::JS_NAME.'.js', $this->js());
        echo "\nfile created ".$this->assetsDir.'js/'.self::JS_NAME.'.js '.$byte.' byte';
        $byte = file_put_contents($this->assetsDir.'css/'.self::CSS_NAME.'.css', $this->css());
        echo "\nfile created ".$this->assetsDir.'css/'.self::CSS_NAME.'.css '.$byte.' byte';
        // view ads assets
        $byte = file_put_contents($this->assetsDir.'css/ads.css', $this->css($this->adsCss));
        echo "\nfile created ".$this->assetsDir.'css/ads.css '.$byte.' byte';
        $byte = file_put_contents($this->assetsDir.'js/ads.js', $this->js($this->adsJs));
        echo "\nfile created ".$this->assetsDir.'js/ads.js '.$byte.' byte';
        $byte = file_put_contents($this->assetsDir.'js/tourismads.js', $this->js($this->tourismAdsJs));
        echo "\nfile created ".$this->assetsDir.'js/tourism.js '.$byte.' byte';
        $byte = file_put_contents($this->assetsDir.'js/tradeads.js', $this->js($this->tradeAdsJs));
        echo "\nfile created ".$this->assetsDir.'js/tradeads.js '.$byte.' byte';
        $byte = file_put_contents($this->assetsDir.'js/vigattinads.js', $this->js($this->vigattinAdsJs));
        echo "\nfile created ".$this->assetsDir.'js/vigattinads.js '.$byte.' byte';

    }

    protected function js($filesArray = null)
    {
        if(!$filesArray) $filesArray = $this->script;
        $content = '';
        foreach($filesArray as $script) {
            $content .= $this->readJs($script)."\n";
        }
        return JSMin::minify($content);
    }

    protected function css($filesArray = null)
    {
        if(!$filesArray) $filesArray = $this->css;
        $content = '';
        foreach($filesArray as $css) {
            $content .= $this->readCss($css)."\n";
        }
        return CSSmin::process($content);
    }

    protected function readJs($fileName)
    {
        return file_get_contents(__DIR__.'/Script/'.$fileName.'.js');
    }

    protected function readCss($fileName)
    {
        return file_get_contents(__DIR__.'/Css/'.$fileName.'.css');
    }
}
