<?php
require_once 'config.php';
require_once 'includes/auth.php';
require_login();

$user = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $about = trim($_POST['about'] ?? '');
    $avatar = trim($_POST['avatar_url'] ?? '');

    $stmt = db()->prepare('UPDATE users SET full_name=:full_name, phone=:phone, username=:username, about=:about, avatar_url=:avatar WHERE id=:id');
    $stmt->execute([
        'full_name' => trim($_POST['full_name']),
        'phone' => trim($_POST['phone']),
        'username' => trim($_POST['username']),
        'about' => $about,
        'avatar' => $avatar,
        'id' => $user['id'],
    ]);
    header('Location: profile.php');
    exit;
}

$user = current_user();
require 'includes/header.php';
?>
<h2>Профиль</h2>
<div class="row g-3">
    <div class="col-md-4">
        <div class="card card-body text-center">
            <img src="<?= h($user['avatar_url'] ?: 'https://placehold.co/240x240') ?>" class="rounded-circle mx-auto mb-2" width="180" height="180" alt="avatar">
            <h5><?= h($user['full_name']) ?></h5>
            <span class="badge text-bg-info"><?= h($user['role']) ?></span>
        </div>
    </div>
    <div class="col-md-8">
        <form method="post" class="card card-body">
            <input name="full_name" class="form-control mb-2" value="<?= h($user['full_name']) ?>" required>
            <input name="phone" class="form-control mb-2" value="<?= h($user['phone']) ?>" required>
            <input name="username" class="form-control mb-2" value="<?= h($user['username']) ?>" required>
            <input name="avatar_url" class="form-control mb-2" value="<?= h($user['avatar_url']) ?>" placeholder="Ссылка на аватарку">
            <textarea name="about" class="form-control mb-2" rows="4" placeholder="О себе"><?= h($user['about']) ?></textarea>
            <button class="btn btn-dark">Сохранить</button>
        </form>
    </div>
</div>
<?php require 'includes/footer.php'; ?>
