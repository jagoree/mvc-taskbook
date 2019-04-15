<?php

namespace App\Database;

/**
 * Description of Query
 *
 * @author zhentos
 */
class Query
{
    private $Model = null;
    
    private $table = null;
    
    private $sqlParts = [
        'select' => [],
        'from' => [],
        'update' => null,
        'set' => null,
        'insert' => null,
        'where' => null,
        'order' => null,
        'limit' => null
    ];
    
    private $sqlTemplate = [
        'select' => 'SELECT %s',
        'update' => 'UPDATE %s',
        'insert' => 'INSERT INTO %s',
        'set' => 'SET %s',
        'from' => 'FROM %s',
        'where' => 'WHERE %s',
        'order' => 'ORDER BY %s',
        'limit' => 'LIMIT %s'
    ];
    
    public function __construct($Model, $table)
    {
        $this->Model = $Model;
        $this->table = $table;
        $this->sqlParts['from'] = "`{$table}`";
        $this->select('*');
        return $this;
    }
    
    public function select($fields = [])
    {
        if (!is_array($fields)) {
            $fields = [$fields];
        }
        $fields = array_map(function ($field) {
            if ($field != '*' and !preg_match('~[\(\)]+|all|distinct|distinctrow~i', $field)) {
                return "`{$field}`";
            }
            return $field;
        }, $fields);
        $this->sqlParts['select'] = $fields;
        return $this;
    }
    
    public function where($expression = null)
    {
        $query = $and = $values = [];
        if (is_array($expression)) {
            $i = 0;
            foreach ($expression as $field => $value) {
                list($field, $operator) = explode(' ', trim($field) . ' ');
                if (empty($operator)) {
                    $operator = '=';
                }
                if (strtolower($operator) == 'in' or is_array($value)) {
                    $operator = 'IN';
                }
                $key = ':c' . $i;
                if (is_array($value)) {
                    $key = '(' . implode(',', array_map(function ($value) use (&$i, &$values) {
                        $key = ':c' . $i;
                        $values[$key] = (!is_numeric($value) ? "'{$value}'" : $value);
                        $i ++;
                        return $key;
                    }, $value)) . ')';
                } else {
                    $values[$key] = $value;
                }
                $and[] = implode(' ', ["`{$field}`", $operator, $key]);
                $i ++;
            }
            $query = implode(' AND ', $and);
        }
        $this->sqlParts['where'] = compact('query', 'values');
        return $this;
    }
    
    public function order($order = []) {
        $expr = [];
        foreach ($order as $key => $value) {
            if (is_numeric($key) or is_string($key) and !in_array(strtolower($value), ['asc', 'desc'])) {
                $key = $value;
                $value = 'ASC';
            }
            $expr[] = implode(' ', ["`{$key}`", $value]);
        }
        $this->sqlParts['order'] = implode(', ', $expr);
        return $this;
    }
    
    public function limit($limit = 1) {
        $this->sqlParts['limit'] = $limit;
        return $this;
    }
    
    public function offset($offset = 1)
    {
        $this->sqlParts['limit'] = implode(',', [$offset, $this->sqlParts['limit']]);
        return $this;
    }
    
    public function insert($data)
    {
        $this->sqlParts['from'] = $this->sqlParts['select'] = $placeholders = $values = $columns = [];
        foreach ($data as $key => $value) {
            $columns[] = "`{$key}`";
            $key = ":{$key}";
            $placeholders[] = $key;
            $values[$key] = $value;
        }
        $this->sqlParts['insert'] = ['query' => implode(' ', ["`{$this->table}`", '(' . implode(',', $columns) . ')', 'VALUES (' . implode(',', $placeholders) . ')']), 'values' => $values];
        return $this;
    }
    
    public function update($table = null)
    {
        $this->sqlParts['from'] = $this->sqlParts['select'] = [];
        if (!$table) {
            $table = $this->table;
        }
        $this->sqlParts['update'] = "`{$table}`";
        return $this;
    }
    
    public function set($data)
    {
        $placeholders = $values = $columns = [];
        foreach ($data as $key => $value) {
            $_key = ":{$key}";
            $vars[] = implode(' = ', ["`{$key}`", $_key]);
            $values[$_key] = $value;
        }
        $this->sqlParts['set'] = ['query' => implode(', ', $vars), 'values' => $values];
        return $this;
    }
    
    public function all()
    {
        return $this->execute()->fetchAll();
    }
    
    public function first()
    {
        $this->limit();
        return $this->execute()->fetch();
    }
    
    public function execute()
    {
        $sql = $values = [];
        foreach ($this->sqlParts as $part => $value) {
            if (empty($value) or isset($value['values']) and empty($value['values'])) {
                continue;
            }
            if (isset($value['values'])) {
                $values += $value['values'];
                $value = $value['query'];
            }
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            $sql[] = sprintf($this->sqlTemplate[$part], $value);
        }
        $statement = $this->Model->getConnection()->prepare(implode(' ', $sql));
        if (!$statement) {
            throw new \Exception('Query error: ' . $this->Model->getConnection()->errorInfo()[2]);
        }
        $statement->execute($values);
        return $statement;
    }
}