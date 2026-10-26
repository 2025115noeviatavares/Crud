<?php
// save.php — Rai estudante foun (INSERT)

require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';

require_post();
csrf_check();

$data   = input_data();
$errors = validate($data);

if ($errors) {
    flash('error', implode(' ', $errors));
    $_SESSION['old'] = $data;
    redirect('create.php');
}

try {
    $stmt = $conn->prepare(
        'INSERT INTO estudantes (nre, naran, email, aldeia, suku, postu, munisipiu)
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->bind_param(
        'sssssss',
        $data['nre'], $data['naran'], $data['email'],
        $data['aldeia'], $data['suku'], $data['postu'], $data['munisipiu']
    );
    $stmt->execute();
    $stmt->close();
} catch (mysqli_sql_exception $ex) {
    if ($ex->getCode() === 1062) {
        flash('error', 'NRE ' . $data['nre'] . ' iha ona. Uza NRE seluk.');
    } else {
        error_log($ex->getMessage());
        flash('error', 'Falla rai dadus. Favór koko fali.');
    }
    $_SESSION['old'] = $data;
    redirect('create.php');
}

flash('success', 'Estudante "' . $data['naran'] . '" rai ona ho susesu.');
redirect('index.php');
