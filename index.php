<?php
require_once 'config.php';
require_once 'includes/auth.php';
$q = trim($_GET['q'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && current_user()) {
    if (isset($_POST['like_post'])) {
        $stmt = db()->prepare('INSERT INTO post_likes(post_id, user_id) VALUES(:post_id, :user_id) ON DUPLICATE KEY UPDATE user_id = VALUES(user_id)');
        $stmt->execute(['post_id' => (int)$_POST['like_post'], 'user_id' => current_user()['id']]);
    }

    if (!empty($_POST['comment_post']) && !empty($_POST['comment_text'])) {
        $stmt = db()->prepare('INSERT INTO post_comments(post_id, user_id, comment_text) VALUES(:post_id,:user_id,:comment_text)');
        $stmt->execute([
            'post_id' => (int)$_POST['comment_post'],
            'user_id' => current_user()['id'],
            'comment_text' => trim($_POST['comment_text']),
        ]);
    }

    header('Location: index.php');
    exit;
}

$sql = 'SELECT p.*, u.full_name,
    (SELECT COUNT(*) FROM post_likes l WHERE l.post_id = p.id) AS likes
    FROM posts p JOIN users u ON u.id = p.author_id';
$params = [];
if ($q !== '') {
    $sql .= ' WHERE p.title LIKE :q OR p.content LIKE :q OR u.full_name LIKE :q';
    $params['q'] = '%' . $q . '%';
}
$sql .= ' ORDER BY p.created_at DESC';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$posts = $stmt->fetchAll();

require 'includes/header.php';
?>
<div class="p-4 p-md-5 mb-4 rounded-4 hero text-white">
    <h1>Школьный портал SPp</h1>
    <p class="mb-0">Новости школы, задания, журнал оценок, поддержка и личные профили — всё в одном месте.</p>
</div>
<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="card card-body h-100"><h6>1) Добавление контента</h6><p class="mb-0 text-muted">Посты/новости добавляются в таблицу <code>posts</code>. Объявления — в <code>announcements</code>.</p></div></div>
    <div class="col-md-4"><div class="card card-body h-100"><h6>2) Задания и оценки</h6><p class="mb-0 text-muted">Учителя публикуют задания в разделе «Задания», оценки редактируют в «Журнале».</p></div></div>
    <div class="col-md-4"><div class="card card-body h-100"><h6>3) Профиль</h6><p class="mb-0 text-muted">В «Профиле» есть поле «О себе», можно обновить телефон, username и аватар.</p></div></div>
</div>
<form method="get" class="row g-2 mb-3">
    <div class="col-md-6"><input name="q" value="<?= h($q) ?>" class="form-control" placeholder="Фильтр по постам: заголовок, текст, автор"></div>
    <div class="col-md-2"><button class="btn btn-primary w-100">Фильтр</button></div>
</form>

<?php foreach ($posts as $post): ?>
    <article class="card mb-3 shadow-sm">
        <div class="card-body">
            <h5 class="card-title"><?= h($post['title']) ?></h5>
            <p class="card-text"><?= nl2br(h($post['content'])) ?></p>
            <?php if ($post['image_url']): ?>
                <img src="<?= h($post['image_url']) ?>" class="img-fluid rounded post-image" alt="Пост">
            <?php endif; ?>
            <div class="mt-3 d-flex gap-2 align-items-center">
                <small class="text-muted">Автор: <?= h($post['full_name']) ?> • <?= h($post['created_at']) ?></small>
                <span class="badge text-bg-primary">Лайки: <?= (int)$post['likes'] ?></span>
            </div>
            <?php if (current_user()): ?>
                <form method="post" class="mt-2 d-flex gap-2">
                    <button class="btn btn-sm btn-outline-primary" name="like_post" value="<?= (int)$post['id'] ?>">👍 Лайк</button>
                </form>
                <form method="post" class="mt-2">
                    <input type="hidden" name="comment_post" value="<?= (int)$post['id'] ?>">
                    <textarea class="form-control" name="comment_text" rows="2" placeholder="Оставить комментарий"></textarea>
                    <button class="btn btn-sm btn-dark mt-2">Отправить</button>
                </form>
            <?php endif; ?>
        </div>
    </article>
<?php endforeach; ?>
<?php if (!$posts): ?>
    <div class="alert alert-info">Посты не найдены по выбранному фильтру.</div>
<?php endif; ?>

<?php require 'includes/footer.php'; ?>
