<?php
/**
 * Created by PhpStorm.
 * User: Nzt200
 * Date: 18.09.2018
 * Time: 19:06
 */

namespace Nzt200\App\Components;

use Nzt200\App\Controller\ShopController;
class Router
{
    private static $routes = array();
    public function __construct()
    {
        require_once ROOT . '/config/routes.php';
    }

    public static function get($pattern,$path)
    {
        self::$routes[] = array('method'=>'GET','pattern'=>$pattern,'path'=>$path);
    }

    public static function post($pattern,$path)
    {
        self::$routes[] = array('method'=>'POST','pattern'=>$pattern,'path'=>$path);
    }

    public function getUrl()
    {
        $uri = trim($_SERVER['REQUEST_URI'],'/');
        return array(
            'method'=>$_SERVER['REQUEST_METHOD'],
            'uri'   => $uri,
        );
    }

    public function detectSubdomain()
    {
        $settings = require_once ROOT . '/config/config.php';
        if(preg_match("~^([a-z-0-9][a-z-0-9-]{3,20})(.)({$settings['app_url']})$~",$_SERVER['HTTP_HOST'])) {
            return true;
        } else {
            return false;
        }
    }

    public function getShop()
    {
        $shop = new ShopController();
        $segments = explode('.',$_SERVER['HTTP_HOST']);
        $subdomain = array_shift($segments);
        $shop->actionShow($subdomain);
    }
    public function run()
    {

        $url = $this->getUrl();

        if($this->detectSubdomain()) {

            self::getShop();
            die();
        }
        else {
            foreach(self::$routes as $route) {
                if(($url['method'] == $route['method']) && (preg_match("~^{$route['pattern']}$~", $url['uri']))) {
                    $path = $route['path'];
                    $segments = preg_replace("~^{$route['pattern']}$~",$path, $url['uri']);
                    $segments = explode('/',$segments);
                    $controllerName = ucfirst(array_shift($segments)) . 'Controller';
                    $actionName = 'action' . ucfirst(array_shift($segments));
                    $parametrs = $segments;
                    $controllerFile = 'Nzt200\App\Controller\\' . $controllerName;
                    $controller = new $controllerFile();
                    call_user_func_array(array($controller,$actionName),$parametrs);
                    die();
                }

            }
            $page = new \Nzt200\App\Controller\PageController();
            $page->actionNotFound();
        }

    }
}