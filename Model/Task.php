<?php
namespace App\Model;

class Task extends Model
{   
    public function validate($fields, $update = false)
	{
        $errors = [];
        foreach (['name', 'email', 'content'] as $field_name) {
            if ($field_name == 'email' and $update === true) {
                continue;
            }
            if (empty($fields[$field_name])) {
                $errors[$field_name] = 'Поле нужно заполнить!';
            }
        }
        if (!$update and !isset($erros['email']) and !filter_var($fields['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Некорректный адрес E-mail';
        }
        if (!isset($errors['content']) and mb_strlen($fields['content']) < 10) {
            $errors['content'] = 'Слишком короткое описание задачи';
        }
        return $errors;
    }
}
