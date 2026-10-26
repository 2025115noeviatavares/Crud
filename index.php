<?php
// index.php — Lista estudante (ho buka no pajinasaun)

require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';

$perPage = 10;
$q       = (isset($_GET['q']) && is_string($_GET['q'])) ? trim($_GET['q']) : '';
$page    = max(1, (int) ($_GET['page'] ?? 1));

$where = '';
$like  = '';
if ($q !== '') {
    $where = 'WHERE nre LIKE ? OR naran LIKE ? OR email LIKE ?';
    $like  = '%' . addcslashes($q, '%_\\') . '%';
}

// 1) Total dadus
$stmt = $conn->prepare("SELECT COUNT(*) FROM estudantes $where");
if ($where !== '') {
    $stmt->bind_param('sss', $like, $like, $like);
}
$stmt->execute();
$total = (int) $stmt->get_result()->fetch_row()[0];
$stmt->close();

$totalPages = max(1, (int) ceil($total / $perPage));
$page       = min($page, $totalPages);
$offset     = ($page - 1) * $perPage;

// 2) Foti deit dadus ba pájina ida ne'e (LIMIT) — lais liu
$sql = "SELECT id, nre, naran, email, aldeia, suku, postu, munisipiu
        FROM estudantes $where
        ORDER BY id DESC
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
if ($where !== '') {
    $stmt->bind_param('sssii', $like, $like, $like, $perPage, $offset);
} else {
    $stmt->bind_param('ii', $perPage, $offset);
}
$stmt->execute();
$students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$pageTitle = 'Lista Estudante';
$active    = 'lista';
require __DIR__ . '/includes/header.php';
?>

<div class="page-head">
    <div>
        <h1>Lista Estudante</h1>
        <p class="muted">Total: <strong><?= $total ?></strong> estudante<?= $q !== '' ? ' (rezultadu buka)' : '' ?></p>
    </div>
    <a href="create.php" class="btn btn--primary">+ Aumenta Estudante</a>
</div>

<form method="GET" action="index.php" class="card search">
    <input type="search" name="q" value="<?= e($q) ?>" placeholder="Buka tuir NRE, naran ka email..." aria-label="Buka">
    <button type="submit" class="btn btn--primary">Buka</button>
    <?php if ($q !== ''): ?>
        <a href="index.php" class="btn btn--ghost">Hamos</a>
    <?php endif; ?>
</form>

<div class="card table-wrap">
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>NRE</th>
                <th>Naran</th>
                <th>Email</th>
                <th>Aldeia</th>
                <th>Suku</th>
                <th>Postu</th>
                <th>Munisipiu</th>
                <th class="col-actions">Aksaun</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!$students): ?>
            <tr>
                <td colspan="9" class="empty">
                    <?= $q !== '' ? 'La hetan dadus ne\'ebé kompatível ho buka.' : 'Seidauk iha estudante rejistu.' ?>
                </td>
            </tr>
        <?php endif; ?>

        <?php foreach ($students as $i => $row): ?>
            <tr>
                <td><?= $offset + $i + 1 ?></td>
                <td><span class="badge"><?= e($row['nre']) ?></span></td>
                <td><?= e($row['naran']) ?></td>
                <td><?= e($row['email']) ?></td>
                <td><?= e($row['aldeia']) ?></td>
                <td><?= e($row['suku']) ?></td>
                <td><?= e($row['postu']) ?></td>
                <td><?= e($row['munisipiu']) ?></td>
                <td class="col-actions">
                    <a href="edit.php?id=<?= (int) $row['id'] ?>" class="btn btn--edit btn--sm">Edit</a>

                    <form action="delete.php" method="POST" class="inline"
                          onsubmit="return confirm('Hakarak hamos dadus ida ne\'e?')">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                        <button type="submit" class="btn btn--danger btn--sm">Hamos</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if ($totalPages > 1): ?>
    <nav class="pagination" aria-label="Pájina">
        <?php if ($page > 1): ?>
            <a href="<?= e(page_url($page - 1, $q)) ?>">&laquo; Antes</a>
        <?php endif; ?>

        <?php for ($p = max(1, $page - 2); $p <= min($totalPages, $page + 2); $p++): ?>
            <?php if ($p === $page): ?>
                <span class="is-current"><?= $p ?></span>
            <?php else: ?>
                <a href="<?= e(page_url($p, $q)) ?>"><?= $p ?></a>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="<?= e(page_url($page + 1, $q)) ?>">Tuir &raquo;</a>
        <?php endif; ?>
    </nav>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
