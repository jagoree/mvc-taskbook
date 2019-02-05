<!doctype html>
<html>
    <head>
        <title></title>
        <link rel="stylesheet" href="/css/bootstrap.min.css">
    </head>
    <body>
        <div class="container">
            <header class="mt-sm-4">
                <a href="/" class="mr-sm-2">Все задачи</a>
                <a href="/tasks/add" class="mr-sm-2">Добавить задачу</a>
                <?php if (!isset($_SESSION['auth'])) { ?>
                <a href="/users">Войти</a>
                <?php } else { ?>
                <a href="/users/logout">Выйти</a>
                <?php } ?>
            </header>
            <div class="content" style="margin-top: 10px;">
                <?= $content; ?>
            </div>
        </div>
    </body>
</html>