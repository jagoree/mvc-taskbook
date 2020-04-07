<?php
use App\Core\Router;
?>
<h1>Список задач</h1>
<?php if (isset($_SESSION['added']) || isset($_SESSION['edited'])) { ?>
<p class="alert alert-success">Задача <?= isset($_SESSION['added']) ? 'добавлена' : 'отредактирована'; ?>!</p>
<?php } ?>
<div class="row font-weight-bold">
    <div class="col-2">Имя <a href="?order=name-up">&uArr;</a>&nbsp;<a href="?order=name-down">&dArr;</a></div>
    <div class="col-2">E-mail <a href="?order=email-up">&uArr;</a>&nbsp;<a href="?order=email-down">&dArr;</a></div>
    <div class="col-4">Описание задачи</div>
    <div class="col-2">Дата постановки</div>
    <div class="col-1">Выполнено <a href="?order=status-up">&uArr;</a>&nbsp;<a href="?order=status-down">&dArr;</a></div>
    <?php if (isset($_SESSION['auth'])) { ?>
    <div class="col-1">&nbsp;</div>
    <?php } ?>
</div>
    <?php foreach ($rows as $row) { ?>
<div class="row">
    <div class="col-2"><?= $row['name']; ?></div>
    <div class="col-2"><a href="mailto:<?= $row['email']; ?>"><?= $row['email']; ?></a></div>
    <div class="col-4">
        <p><?= htmlspecialchars($row['content']); ?></p>
    </div>
    <div class="col-2"><?= date('d.m.Y H:i:s', strtotime($row['created'])); ?></div>
    <div class="col-1"><?= $row['checked'] ? '&#10004;': ''; ?></div>
    <?php if (isset($_SESSION['auth'])) { ?>
    <div class="col-1"><a href="/tasks/edit/<?php echo $row['id']; ?>">Редактировать</a></div>
    <?php } ?>
</div>
    <?php } ?>
<?php if ($count_rows > 3) {
    $_url = [];
    if ($order = Router::getQuery('order')) {
        $_url['order'] = $order;
    }
    ?>
<nav>
    <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= ceil($count_rows / 3); $i ++) {
            $_url['page'] = $i;
            $url = [];
            foreach ($_url as $key => $value) {
                $url[] = $key . '=' . $value;
            }
            ?>
        <li class="page-item<?php if ($current_page == $i) { ?> active<?php } ?>"><a href="?<?= implode('&', $url); ?>" class="page-link"><?= $i; ?></a></li>
        <?php } ?>
    </ul>
</nav>
<?php
}