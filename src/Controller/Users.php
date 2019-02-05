<?php
namespace App\Controller;

use App\Controller\Base;
use App\Core\Router;

class Users extends Base
{
    protected $useModel = false;
    
    public function login()
    {
        $error = null;
        if (Router::isPost()) {
            if ($_POST['login'] == 'admin' and md5($_POST['password']) == '202cb962ac59075b964b07152d234b70') {
                $_SESSION['auth'] = true;
                header('Location: /');
            } else {
                $error = 'Неверный логин или пароль';
            }
        }
        $this->View->render(['error' => $error]);
    }
    
    public function logout()
    {
        session_destroy();
        header('Location: /');
    }
}
