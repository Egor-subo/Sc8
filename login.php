<?php
require_once 'config.php';
require_once 'includes/captcha.php';

$error = '';
$captcha = generate_captcha();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identity = trim($_POST['identity'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!validate_captcha($_POST['captcha'] ?? null)) {
        $error = 'Неверная капча.';
    } else {
        $stmt = db()->prepare('SELECT * FROM users WHERE phone = :identity OR username = :identity');
        $stmt->execute(['identity' => $identity]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            header('Location: index.php');
            exit;
        }
        $error = 'Неверные данные входа.';
    }
    $captcha = generate_captcha();
}

require 'includes/header.php';
?>
<div class="card shadow-sm mx-auto" style="max-width: 500px;">
    <div class="card-body">
        <h3>Вход</h3>
        <?php if ($error): ?><div class="alert alert-danger"><?= h($error) ?></div><?php endif; ?>
        <form method="post" class="row g-3">
            <div class="col-12"><input name="identity" class="form-control" placeholder="Телефон или UserName" required></div>
            <div class="col-12"><input type="password" name="password" class="form-control" placeholder="Пароль" required></div>
            <div class="col-12"><label class="form-label">Капча: <?= h($captcha) ?></label><input name="captcha" class="form-control" required></div>
            <div class="col-12"><button class="btn btn-dark">Войти</button></div>
        </form>
    </div>
</div>
<?php require 'includes/footer.php'; ?>
