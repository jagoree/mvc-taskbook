<?php

namespace App\Database;

class Connection
{

    protected static $connection = null;
    private static $mapConnections = [
        'default' => 'Mysql'
    ];
    private static $config = null;

    public static function get($name)
    {
        if (static::$config === null) {
            static::setConfig();
        }
        if (static::$connection !== null) {
            return static::$connection;
        }
        try {
            if (empty(static::$config[$name]['className']) and ! isset(static::$mapConnections[$name])) {
                throw new Exception(sprintf('Connection adapter for type %s not found', $name));
            }
            $className = static::$config[$name]['className'];
            static::$connection = new $className(static::$config[$name]);
        } catch (\RuntimeException $ex) {
            throw new \RuntimeException($ex->getMessage());
        }
        return static::$connection;
    }
    
    public static function reset()
    {
        static::$connection = null;
    }
    
    public static function setConfig()
    {
        try {
            static::$config = include APP . 'src' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'db.php';
        } catch (\RuntimeException $ex) {
            die('Could not include database configuration file');
        }
    }
}
