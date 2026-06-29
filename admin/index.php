<?php
require_once __DIR__ . '/auth.php';
requireLogin();
require_once __DIR__ . '/../config/database.php';

$productions = [];
if ($pdo) {
    $stmt = $pdo->query("SELECT * FROM productions ORDER BY date DESC, id DESC");
    $productions = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - New Ballet Era</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body class="dashboard-body">
    <div class="header">
        <h1>Admin Dashboard</h1>
        <div>
            <a href="add_production.php" class="btn">+ Add Production</a>
            <a href="logout.php" style="margin-left: 15px;">Logout</a>
        </div>
    </div>

    <h2>Manage Productions</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Title</th>
                <th>Date</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($productions as $p): ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td><img src="../<?= htmlspecialchars($p['image_url']) ?>" class="thumb" alt="Thumb"></td>
                <td><?= htmlspecialchars($p['title']) ?></td>
                <td><?= $p['date'] ? htmlspecialchars($p['date']) : 'TBA' ?></td>
                <td><?= htmlspecialchars(substr($p['description'] ?? '', 0, 50)) ?>...</td>
                <td>
                    <!-- Edit/Delete links -->
                    <a href="edit_production.php?id=<?= $p['id'] ?>">Edit</a> | 
                    <a href="delete_production.php?id=<?= $p['id'] ?>" style="color:#ff5555;" onclick="return confirm('Are you sure you want to delete this production?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($productions)): ?>
            <tr><td colspan="6" style="text-align:center;">No productions found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
