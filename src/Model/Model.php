<?php
namespace app\Model;

use App\Core\Inflector;
use App\Core\Db;
use App\Database\Connection;
use App\Database\Query;

class Model {
    public $table;
    
    protected $_driver = null;


    public function __construct($options)
	{
        $this->_driver = Connection::get();
    }
    
    public function fetch($type = 'all', $options = [])
    {
        $query = $this->query();
        if ($type == 'byid' and isset($options['value'])) {
            $query->where(['id' => $options['value']]);
            unset($options['where'], $options['value']);
            $type = 'first';
        }
        foreach ($options as $option => $values) {
            $query->{$option}($values);
        }
        if ($type == 'first') {
            return $query->first();
        }
        return $query->all();
    }

    public function getAlias()
    {
        $class = get_class($this);
        $pos = strrpos($class, '\\');
        return ($pos === false ? $class : substr($class, $pos + 1));
    }
    
    public function getConnection()
    {
        return $this->_driver->getConnection();
    }
    
    public function count($where = [])
	{
        return $this->query()
                ->where($where)
                ->select('COUNT(*) AS `count`')
                ->first()['count'];
    }
    
    protected function query($table = null)
    {
        if ($table === null) {
            $table = Inflector::tableize($this->getAlias());
        }
        return new Query($this, $table);
    }


    public function create($data)
	{
        return $this->query()
                ->insert($data)
                ->execute()
                ->rowCount();
    }
    
    public function update($data)
    {
        $this->query()
                ->update()
                ->set($data)
                ->where(['id' => $data['id']])
                ->execute()
                ->rowCount();
        return true;
    }
    
    public function validate($fields)
	{
        return [];
    }
}