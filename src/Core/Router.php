<?php

namespace App\Core;

/**
 * Parses the requested URL into controller, action and parameters. Lanches application
 * 
 * @author jagoree
 */
class Router
{

    /**
     * Launches the application
     * 
     * @throws \RuntimeException
     */
    public static function launche()
    {
        session_start();
        $config = static::parseRequest();
        try {
            $className = 'App\Controller\\' . $config['controller'] . 'Controller';
            if (!class_exists($className)) {
                throw new \RuntimeException(sprintf('Controller class %s not found', $className));
            }
            $controller = new $className($config);
            $action = $config['action'];
            if (!method_exists($controller, $config['action'])) {
                throw new \RuntimeException(sprintf('Controller action %s not found', $config['action']));
            }
            [$controller, $config['action']]($config['id'] ?? null);
        } catch (\Exception $ex) {
            printf("<pre>%s\n%s</pre>", $ex->getMessage(), $ex->getTraceAsString());
        }
    }

    /**
     * Parses the requested URL into controller, action and parameters.
     * 
     * @return array
     */
    public static function parseRequest()
    {
        $components = parse_url($_SERVER['REQUEST_URI']);
        $path_array = explode('/', $components['path']);
        $config = [
            'controller' => 'Tasks',
            'action' => 'index'
        ];
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
        return $config;
    }

    /**
     * Checks if requested method is POST
     * 
     * @return (bool)
     */
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
