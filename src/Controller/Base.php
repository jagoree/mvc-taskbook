<?php
namespace App\Controller;

use App\Core\Router;
use App\Core\Inflector;
use App\View\View;

class Base
{
    public $name;
    public $action;
    public $modelClass;
    public $Model;
    
    protected $useModel = true;
    protected $View;
    
    public function __construct($config = [])
	{
        try {
            $this->name = $config['controller'];
            $this->action = $config['action'];
            if ($this->useModel) {
                $modelName = Inflector::classify($config['controller']);
                $modelClass = '\App\Model\\' . $modelName;
                if (!class_exists($modelClass)) {
                    throw new \Exception(sprintf('Model %s not found for controller %s', $modelName, $config['controller']));
                }
                if (!method_exists($this, $config['action'])) {
                    throw new \Exception(sprintf('You should create method %s in controller %s', $config['action'], $config['controller']));
                }
                $this->modelClass = $modelName;
                $this->{$modelName} = $this->Model = new $modelClass(['name' => $modelName]);
            }
            $this->View = new View($this);
            $callable = [$this, $config['action']];
            $callable((isset($config['id']) ? $config['id'] : null));
        } catch (\Exception $ex) {
            echo $ex->getMessage();
        }
    }
    
    public function shutdown()
	{
        
    }
    
    public function index()
	{
        $query = ['limit' => 3, 'order' => ' ORDER BY `id` DESC'];
        if ($param = Router::getQuery('order') and $order = $this->getOrder($param)) {
            $query['order'] = $order;
        }
        if ($page = Router::getQuery('page')) {
            if ($page < 1) {
                $page = 1;
            }
            $offset = 3 * ($page - 1);
            $query['offset'] = $offset;
        } else {
            $page = 1;
        }
        $data = $this->Model->find('all', $query);
        $this->View->render(['rows' => $data, 'count_rows' => $this->Model->count(), 'current_page' => $page]);
    }
    
    public function add()
	{
        $data = [];
        if (Router::isPost()) {
            if ($errors = $this->Model->validate($_POST)) {
                $data['errors'] = $errors;
            } else {
                if ($this->Model->create($_POST)) {
                    $_SESSION['added'] = 1;
                    header('Location: /');
                    exit();
                }
            }
        }
        $this->View->render($data);
    }
    
    public function edit($id = null)
	{
        if (!$id or !is_numeric($id)) {
            throw new Exception(sprintf('Argument must be an integer, %s given', gettype($id)));
        }
        if (!isset($_SESSION['auth'])) {
            throw new Exception('Доступ закрыт!');
        }
        if (Router::isPost()) {
            if ($errors = $this->Model->validate($_POST, true)) {
                $data['errors'] = $errors;
            } else {
                $data = array_merge($_POST, ['id' => $id]);
                if (isset($data['checked'])) {
                    $data['checked'] = 1;
                } else {
                    $data['checked'] = 0;
                }
                if ($this->Model->update($data)) {
                    $_SESSION['edited'] = 1;
                    header('Location: /');
                    exit();
                }
            }
        }
        $row = $this->Model->find('byid', ['value' => $id]);
        $this->View->render(['row' => $row]);
    }
    
    private function getOrder($value)
	{
        $order = '';
        switch ($value) {
            case 'name-up':
                $order = '`name` ASC';
                break;
            case 'name-down':
                $order = '`name` DESC';
                break;
            case 'email-up':
                $order = '`email` ASC';
                break;
            case 'email-down':
                $order = '`email` DESC';
                break;
            case 'status-up':
                $order = '`checked` ASC';
                break;
            case 'status-down':
                $order = '`checked` DESC';
                break;
        }
        if ($order) {
            $order = 'ORDER BY ' . $order;
        }
        return $order;
    }
}
