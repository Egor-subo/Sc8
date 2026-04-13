<?php
require_once 'config.php';
require_once 'includes/auth.php';
require_login();

$user = current_user();
$subject = trim($_GET['subject'] ?? '');
$classNum = (int)($_GET['class_num'] ?? 0);
$canManageAllGrades = ($user['role'] === 'teacher');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $canManageAllGrades && isset($_POST['grade_id'])) {
    $stmt = db()->prepare('UPDATE grades SET grade = :grade, comment = :comment WHERE id = :id');
    $stmt->execute([
        'grade' => max(2, min(5, (int)($_POST['grade'] ?? 2))),
        'comment' => trim($_POST['comment'] ?? ''),
        'id' => (int)$_POST['grade_id'],
    ]);
    header('Location: journal.php?subject=' . urlencode($subject) . '&class_num=' . $classNum);
    exit;
}

$sql = 'SELECT g.id, g.grade, g.comment, g.created_at, s.name as subject_name,
               u.full_name as student_name, u.class_num, t.full_name as teacher_name
        FROM grades g
        JOIN subjects s ON s.id = g.subject_id
        JOIN users u ON u.id = g.student_id
        JOIN users t ON t.id = g.teacher_id WHERE 1=1';
$params = [];

if ($canManageAllGrades) {
    if ($classNum >=1 && $classNum <=11) {
        $sql .= ' AND u.class_num = :class_num';
        $params['class_num'] = $classNum;
    }
} else {
    $sql .= ' AND g.student_id = :student_id';
    $params['student_id'] = $user['id'];
}
if ($subject !== '') {
    $sql .= ' AND s.name = :subject';
    $params['subject'] = $subject;
}

$sql .= ' ORDER BY u.full_name ASC, g.created_at DESC';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$grades = $stmt->fetchAll();
$subjects = db()->query('SELECT name FROM subjects ORDER BY name')->fetchAll();

require 'includes/header.php';
?>
<h2>Онлайн-журнал</h2>
<p class="text-muted">Учитель видит и редактирует оценки всех учеников, ученик видит только свои оценки.</p>
<form method="get" class="row g-2 mb-3">
    <div class="col-md-4"><select class="form-select" name="subject"><option value="">Все предметы</option><?php foreach($subjects as $s): ?><option <?= $subject===$s['name']?'selected':'' ?>><?= h($s['name']) ?></option><?php endforeach; ?></select></div>
    <?php if ($canManageAllGrades): ?>
    <div class="col-md-2"><input name="class_num" type="number" min="1" max="11" class="form-control" placeholder="Класс" value="<?= $classNum ?: '' ?>"></div>
    <?php endif; ?>
    <div class="col-md-2"><button class="btn btn-primary">Применить</button></div>
</form>
<table class="table table-striped table-sm">
<thead><tr><th>Ученик</th><th>Класс</th><th>Предмет</th><th>Оценка</th><th>Комментарий</th><th>Учитель</th><th>Дата</th></tr></thead>
<tbody>
<?php foreach($grades as $g): ?>
<tr>
<td><?= h($g['student_name']) ?></td>
<td><?= (int)$g['class_num'] ?></td>
<td><?= h($g['subject_name']) ?></td>
<td>
    <?php if ($canManageAllGrades): ?>
        <form method="post" class="d-flex gap-1">
            <input type="hidden" name="grade_id" value="<?= (int)$g['id'] ?>">
            <input type="number" class="form-control form-control-sm" name="grade" min="2" max="5" value="<?= (int)$g['grade'] ?>" style="width: 72px;">
            <input type="text" class="form-control form-control-sm" name="comment" value="<?= h($g['comment']) ?>" placeholder="Комментарий">
            <button class="btn btn-sm btn-dark">Сохранить</button>
        </form>
    <?php else: ?>
        <?= (int)$g['grade'] ?>
    <?php endif; ?>
</td>
<td><?= h($g['comment']) ?></td>
<td><?= h($g['teacher_name']) ?></td>
<td><?= h($g['created_at']) ?></td>
</tr>
<?php endforeach; ?>
</tbody></table>
<?php require 'includes/footer.php'; ?>
