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

    protected $script = array(
        'global',
        'tools',
        'all',
    );

    protected $css = array(
        'global',
        'default',
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
    }

    protected function js()
    {
        $content = '';
        foreach($this->script as $script) {
            $content .= $this->readJs($script)."\n";
        }
        return JSMin::minify($content);
    }

    protected function css()
    {
        $content = '';
        foreach($this->css as $css) {
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
