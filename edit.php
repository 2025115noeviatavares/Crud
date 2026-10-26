<?php
// edit.php — Formuláriu edit dadus estudante

require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';

$id = get_id($_GET);
if ($id === null) {
    redirect('index.php');
}

$stmt = $conn->prepare(
    'SELECT id, nre, naran, email, aldeia, suku, postu, munisipiu FROM estudantes WHERE id = ?'
);
$stmt->bind_param('i', $id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$student) {
    flash('error', 'Dadus estudante la hetan.');
    redirect('index.php');
}

$old = take_old();

$pageTitle   = 'Edit Estudante';
$active      = '';
$action      = 'update.php';
$submitLabel = 'Atualiza';
$studentId   = $student['id'];
$values      = $old ?: $student;

require __DIR__ . '/includes/header.php';
?>

<div class="page-head">
    <div>
        <h1>Edit Dadus Estudante</h1>
        <p class="muted">Troka dadus no klik <strong>Atualiza</strong>.</p>
    </div>
</div>

<?php require __DIR__ . '/includes/student_form.php'; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
