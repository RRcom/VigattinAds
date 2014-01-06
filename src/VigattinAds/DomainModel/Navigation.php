<?php
namespace VigattinAds\DomainModel;

class Navigation
{
    static public function breadcrumbs()
    {
        $relative = static::currentUrl(true);
        $splited = explode('?', $relative);
        $splited = trim($splited[0], '/');
        $splited = explode('/', $splited);
        unset($splited[0]);
        unset($splited[1]);
        $breadCrumb = '<ol class="breadcrumb ol-no-padding"><li><a href="/vigattinads">Dashboard</a></li>';
        $route = '/vigattinads/dashboard';
        $navTotal = count($splited);
        foreach($splited as $nav)
        {
            $navTotal--;
            $route .= '/'.$nav;
            if(!$navTotal) $breadCrumb .= '<li class="active">'.ucfirst(strtolower($nav)).'</li>';
            else $breadCrumb .= '<li><a href="'.$route.'">'.ucfirst(strtolower($nav)).'</a></li>';
        }
        $breadCrumb .= '</ol>';
        return $breadCrumb;
    }

    static public function currentUrl($relativePath = false)
    {
        $pageURL = 'http';
        if(isset($_SERVER["HTTPS"]) && ($_SERVER["HTTPS"] == "on"))
        {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80")
        {
            if($relativePath) $pageURL = $_SERVER["REQUEST_URI"];
            else $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        }
        else
        {
            if($relativePath) $pageURL = $_SERVER["REQUEST_URI"];
            else $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }
}