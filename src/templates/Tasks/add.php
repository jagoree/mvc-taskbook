<?php
use App\Core\Router;
?>
<h1>Новая задача</h1>
<div class="row">
    <form action="" method="post" class="col-12">
        <div class="form-group">
            <label>Ваше имя<span>*</span></label>
            <input type="text" name="name" value="<?= Router::getData('name'); ?>" class="form-control">
            <?php if (isset($errors['name'])) { ?>
            <p class="text-danger"><?= $errors['name']; ?></p>
            <?php } ?>
        </div>
        <div class="form-group">
            <label>E-mail<span>*</span></label>
            <input type="email" name="email" value="<?= Router::getData('email'); ?>" class="form-control">
            <?php if (isset($errors['email'])) { ?>
            <p class="text-danger"><?= $errors['email']; ?></p>
            <?php } ?>
        </div>
        <div class="form-group">
            <label>Текст задачи<span>*</span></label>
            <textarea name="content" class="form-control"><?= Router::getData('content'); ?></textarea>
            <?php if (isset($errors['content'])) { ?>
            <p class="text-danger"><?= $errors['content']; ?></p>
            <?php } ?>
        </div>
        <br>
        <button class="btn btn-primary" type="submit">Сохранить</button>
    </form>
</div>