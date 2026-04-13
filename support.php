<?php
require_once 'config.php';
require_once 'includes/auth.php';
require_once 'includes/captcha.php';
require_login();

$error = '';
$captcha = generate_captcha();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message'] ?? '');
    if (mb_strlen($message) > SUPPORT_MAX_LENGTH) {
        $error = 'Максимум 1500 символов.';
    } elseif (!validate_captcha($_POST['captcha'] ?? null)) {
        $error = 'Неверная капча.';
    } else {
        $stmt = db()->prepare('INSERT INTO support_tickets(user_id, message) VALUES(:uid,:message)');
        $stmt->execute(['uid' => current_user()['id'], 'message' => $message]);
        header('Location: support.php');
        exit;
    }
    $captcha = generate_captcha();
}

$tickets = db()->prepare('SELECT t.*, a.full_name AS admin_name FROM support_tickets t LEFT JOIN users a ON a.id = t.answered_by WHERE t.user_id = :uid ORDER BY t.created_at DESC');
$tickets->execute(['uid' => current_user()['id']]);

require 'includes/header.php';
?>
<h2>Поддержка</h2>
<?php if ($error): ?><div class="alert alert-danger"><?= h($error) ?></div><?php endif; ?>
<form method="post" class="card card-body mb-3">
    <textarea name="message" class="form-control" maxlength="1500" rows="4" placeholder="Опишите вопрос" required></textarea>
    <label class="form-label mt-2">Капча: <?= h($captcha) ?></label>
    <input name="captcha" class="form-control" required>
    <button class="btn btn-primary mt-2">Отправить</button>
</form>

<?php foreach($tickets->fetchAll() as $t): ?>
<div class="card mb-2"><div class="card-body">
    <div><?= nl2br(h($t['message'])) ?></div>
    <small class="text-muted">Дата: <?= h($t['created_at']) ?></small>
    <?php if ($t['answer']): ?><hr><div><b>Ответ администратора (<?= h($t['admin_name']) ?>):</b> <?= nl2br(h($t['answer'])) ?></div><?php endif; ?>
</div></div>
<?php endforeach; ?>
<?php require 'includes/footer.php'; ?>
