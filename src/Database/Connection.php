<?php
namespace App\Database;

class Connection
{
    protected static $connection = null;
    
    private static $mapConnections = [
        'default' => 'Mysql'
    ];
    
    public static function get($name = 'default') {
        $config = include APP . 'src' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'db.php';
        if (!static::$connection) {
            if (empty($config[$name]['className']) and !isset(static::$mapConnections[$name])) {
                throw new Exception(sprintf('Connection adapter for type %s not found', $name));
            }
            $className = $config[$name]['className'];
            static::$connection = new $className($config[$name]);
        }
        return static::$connection;
    }
}