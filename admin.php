<?php
require_once 'config.php';
require_once 'includes/auth.php';
require_login();

if (!has_role(['admin', 'director', 'vice_director'])) {
    http_response_code(403);
    exit('Нет доступа');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_user'])) {
        $stmt = db()->prepare('DELETE FROM users WHERE id = :id AND role = "student"');
        $stmt->execute(['id' => (int)$_POST['delete_user']]);
    }

    if (isset($_POST['answer_ticket'])) {
        $stmt = db()->prepare('UPDATE support_tickets SET answer=:answer, answered_by=:admin, answered_at=NOW() WHERE id=:id');
        $stmt->execute([
            'answer' => trim($_POST['answer']),
            'admin' => current_user()['id'],
            'id' => (int)$_POST['answer_ticket'],
        ]);
    }

    if (isset($_POST['add_teacher']) && has_role(['director', 'admin'])) {
        $fullName = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $subjectId = (int)($_POST['subject_id'] ?? 0);
        $classesRaw = trim($_POST['class_nums'] ?? '');

        if ($fullName !== '' && preg_match('/^\+7\d{10}$/', $phone) && mb_strlen($password) >= MIN_PASSWORD_LENGTH && $subjectId > 0) {
            $pdo = db();
            $pdo->beginTransaction();
            try {
                $stmt = $pdo->prepare('INSERT INTO users(full_name, phone, username, password_hash, role) VALUES(:full_name, :phone, :username, :password_hash, "teacher")');
                $stmt->execute([
                    'full_name' => $fullName,
                    'phone' => $phone,
                    'username' => $username,
                    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                ]);
                $teacherId = (int)$pdo->lastInsertId();

                $classNums = array_filter(array_map('trim', explode(',', $classesRaw)));
                $classNums = array_values(array_unique(array_map('intval', $classNums)));
                foreach ($classNums as $classNum) {
                    if ($classNum >= 1 && $classNum <= 11) {
                        $assignStmt = $pdo->prepare('INSERT INTO teacher_subject_classes(teacher_id, subject_id, class_num) VALUES(:teacher_id, :subject_id, :class_num)');
                        $assignStmt->execute([
                            'teacher_id' => $teacherId,
                            'subject_id' => $subjectId,
                            'class_num' => $classNum,
                        ]);
                    }
                }
                $pdo->commit();
            } catch (Throwable $e) {
                $pdo->rollBack();
            }
        }
    }

    header('Location: admin.php');
    exit;
}

$students = db()->query('SELECT * FROM users WHERE role = "student" ORDER BY full_name ASC')->fetchAll();
$subjects = db()->query('SELECT * FROM subjects ORDER BY name ASC')->fetchAll();
$tickets = db()->query('SELECT t.*, u.full_name FROM support_tickets t JOIN users u ON u.id=t.user_id ORDER BY t.created_at DESC')->fetchAll();
$teacherLoads = db()->query('SELECT tsc.teacher_id, u.full_name AS teacher_name, s.name AS subject_name, tsc.class_num
                             FROM teacher_subject_classes tsc
                             JOIN users u ON u.id = tsc.teacher_id
                             JOIN subjects s ON s.id = tsc.subject_id
                             ORDER BY u.full_name ASC, tsc.class_num ASC')->fetchAll();

require 'includes/header.php';
?>
<h2>Админ-панель</h2>
<div class="row g-3">
<div class="col-lg-6">
    <div class="card card-body">
        <h5>Ученики</h5>
        <?php foreach($students as $s): ?>
            <div class="d-flex justify-content-between border-bottom py-1">
                <span><?= h($s['full_name']) ?> (<?= (int)$s['class_num'] ?> класс)</span>
                <form method="post"><button class="btn btn-sm btn-outline-danger" name="delete_user" value="<?= (int)$s['id'] ?>">Удалить</button></form>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</div>

<?php if (has_role(['director', 'admin'])): ?>
<div class="card card-body mt-3">
    <h5>Добавить учителя (роль + предмет + классы)</h5>
    <form method="post" class="row g-2">
        <div class="col-md-3"><input name="full_name" class="form-control" placeholder="ФИО учителя" required></div>
        <div class="col-md-2"><input name="phone" class="form-control" placeholder="+79991234567" required></div>
        <div class="col-md-2"><input name="username" class="form-control" placeholder="username" required></div>
        <div class="col-md-2"><input name="password" type="password" class="form-control" placeholder="Пароль от 8 символов" required></div>
        <div class="col-md-3"><select name="subject_id" class="form-select" required><?php foreach($subjects as $s): ?><option value="<?= (int)$s['id'] ?>"><?= h($s['name']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-6"><input name="class_nums" class="form-control" placeholder="Классы через запятую: 5,6,7" required></div>
        <div class="col-md-2"><button class="btn btn-primary w-100" name="add_teacher" value="1">Создать</button></div>
    </form>
</div>

<div class="card card-body mt-3">
    <h5>Нагрузка учителей</h5>
    <table class="table table-sm table-striped">
        <thead><tr><th>Учитель</th><th>Предмет</th><th>Класс</th></tr></thead>
        <tbody>
        <?php foreach($teacherLoads as $load): ?>
            <tr>
                <td><?= h($load['teacher_name']) ?></td>
                <td><?= h($load['subject_name']) ?></td>
                <td><?= (int)$load['class_num'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<div class="card card-body mt-3">
    <h5>Запросы в поддержку</h5>
    <?php foreach($tickets as $t): ?>
        <div class="border rounded p-2 mb-2">
            <div><b><?= h($t['full_name']) ?></b>: <?= h($t['message']) ?></div>
            <?php if (!$t['answer']): ?>
                <form method="post" class="mt-2 d-flex gap-2">
                    <input class="form-control" name="answer" placeholder="Ответ администратора" required>
                    <button class="btn btn-primary" name="answer_ticket" value="<?= (int)$t['id'] ?>">Ответить</button>
                </form>
            <?php else: ?><div class="text-success">Ответ: <?= h($t['answer']) ?></div><?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
<?php require 'includes/footer.php'; ?>
