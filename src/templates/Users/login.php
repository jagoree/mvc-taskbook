<h1>Авторизация</h1>
<div class="row">
    <form action="" method="post">
        <?php if (!empty($error)) { ?>
        <p class="alert alert-danger"><?= $error; ?></p>
        <?php } ?>
        <div class="form-group">
            <label>Логин</label>
            <input type="text" name="login" value="" class="form-control">
        </div>
        <div class="form-group">
            <label>Пароль</label>
            <input type="password" name="password" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary mb-2">Войти</button>
    </form>
</div>