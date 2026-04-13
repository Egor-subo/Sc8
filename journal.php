<?php
require_once 'config.php';
require_once 'includes/auth.php';
require_login();

$user = current_user();
$subject = trim($_GET['subject'] ?? '');
$classNum = (int)($_GET['class_num'] ?? 0);

$sql = 'SELECT g.id, g.grade, g.comment, g.created_at, s.name as subject_name,
               u.full_name as student_name, u.class_num, t.full_name as teacher_name
        FROM grades g
        JOIN subjects s ON s.id = g.subject_id
        JOIN users u ON u.id = g.student_id
        JOIN users t ON t.id = g.teacher_id WHERE 1=1';
$params = [];

if ($user['role'] === 'student') {
    $sql .= ' AND g.student_id = :student_id';
    $params['student_id'] = $user['id'];
}
if ($subject !== '') {
    $sql .= ' AND s.name = :subject';
    $params['subject'] = $subject;
}
if (has_role(['teacher','admin','director','vice_director']) && $classNum >=1 && $classNum <=11) {
    $sql .= ' AND u.class_num = :class_num';
    $params['class_num'] = $classNum;
}

$sql .= ' ORDER BY u.full_name ASC, g.created_at DESC';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$grades = $stmt->fetchAll();
$subjects = db()->query('SELECT name FROM subjects ORDER BY name')->fetchAll();

require 'includes/header.php';
?>
<h2>Онлайн-журнал</h2>
<form method="get" class="row g-2 mb-3">
    <div class="col-md-4"><select class="form-select" name="subject"><option value="">Все предметы</option><?php foreach($subjects as $s): ?><option <?= $subject===$s['name']?'selected':'' ?>><?= h($s['name']) ?></option><?php endforeach; ?></select></div>
    <?php if (has_role(['teacher','admin','director','vice_director'])): ?>
    <div class="col-md-2"><input name="class_num" type="number" min="1" max="11" class="form-control" placeholder="Класс" value="<?= $classNum ?: '' ?>"></div>
    <?php endif; ?>
    <div class="col-md-2"><button class="btn btn-primary">Применить</button></div>
</form>
<table class="table table-striped table-sm">
<thead><tr><th>Ученик</th><th>Класс</th><th>Предмет</th><th>Оценка</th><th>Комментарий</th><th>Учитель</th><th>Дата</th></tr></thead>
<tbody>
<?php foreach($grades as $g): ?>
<tr>
<td><?= h($g['student_name']) ?></td><td><?= (int)$g['class_num'] ?></td><td><?= h($g['subject_name']) ?></td><td><?= (int)$g['grade'] ?></td><td><?= h($g['comment']) ?></td><td><?= h($g['teacher_name']) ?></td><td><?= h($g['created_at']) ?></td>
</tr>
<?php endforeach; ?>
</tbody></table>
<?php require 'includes/footer.php'; ?>
