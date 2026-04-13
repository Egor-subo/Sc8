<?php
require_once 'config.php';
require_once 'includes/captcha.php';

$errors = [];
$captcha = generate_captcha();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $age = (int)($_POST['age'] ?? 0);
    $classNum = (int)($_POST['class_num'] ?? 0);

    if ($age < 8) $errors[] = 'Регистрация доступна только с 8 лет.';
    if (!preg_match('/^\+7\d{10}$/', $phone)) $errors[] = 'Телефон должен быть в формате +7XXXXXXXXXX.';
    if (mb_strlen($password) < MIN_PASSWORD_LENGTH) $errors[] = 'Пароль минимум 8 символов.';
    if ($classNum < 1 || $classNum > 11) $errors[] = 'Класс должен быть от 1 до 11.';
    if (!validate_captcha($_POST['captcha'] ?? null)) $errors[] = 'Капча неверная.';

    if (!$errors) {
        $stmt = db()->prepare('INSERT INTO users(full_name, phone, username, password_hash, age, class_num, role) VALUES(:full_name,:phone,:username,:password_hash,:age,:class_num,"student")');
        try {
            $stmt->execute([
                'full_name' => $fullName,
                'phone' => $phone,
                'username' => $username,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'age' => $age,
                'class_num' => $classNum,
            ]);
            header('Location: login.php?registered=1');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Пользователь с таким телефоном или username уже существует.';
        }
    }
    $captcha = generate_captcha();
}

require 'includes/header.php';
?>
<div class="card shadow-sm mx-auto" style="max-width: 640px;">
    <div class="card-body">
        <h3>Регистрация ученика</h3>
        <?php foreach ($errors as $error): ?>
            <div class="alert alert-danger py-2"><?= h($error) ?></div>
        <?php endforeach; ?>
        <form method="post" class="row g-3">
            <div class="col-12"><input name="full_name" class="form-control" placeholder="ФИО" required></div>
            <div class="col-md-6"><input name="phone" class="form-control" placeholder="+79991234567" required></div>
            <div class="col-md-6"><input name="username" class="form-control" placeholder="UserName" required></div>
            <div class="col-md-6"><input name="password" type="password" class="form-control" placeholder="Пароль" required></div>
            <div class="col-md-3"><input name="age" type="number" min="8" class="form-control" placeholder="Возраст" required></div>
            <div class="col-md-3">
                <select name="class_num" class="form-select" required>
                    <option value="">Класс</option>
                    <?php for ($i = 1; $i <= 11; $i++): ?><option><?= $i ?></option><?php endfor; ?>
                </select>
            </div>
            <div class="col-12"><label class="form-label">Капча: <?= h($captcha) ?></label><input name="captcha" class="form-control" required></div>
            <div class="col-12"><button class="btn btn-primary">Зарегистрироваться</button></div>
        </form>
    </div>
</div>
<?php require 'includes/footer.php'; ?>
