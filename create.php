<?php
// create.php — Formuláriu aumenta estudante foun

require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';

$pageTitle   = 'Aumenta Estudante';
$active      = 'aumenta';
$action      = 'save.php';
$submitLabel = 'Rai Dadus';
$values      = take_old();

require __DIR__ . '/includes/header.php';
?>

<div class="page-head">
    <div>
        <h1>Formuláriu Rejistu Estudante</h1>
        <p class="muted">Prense dadus estudante foun iha kraik.</p>
    </div>
</div>

<?php require __DIR__ . '/includes/student_form.php'; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
