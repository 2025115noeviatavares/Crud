<?php
// update.php — Atualiza dadus estudante (UPDATE)

require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';

require_post();
csrf_check();

$id = get_id($_POST);
if ($id === null) {
    flash('error', 'ID estudante la válidu.');
    redirect('index.php');
}

$data   = input_data();
$errors = validate($data);

if ($errors) {
    flash('error', implode(' ', $errors));
    $_SESSION['old'] = $data;
    redirect('edit.php?id=' . $id);
}

try {
    $stmt = $conn->prepare(
        'UPDATE estudantes
            SET nre = ?, naran = ?, email = ?, aldeia = ?, suku = ?, postu = ?, munisipiu = ?
          WHERE id = ?'
    );
    $stmt->bind_param(
        'sssssssi',
        $data['nre'], $data['naran'], $data['email'],
        $data['aldeia'], $data['suku'], $data['postu'], $data['munisipiu'],
        $id
    );
    $stmt->execute();
    $stmt->close();
} catch (mysqli_sql_exception $ex) {
    if ($ex->getCode() === 1062) {
        flash('error', 'NRE ' . $data['nre'] . ' uza ona husi estudante seluk.');
    } else {
        error_log($ex->getMessage());
        flash('error', 'Falla atualiza dadus. Favór koko fali.');
    }
    $_SESSION['old'] = $data;
    redirect('edit.php?id=' . $id);
}

flash('success', 'Dadus estudante atualiza ona ho susesu.');
redirect('index.php');
