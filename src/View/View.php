<?php
namespace App\View;

use App\Controller\Base;

class View
{
    private $controller;
    
    public function __construct(Base $controller)
	{
        $this->controller = $controller;
    }
    
    public function render($data = [], $name = null)
	{
        if (!$name) {
            $name = $this->controller->action;
        }
        $content = $this->_render($name, $data);
        include TPL_PATH . 'layout.php';
        $this->controller->shutdown();
    }
    
    private function _render($name, $data)
	{
        if (!file_exists(TPL_PATH . $this->controller->name . DIRECTORY_SEPARATOR . $name . '.php')) {
            throw new Exception(sprintf('Template file %s not found in %s', $name . '.php', TPL_PATH . $this->controller->name . DIRECTORY_SEPARATOR));
        }
        extract($data);
        ob_start();
        include TPL_PATH . $this->controller->name . DIRECTORY_SEPARATOR . $name . '.php';
        return ob_get_clean();
    }
}
