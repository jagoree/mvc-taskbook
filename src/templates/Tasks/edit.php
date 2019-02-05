<h1>Редактирование</h1>
<div class="row">
    <form action="" method="post" class="col-12">
        <div class="form-group">
            <label><?= $row['email']; ?></label>
        </div>
        <div class="form-group">
            <label for="name">Имя</label>
            <input type="text" name="name" value="<?= $row['name']; ?>" id="name" class="form-control">
        </div>
        <div class="form-group">
            <label>Текст задачи</label>
            <textarea name="content" class="form-control"><?= $row['content']; ?></textarea>
        </div>
        <div class="form-check">
            <input type="checkbox" name="checked" id="checked" class="form-check-input"<?php if ($row['checked']) { ?> checked="checked"<?php } ?>>
            <label for="checked" class="form-check-label">Выполнено</label>
        </div>
        <br>
        <button class="btn btn-primary" type="submit">Сохранить</button>
    </form>
</div>