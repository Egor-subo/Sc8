<?php
require_once __DIR__ . '/auth.php';
$user = current_user();
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SPp — школьный портал</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">SPp</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div id="nav" class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto gap-lg-2 nav-modern">
                <li class="nav-item"><a class="nav-link" href="index.php">Главная</a></li>
                <li class="nav-item"><a class="nav-link" href="announcements.php">Объявления</a></li>
                <li class="nav-item"><a class="nav-link" href="assignments.php">Задания</a></li>
                <li class="nav-item"><a class="nav-link" href="journal.php">Журнал</a></li>
                <li class="nav-item"><a class="nav-link" href="support.php">Поддержка</a></li>
                <?php if ($user): ?>
                    <li class="nav-item"><a class="nav-link" href="profile.php">Профиль</a></li>
                    <?php if (in_array($user['role'], ['admin', 'director', 'vice_director'], true)): ?>
                        <li class="nav-item"><a class="nav-link" href="admin.php">Админ</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Выход</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="register.php">Регистрация</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php">Вход</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<main class="container py-4">
