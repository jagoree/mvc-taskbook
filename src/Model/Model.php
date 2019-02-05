<?php
namespace app\Model;

use App\Core\Inflector;
use App\Core\Db;

class Model {
    public $table;
	
    public $name;
    
    public function __construct($options)
	{
        $this->name = $options['name'];
        $this->table = Inflector::tableize($this->name);
    }
    
    public function find($type = 'all', $query = [])
	{
        $query += ['type' => $type];
        if ($type == 'byid' and isset($query['value'])) {
            $query['where'] = '`id` = ' . $query['value'];
            $query['limit'] = 1;
        }
        return (new Db())->read($this, $query);
    }
    
    public function count($where = null)
	{
        return (new Db())->read($this, ['fields' => ['COUNT(*) AS `count`'], 'type' => 'count', 'limit' => 1]);
    }
    
    public function create($data)
	{
        return (new Db())->create($this, $data);
    }
    
    public function update($data)
	{
        return (new Db())->update($this, $data);
    }
    
    public function validate($fields)
	{
        return [];
    }
}