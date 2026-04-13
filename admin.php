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

    if (isset($_POST['add_grade'])) {
        $stmt = db()->prepare('INSERT INTO grades(student_id, subject_id, teacher_id, grade, comment) VALUES(:student,:subject,:teacher,:grade,:comment)');
        $stmt->execute([
            'student' => (int)$_POST['student_id'],
            'subject' => (int)$_POST['subject_id'],
            'teacher' => current_user()['id'],
            'grade' => (int)$_POST['grade'],
            'comment' => trim($_POST['comment']),
        ]);
    }

    if (isset($_POST['answer_ticket'])) {
        $stmt = db()->prepare('UPDATE support_tickets SET answer=:answer, answered_by=:admin, answered_at=NOW() WHERE id=:id');
        $stmt->execute([
            'answer' => trim($_POST['answer']),
            'admin' => current_user()['id'],
            'id' => (int)$_POST['answer_ticket'],
        ]);
    }

    header('Location: admin.php');
    exit;
}

$students = db()->query('SELECT * FROM users WHERE role = "student" ORDER BY full_name ASC')->fetchAll();
$subjects = db()->query('SELECT * FROM subjects ORDER BY name ASC')->fetchAll();
$tickets = db()->query('SELECT t.*, u.full_name FROM support_tickets t JOIN users u ON u.id=t.user_id ORDER BY t.created_at DESC')->fetchAll();

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
<div class="col-lg-6">
    <div class="card card-body">
        <h5>Выставить оценку</h5>
        <form method="post" class="row g-2">
            <div class="col-12"><select name="student_id" class="form-select"><?php foreach($students as $s): ?><option value="<?= (int)$s['id'] ?>"><?= h($s['full_name']) ?></option><?php endforeach; ?></select></div>
            <div class="col-12"><select name="subject_id" class="form-select"><?php foreach($subjects as $s): ?><option value="<?= (int)$s['id'] ?>"><?= h($s['name']) ?></option><?php endforeach; ?></select></div>
            <div class="col-4"><input type="number" min="2" max="5" name="grade" class="form-control" required></div>
            <div class="col-8"><input name="comment" class="form-control" placeholder="Комментарий"></div>
            <div class="col-12"><button class="btn btn-dark" name="add_grade" value="1">Сохранить</button></div>
        </form>
    </div>
</div>
</div>

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
