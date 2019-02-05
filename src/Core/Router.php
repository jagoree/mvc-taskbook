<?php
namespace App\Core;

class Router {
    public static function launche()
    {
        $config = [
            'controller' => 'Tasks',
            'action' => 'index'
        ];
        $components = parse_url($_SERVER['REQUEST_URI']);
        $path_array = explode('/', $components['path']);
        if (!empty($path_array[1])) {
            $config['controller'] = Inflector::camelize(Inflector::pluralize($path_array[1]));
        }
        if ($config['controller'] == 'Users' and empty($path_array[2])) {
            $config['action'] = 'login';
        }
        if (!empty($path_array[2])) {
            $config['action'] = $path_array[2];
        }
        if (!empty($path_array[3])) {
            $config['id'] = $path_array[3];
        }
        try {
            $className = 'App\Controller\\' . $config['controller'];
            if (!class_exists($className)) {
                throw new \Exception(sprintf('Controller class %s not found', $className));
            }
            $controller = new $className($config);
        } catch (\Exception $ex) {
            echo $ex->getMessage();
        }
    }
    
    public static function isPost()
    {
        return strtolower($_SERVER['REQUEST_METHOD']) == 'post';
    }
    
    public static function getData($key)
    {
        if (isset($_POST[$key])) {
            return $_POST[$key];
        }
        return null;
    }
    
    public static function getQuery($key)
    {
        if (isset($_GET[$key])) {
            return $_GET[$key];
        }
        return null;
    }
}
