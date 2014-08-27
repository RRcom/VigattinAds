<?php
namespace VigattinAds\DomainModel\Assets;

class AssetsGenerator
{
    const JS_NAME = 'global';
    const CSS_NAME = 'style';

    protected $script = array(
        'global',
        'tools',
        'all',
    );

    protected $css = array(
        'global',
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
        //$byte = file_put_contents($this->assetsDir.'css/'.self::CSS_NAME.'.css', $this->css());
        //echo "\nfile created ".$this->assetsDir.'css/'.self::CSS_NAME.'.css '.$byte.' byte';
    }

    protected function js()
    {
        $content = '';
        foreach($this->script as $script) {
            $content .= $this->readJs($script)."\n";
        }
        return $content;
    }

    protected function css()
    {
        $content = '';
        foreach($this->css as $css) {
            $content .= $this->readCss($css)."\n";
        }
        return $content;
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
