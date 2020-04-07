<?php

namespace App\Controller;

use App\Core\Router;
use App\Core\Inflector;
use App\View\View;

/**
 * Base controller class
 * @author jagoree
 */
class BaseController implements ControllerInterface
{

    /**
     *
     * @var string Current controller action
     */
    public $action;

    /**
     *
     * @var \App\Model\Model
     */
    public $Model;
    /**
     *
     * @var bool Use model in current controller
     */
    protected $useModel = true;
    /**
     *
     * @var \App\View\View View object 
     */
    protected $View;
    /**
     *
     * @var int Number of rows
     */
    protected $limit = 3;

    public function __construct($config = [])
    {
        try {
            $this->name = $config['controller'];
            $this->action = $config['action'];
            if ($this->useModel) {
                $modelName = Inflector::classify($config['controller']);
                $modelClass = '\App\Model\\' . $modelName;
                if (!class_exists($modelClass)) {
                    throw new \RuntimeException(sprintf('Model %s not found for controller %s', $modelName, $config['controller']));
                }
                if (!method_exists($this, $config['action'])) {
                    throw new \RuntimeException(sprintf('You should create method %s in controller %s', $config['action'], $config['controller']));
                }
                $this->{$modelName} = $this->Model = new $modelClass();
            }
            
            $this->View = new View($this);
        } catch (\Exception $ex) {
            printf("<pre>%s\n%s</pre>", $ex->getMessage(), $ex->getTraceAsString());
        }
    }

    /**
     * @inheritDoc
     */
    public function render(array $data = [], string $name = null)
    {
        if (!$this->View) {
            $this->View = new View($this);
        }
        $this->View->render($data, $name);
    }

    /**
     * Calls after view file has been rendered
     */
    public function shutdown()
    {
        
    }

    /**
     * Controller index action
     */
    public function index()
    {
        $query = ['limit' => $this->limit, 'order' => ['id' => 'DESC']];
        if ($param = Router::getQuery('order') and $order = $this->getOrder($param)) {
            $query['order'] = $order;
        }
        if ($page = Router::getQuery('page')) {
            if ($page < 1) {
                $page = 1;
            }
            $offset = $this->limit * ($page - 1);
            $query['offset'] = $offset;
        } else {
            $page = 1;
        }
        $data = $this->Model->fetch('all', $query);
        $this->render(['rows' => $data, 'count_rows' => $this->Model->count(), 'current_page' => $page]);
    }

    /**
     * Controller add action
     */
    public function add()
    {
        $data = [];
        if (Router::isPost()) {
            if ($errors = $this->Model->validate($_POST)) {
                $data['errors'] = $errors;
            } else {
                if ($this->Model->create($_POST + ['created' => date('Y-m-d H:i:s')])) {
                    $_SESSION['added'] = 1;
                    $this->redirect();
                }
            }
        }
        $this->render($data);
    }

    /**
     * Controller edit action
     * 
     * @param int $id ID of editing record
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function edit(int $id = null)
    {
        if (!$id or ! is_numeric($id)) {
            throw new InvalidArgumentException(sprintf('Argument must be an integer, %s given', gettype($id)));
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
                }
                $this->redirect();
            }
        }
        $row = $this->Model->fetch('byid', ['value' => $id]);
        $this->render(['row' => $row]);
    }

    /**
     * Redirects to URL
     * @param string $url
     * @param bool $exit
     */
    protected function redirect($url = '/', $exit = true)
    {
        header("Location: {$url}");
        if ($exit === true) {
            exit;
        }
    }

    /**
     * Returns order conditions
     * 
     * @param string $value
     * @return string|null
     */
    private function getOrder($value)
    {
        switch ($value) {
            case 'name-up':
                return ['name'];
            case 'name-down':
                return ['name' => 'DESC'];
            case 'email-up':
                return ['email'];
            case 'email-down':
                return ['email' => 'DESC'];
            case 'status-up':
                return ['checked'];
            case 'status-down':
                return ['checked' => 'DESC'];
        }
        return null;
    }

}
