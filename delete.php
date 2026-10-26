<?php
// delete.php — Hamos estudante (DELETE) — uza POST + CSRF, la uza GET

require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';

require_post();
csrf_check();

$id = get_id($_POST);
if ($id === null) {
    flash('error', 'ID estudante la válidu.');
    redirect('index.php');
}

try {
    $stmt = $conn->prepare('DELETE FROM estudantes WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $deleted = $stmt->affected_rows > 0;
    $stmt->close();
} catch (mysqli_sql_exception $ex) {
    error_log($ex->getMessage());
    flash('error', 'Falla hamos dadus. Favór koko fali.');
    redirect('index.php');
}

if ($deleted) {
    flash('success', 'Dadus estudante hamos ona.');
} else {
    flash('error', 'Dadus la hetan ka hamos ona.');
}
redirect('index.php');
