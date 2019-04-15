<?php
namespace App\Core;

use PDO;
use PDOException;
use App\Model\Model;

class Db
{
    private $connection;
    
    public function __construct()
	{
        $config = include APP . 'src' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'db.php';
        try {
            $this->connection = new PDO('mysql:host=' . $config['host'] . ';port=' . $config['port'] . ';dbname=' . $config['db_name'], $config['user'], $config['password']);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            throw new \Exception($ex->getMessage());
        }
        return $this->connection;
    }
    
    public function read(Model $model, $query)
	{
        $type = $query['type'];
        $schema = [];
        foreach ($this->connection->query('DESCRIBE `' . $model->table . '`') as $column) {
            $schema[$column['Field']] = $column;
        }
        unset($query['type']);
        $query['table'] = '`' .$model->table . '`';
        if (empty($query['fields'])) {
            $query['fields'] = '*';
        } else {
            $query['fields'] = implode(', ', array_map(function ($field_name) use ($schema) {
                if (isset($schema[$field_name])) {
                    return '`' . $field_name . '`';
                }
                return $field_name;
            }, $query['fields']));
        }
        if (empty($query['alias'])) {
            $query['alias'] = $model->name;
        }
        if (!empty($query['where'])) {
            $query['where'] = ' WHERE ' . $query['where'];
        }
        if (!empty($query['offset'])) {
            $query['limit'] = ' LIMIT ' . $query['offset'] . ', ' . $query['limit'];
        } elseif (!empty($query['limit'])) {
            $query['limit'] = ' LIMIT ' . $query['limit'];
        }
        $query += [
            'joins' => '',
            'where' => '',
            'group' => '',
            'order' => '',
            'limit' => ''
        ];
        if ($statement = $this->connection->query($this->buildQuery('select', $query), PDO::FETCH_ASSOC)) {
            $result = [];
            foreach ($statement as $data) {
                $result[] = $data;
            }
            if ($type != 'all') {
                $result = current($result);
            }
            if ($type == 'count') {
                $result = $result['count'];
            }
            $statement->closeCursor();
            return $result;
        }
        throw new \Exception(sprintf('Query can\'t be executed. %s', implode("\n", $this->connection->errorInfo())));
    }
    
    public function create(Model $model, $data)
	{
        $schema = $values = [];
        $this->prepareData($data);
        foreach ($this->connection->query('DESCRIBE `' . $model->table . '`') as $column) {
            $schema[$column['Field']] = $column;
        }
        foreach ($data as $field => $value) {
            $key = ':' . $field;
            $fields[] = '`' . $field . '`';
            $values[$key] = $value;
        }
        if (!isset($data['created']) and isset($schema['created']) and in_array($schema['created']['Type'], ['date', 'datetime'])) {
            $fields[] = '`created`';
            $values[':created'] = date('Y-m-d H:i:s');
        }
        $query['table'] = $model->table;
        $query['fields'] = implode(', ', $fields);
        $query['values'] = implode(', ', array_keys($values));
        $sql = $this->buildQuery('create', $query);
        $statement = $this->connection->prepare($sql);
        return $statement->execute($values);
    }
    
    public function update(Model $model, $data)
	{
		$values = [];
        $this->prepareData($data);
        foreach ($this->connection->query('DESCRIBE `' . $model->table . '`') as $column) {
            $schema[$column['Field']] = $column;
        }
        foreach ($data as $field => $value) {
            if ($field == 'id') {
                continue;
            }
            $key = ':' . $field;
            $fields[] = '`' . $field . '` = ' . $key;
            $values[$key] = $value;
        }
        $query['table'] = '`' . $model->table . '`';
        $query['fields'] = implode(', ', $fields);
        if (isset($data['id'])) {
            $query['where'] = ' WHERE `id` = :where_id';
            $values[':where_id'] = $data['id'];
        }
        $sql = $this->buildQuery('update', $query);
        $statement = $this->connection->prepare($sql);
        $result = $statement->execute($values);
        return $result;
    }
    
    private function buildQuery($type, $data)
	{
        extract($data);
        switch ($type) {
            case 'select':
                return trim("SELECT {$fields} FROM {$table} {$alias} {$joins} {$where} {$group} {$order} {$limit}");
                break;
            case 'create':
                return "INSERT INTO {$table} ({$fields}) VALUES ({$values})";
                break;
            case 'update':
                return "UPDATE {$table} SET {$fields}{$where}";
                break;
        }
    }
    
    private function prepareData(&$data)
	{
        foreach ($data as &$value) {
            $value = trim(preg_replace('~\s{2,}~si', ' ', preg_replace('~<[^>]+>~si', ' ', $value)));
        }
    }
}