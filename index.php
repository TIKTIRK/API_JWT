<?php
header("Content-Type: application/json; charset=UTF-8");
require 'db.php';
require 'News.php';
require 'NewsController.php';
require 'Authorization.php';
require 'config.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

$route= new Route();
$method = $_SERVER['REQUEST_METHOD'];
$controller = $_GET['controller'];
$action = $_GET['action'];

$route->add('GET','News','number',new NewsController(),'show');
$route->add('GET','News',NULL,new NewsController(),'showAll');
$route->add('POST','News','create',new NewsController(),'create');
$route->add('PATCH','News','update',new NewsController(),'update');
$route->add('DELETE','News','delete',new NewsController(),'delete');
$route->add('POST','login',NULL,new Authorization(),'log');
$route->add('POST','registration',NULL,new Authorization(),'reg');
$route->check($method, $controller, $action);

class Route {
    private $routes = [];

    public function add($method,$controller,$action, $class, $method_class){
        $this->routes[] = ['method' => $method, 'controller' => $controller, 'action' => $action, 'class' => $class, 'method_class' => $method_class];
    }

    public function check($method, $controller, $action){
        foreach ($this->routes as $route){
            if (($method == $route['method']) && ($controller==$route['controller']) && ($controller=='login' || $controller=='registration')){
                $controller = new $route['class'];
                return call_user_func_array([$controller, $route['method_class']],[$action]);
            }
            
            if (($method == $route['method']) && ($controller==$route['controller']) && ($action==$route['action'] || (is_numeric($action) ))){
                $controller = new $route['class'];
                $headers = apache_request_headers();
                if ($headers['Authorization']!="") {
                    $jwt=$headers['Authorization'];
                    try {
                        $decoded = JWT::decode($jwt, new Key(SECRET_KEY, 'HS256') );
                        echo json_encode([
                            "message" => "Доступ к защищенному ресурсу",
                            "user" => $decoded->data
                        ]);
                    }catch (Exception $e) {
                        echo 'Ошибка: ' . $e->getMessage();
                        exit;
                    }
                } else {
                    echo json_encode(["message" => "Токен не передан."]);
                    http_response_code(401);
                    exit;
                }
                return call_user_func_array([$controller, $route['method_class']],[$action]);
            }
        }
        http_response_code(404);
        echo json_encode(['error' => 'Wrong URI or METHOD']);
    }
}
/*$requestUri = explode('/', trim($_SERVER['REQUEST_URI'],'/'));
$class=$requestUri[0];
$controller= new $class();
$func=$requestUri[1];
$controller->$func($requestUri[2]);*/
?>