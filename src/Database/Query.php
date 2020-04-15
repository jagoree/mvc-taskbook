<?php

namespace App\Database;

/**
 * Builds and executes SQL queries
 *
 * @author jagoree
 */
class Query
{

    /**
     *
     * @var \App\Database\DriverInterface Database connection instance
     */
    private $connection = null;
    
    /**
     *
     * @var string|null Table name for the query used
     */
    private $table = null;
    
    /**
     *
     * @var array SQL fragments for a query
     */
    private $sqlParts = [
        'select' => [],
        'from' => [],
        'update' => null,
        'set' => null,
        'insert' => null,
        'where' => null,
        'order' => null,
        'limit' => null,
        'offset' => null
    ];
    
    /**
     *
     * @var array SQL template for a query
     */
    private $sqlTemplate = [
        'select' => 'SELECT %s',
        'update' => 'UPDATE %s',
        'insert' => 'INSERT INTO %s',
        'set' => 'SET %s',
        'from' => 'FROM %s',
        'where' => 'WHERE %s',
        'order' => 'ORDER BY %s',
        'limit' => 'LIMIT %s',
        'offset' => 'OFFSET %s'
    ];

    /**
     * 
     * @param \App\Database\DriverInterface $connection
     * @param string $table
     * @return $this
     */
    public function __construct($connection, $table)
    {
        $this->connection = $connection;
        $this->table = $table;
        $this->sqlParts['from'] = "`{$table}`";
        $this->select('*');
        return $this;
    }

    /**
     * 
     * @param array|string $fields Selected field(s) for the query
     * @return $this
     */
    public function select($fields = [])
    {
        if (!is_array($fields)) {
            $fields = [$fields];
        }
        $fields = array_map(function ($field) {
            if ($field != '*' and ! preg_match('~[\(\)]+|all|distinct|distinctrow~i', $field)) {
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

    public function order($order = [])
    {
        $expr = [];
        foreach ($order as $key => $value) {
            if (is_numeric($key) or is_string($key) and ! in_array(strtolower($value), ['asc', 'desc'])) {
                $key = $value;
                $value = 'ASC';
            }
            $expr[] = implode(' ', ["`{$key}`", $value]);
        }
        $this->sqlParts['order'] = implode(', ', $expr);
        return $this;
    }

    public function limit($limit = 1)
    {
        $this->sqlParts['limit'] = [
            'query' => ':limit',
            'values' => [
                ':limit' => [
                    'value' => $limit,
                    'type' => \PDO::PARAM_INT
                ]
            ]
        ];
        return $this;
    }

    public function offset($offset = 1)
    {
        $this->sqlParts['offset'] = [
            'query' => ':offset',
            'values' => [
                ':offset' => [
                    'value' => $offset,
                    'type' => \PDO::PARAM_INT
                ]
            ]
        ];
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
        $this->sqlParts['insert'] = [
            'query' => implode(' ', [
                "`{$this->table}`",
                '(' . implode(',', $columns) . ')',
                'VALUES (' . implode(',', $placeholders) . ')'
            ]),
            'values' => $values
        ];
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
        $this->sqlParts['set'] = [
            'query' => implode(', ', $vars),
            'values' => $values
        ];
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
    
    public function getSql()
    {
        $sql = $this->createSql();
        $values = $this->buildParams();
        
        $statement = $this->connection->prepare($sql);
        
        $values = array_map(function ($item, $key) {
            return $key . ' = ' . $item['value'];
        }, $values, array_keys($values));
        
        return sprintf('%s [%s]', $statement->queryString, implode(', ', $values));
    }

    public function execute()
    {
        $sql = $this->createSql();
        $values = $this->buildParams();
        
        $statement = $this->connection->prepare($sql);
        
        foreach ($values as $key => $value) {
            $type = \PDO::PARAM_STR;
            if (is_array($value) and isset($value['type'])) {
                $type = $value['type'];
                $value = $value['value'];
            }
            $statement->bindValue($key, $value, $type);
        }
        if (!$statement) {
            throw new \Exception('Query error: ' . $this->connection->errorInfo()[2]);
        }
        $statement->execute();
        
        return $statement;
    }
    
    private function createSql()
    {
        $sql = [];
        foreach ($this->sqlParts as $part => $value) {
            if (empty($value) or isset($value['values']) and empty($value['values'])) {
                continue;
            }
            if (isset($value['values'])) {
                $value = $value['query'];
            }
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            $sql[] = sprintf($this->sqlTemplate[$part], $value);
        }
        return implode(' ', $sql);
    }
    
    private function buildParams()
    {
        $values = [];
        foreach ($this->sqlParts as $part => $value) {
            if (empty($value) or isset($value['values']) and empty($value['values'])) {
                continue;
            }
            if (isset($value['values'])) {
                $values += $value['values'];
            }
        }
        return $values;
    }

}
