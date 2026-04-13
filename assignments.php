<?php
require_once 'config.php';
require_once 'includes/auth.php';
require_login();

$user = current_user();
$subject = trim($_GET['subject'] ?? '');
$classFilter = (int)($_GET['class_num'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && has_role(['teacher', 'admin', 'director', 'vice_director'])) {
    $stmt = db()->prepare('INSERT INTO assignments(subject_id, class_num, teacher_id, title, description, due_date) VALUES(:subject,:class_num,:teacher,:title,:description,:due_date)');
    $stmt->execute([
        'subject' => (int)$_POST['subject_id'],
        'class_num' => (int)$_POST['class_num'],
        'teacher' => $user['id'],
        'title' => trim($_POST['title']),
        'description' => trim($_POST['description']),
        'due_date' => $_POST['due_date'],
    ]);
    header('Location: assignments.php');
    exit;
}

$query = 'SELECT a.*, s.name AS subject_name, u.full_name AS teacher_name FROM assignments a
          JOIN subjects s ON s.id = a.subject_id
          JOIN users u ON u.id = a.teacher_id WHERE 1=1';
$params = [];
if ($subject !== '') { $query .= ' AND s.name = :subject'; $params['subject'] = $subject; }
if ($classFilter >= 1 && $classFilter <= 11) { $query .= ' AND a.class_num = :class_num'; $params['class_num'] = $classFilter; }
$query .= ' ORDER BY a.due_date ASC';
$stmt = db()->prepare($query);
$stmt->execute($params);
$assignments = $stmt->fetchAll();
$subjects = db()->query('SELECT * FROM subjects ORDER BY name')->fetchAll();

require 'includes/header.php';
?>
<h2>Задания и тесты</h2>
<form method="get" class="row g-2 mb-3">
    <div class="col-md-4"><select name="subject" class="form-select"><option value="">Все предметы</option><?php foreach($subjects as $s): ?><option <?= $subject===$s['name']?'selected':'' ?>><?= h($s['name']) ?></option><?php endforeach; ?></select></div>
    <div class="col-md-2"><input class="form-control" type="number" min="1" max="11" name="class_num" value="<?= $classFilter ?: '' ?>" placeholder="Класс"></div>
    <div class="col-md-2"><button class="btn btn-primary">Фильтр</button></div>
</form>

<?php if (has_role(['teacher', 'admin', 'director', 'vice_director'])): ?>
<div class="card mb-3"><div class="card-body"><h5>Добавить задание</h5>
<form method="post" class="row g-2">
<div class="col-md-3"><select name="subject_id" class="form-select" required><?php foreach($subjects as $s): ?><option value="<?= (int)$s['id'] ?>"><?= h($s['name']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-1"><input name="class_num" type="number" min="1" max="11" class="form-control" required></div>
<div class="col-md-3"><input name="title" class="form-control" placeholder="Заголовок" required></div>
<div class="col-md-3"><input name="description" class="form-control" placeholder="Описание" required></div>
<div class="col-md-2"><input type="date" name="due_date" class="form-control" required></div>
<div class="col-12"><button class="btn btn-dark">Сохранить</button></div>
</form></div></div>
<?php endif; ?>

<?php foreach($assignments as $a): ?>
<div class="card mb-2"><div class="card-body">
    <h6><?= h($a['title']) ?> <span class="badge text-bg-secondary"><?= h($a['subject_name']) ?></span></h6>
    <p class="mb-1"><?= h($a['description']) ?></p>
    <small>Класс <?= (int)$a['class_num'] ?> • Срок сдачи: <?= h($a['due_date']) ?> • Учитель: <?= h($a['teacher_name']) ?></small>
</div></div>
<?php endforeach; ?>
<?php require 'includes/footer.php'; ?>
