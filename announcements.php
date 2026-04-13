<?php
require_once 'config.php';
require_once 'includes/auth.php';
$q = trim($_GET['q'] ?? '');
$eventDate = trim($_GET['event_date'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && current_user() && !empty($_POST['announcement_id']) && !empty($_POST['comment'])) {
    $stmt = db()->prepare('INSERT INTO announcement_comments(announcement_id, user_id, comment_text, rating) VALUES(:aid,:uid,:comment,:rating)');
    $stmt->execute([
        'aid' => (int)$_POST['announcement_id'],
        'uid' => current_user()['id'],
        'comment' => trim($_POST['comment']),
        'rating' => min(5, max(1, (int)$_POST['rating'])),
    ]);
    header('Location: announcements.php');
    exit;
}

$sql = 'SELECT a.*, u.full_name FROM announcements a JOIN users u ON u.id = a.author_id WHERE 1=1';
$params = [];
if ($q !== '') {
    $sql .= ' AND (a.title LIKE :q OR a.body LIKE :q)';
    $params['q'] = '%' . $q . '%';
}
if ($eventDate !== '') {
    $sql .= ' AND a.event_date = :event_date';
    $params['event_date'] = $eventDate;
}
$sql .= ' ORDER BY a.event_date DESC';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$announcements = $stmt->fetchAll();
require 'includes/header.php';
?>
<h2 class="mb-3">Объявления</h2>
<form method="get" class="row g-2 mb-3">
    <div class="col-md-6"><input name="q" value="<?= h($q) ?>" class="form-control" placeholder="Фильтр: заголовок или текст объявления"></div>
    <div class="col-md-3"><input type="date" name="event_date" value="<?= h($eventDate) ?>" class="form-control"></div>
    <div class="col-md-2"><button class="btn btn-primary w-100">Фильтр</button></div>
</form>
<?php foreach ($announcements as $a): ?>
<div class="card mb-3">
    <div class="card-body">
        <h5><?= h($a['title']) ?></h5>
        <p><?= nl2br(h($a['body'])) ?></p>
        <div class="text-muted small">Дата события: <?= h($a['event_date']) ?> • Автор: <?= h($a['full_name']) ?></div>
        <?php if (current_user()): ?>
        <form method="post" class="row g-2 mt-2">
            <input type="hidden" name="announcement_id" value="<?= (int)$a['id'] ?>">
            <div class="col-md-8"><input name="comment" class="form-control" placeholder="Комментарий / вопрос" required></div>
            <div class="col-md-2"><select name="rating" class="form-select"><?php for($i=1;$i<=5;$i++):?><option><?= $i ?></option><?php endfor; ?></select></div>
            <div class="col-md-2"><button class="btn btn-outline-dark w-100">Отправить</button></div>
        </form>
        <?php endif; ?>
    </div>
</div>
<?php endforeach; ?>
<?php if (!$announcements): ?><div class="alert alert-info">Объявления по выбранному фильтру не найдены.</div><?php endif; ?>
<?php require 'includes/footer.php'; ?>
