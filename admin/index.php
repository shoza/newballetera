<?php
require_once __DIR__ . '/auth.php';
requireLogin();
require_once __DIR__ . '/../config/database.php';

// "Back to website" target: the last public page visited (set by index.php).
// Only accept same-origin relative paths ("/…", not "//…") to avoid open redirects.
$back_url = $_COOKIE['nbe_last_page'] ?? '';
if ($back_url === '' || $back_url[0] !== '/' || strncmp($back_url, '//', 2) === 0) {
    $back_url = '../';
}

$productions = $dancers = $choreos = [];
if ($pdo) {
    $productions = $pdo->query("SELECT * FROM productions ORDER BY date DESC, id DESC")->fetchAll();
    $dancers     = $pdo->query("SELECT * FROM dancer_applications ORDER BY submitted_at DESC")->fetchAll();
    $choreos     = $pdo->query("SELECT * FROM choreographer_applications ORDER BY submitted_at DESC")->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - New Ballet Era</title>
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        .tabs { display:flex; gap:4px; margin-bottom:20px; border-bottom:1px solid #444; }
        .tab-btn { background:#222; color:#aaa; border:none; padding:10px 20px; cursor:pointer; font-size:14px; border-radius:4px 4px 0 0; }
        .tab-btn.active { background:#fff; color:#000; font-weight:bold; }
        .tab-panel { display:none; }
        .tab-panel.active { display:block; }
        .badge { background:#555; color:#fff; border-radius:10px; font-size:11px; padding:2px 7px; margin-left:5px; }
        .video-links a { display:block; color:#7af; font-size:12px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:180px; }

        /* file column */
        .files-col { display:flex; flex-direction:column; gap:4px; }
        a.file-link { display:block; color:#7af; font-size:12px; padding:5px 10px; background:rgba(100,170,255,0.08); border-radius:3px; cursor:pointer; text-decoration:none; white-space:nowrap; }
        a.file-link:hover { background:rgba(100,170,255,0.2); }

        /* headshot thumbnail */
        .app-headshot { width:46px; height:46px; object-fit:cover; border-radius:4px; cursor:pointer; display:block; }

        /* file preview modal */
        .file-modal { position:fixed; inset:0; background:rgba(0,0,0,0.92); z-index:9999; display:flex; align-items:center; justify-content:center; opacity:0; pointer-events:none; transition:opacity .18s; }
        .file-modal.visible { opacity:1; pointer-events:all; }
        .file-modal-box { position:relative; max-width:90vw; max-height:92vh; background:#111; border-radius:6px; overflow:auto; display:flex; align-items:center; justify-content:center; min-width:200px; min-height:100px; }
        .file-modal-close { position:fixed; top:14px; right:18px; background:rgba(40,40,40,0.9); border:1px solid #444; color:#fff; font-size:22px; line-height:1; width:36px; height:36px; border-radius:50%; cursor:pointer; display:flex; align-items:center; justify-content:center; }
        .badge-paid    { background:#1a7a3a; color:#fff; font-size:11px; padding:2px 8px; border-radius:10px; }
        .badge-pending { background:#7a5a1a; color:#fff; font-size:11px; padding:2px 8px; border-radius:10px; }
    </style>
</head>
<body class="dashboard-body">
    <div class="header">
        <h1>Admin Dashboard</h1>
        <div>
            <a href="<?= htmlspecialchars($back_url) ?>" class="btn">← Back to Website</a>
            <a href="add_production.php" class="btn" style="margin-left:8px;">+ Add Production</a>
            <a href="logout.php" style="margin-left:15px;">Logout</a>
        </div>
    </div>

    <div class="tabs">
        <button class="tab-btn active" onclick="showTab('productions')">
            Productions <span class="badge"><?= count($productions) ?></span>
        </button>
        <button class="tab-btn" onclick="showTab('dancers')">
            Dancer Applications <span class="badge"><?= count($dancers) ?></span>
        </button>
        <button class="tab-btn" onclick="showTab('choreos')">
            Choreographer Applications <span class="badge"><?= count($choreos) ?></span>
        </button>
    </div>

    <!-- Productions -->
    <div id="tab-productions" class="tab-panel active">
        <table>
            <thead>
                <tr><th>ID</th><th>Image</th><th>Title</th><th>Date</th><th>Description</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($productions as $p): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td><img src="../<?= htmlspecialchars($p['image_url'] ?? '') ?>" class="thumb" alt=""></td>
                    <td><?= htmlspecialchars($p['title']) ?></td>
                    <td><?= $p['date'] ? htmlspecialchars($p['date']) : 'TBA' ?></td>
                    <td><?= htmlspecialchars(substr($p['description'] ?? '', 0, 50)) ?>…</td>
                    <td>
                        <a href="edit_production.php?id=<?= $p['id'] ?>">Edit</a> |
                        <a href="delete_production.php?id=<?= $p['id'] ?>" style="color:#ff5555;"
                           onclick="return confirm('Delete this production?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($productions)): ?>
                <tr><td colspan="6" style="text-align:center;">No productions yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Dancer Applications -->
    <div id="tab-dancers" class="tab-panel">
        <table>
            <thead>
                <tr><th>Date</th><th></th><th>Name</th><th>Email</th><th>Phone</th><th>Videos</th><th>Files</th><th></th></tr>
            </thead>
            <tbody>
                <?php foreach ($dancers as $d): ?>
                <tr>
                    <td style="white-space:nowrap;"><?= date('m/d/Y', strtotime($d['submitted_at'])) ?></td>
                    <td style="width:54px;">
                        <?php if ($d['headshot_path']): ?>
                        <img class="app-headshot"
                             src="../<?= htmlspecialchars($d['headshot_path']) ?>"
                             onclick="openFile(<?= htmlspecialchars(json_encode('../' . $d['headshot_path'])) ?>)"
                             alt="">
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= htmlspecialchars($d['full_name']) ?>
                        <?php $ps = $d['payment_status'] ?? 'pending'; ?>
                        <span class="badge-<?= $ps ?>"><?= $ps === 'paid' ? '✓ paid' : 'pending' ?></span>
                    </td>
                    <td><a href="mailto:<?= htmlspecialchars($d['email']) ?>"><?= htmlspecialchars($d['email']) ?></a></td>
                    <td><?= htmlspecialchars($d['phone']) ?></td>
                    <td class="video-links">
                        <a href="<?= htmlspecialchars($d['video_link_1']) ?>" target="_blank">Video 1</a>
                        <a href="<?= htmlspecialchars($d['video_link_2']) ?>" target="_blank">Video 2</a>
                        <a href="<?= htmlspecialchars($d['video_link_3']) ?>" target="_blank">Video 3</a>
                    </td>
                    <td>
                        <div class="files-col">
                        <?php if ($d['headshot_path']): ?>
                            <a class="file-link" onclick="openFile(<?= htmlspecialchars(json_encode('../' . $d['headshot_path'])) ?>)">Headshot</a>
                        <?php endif; ?>
                        <?php if ($d['resume_path']): ?>
                            <a class="file-link" onclick="openFile(<?= htmlspecialchars(json_encode('../' . $d['resume_path'])) ?>)">Résumé</a>
                        <?php endif; ?>
                        <?php
                        $photos = json_decode($d['dance_photos'] ?? '[]', true) ?: [];
                        foreach ($photos as $pi => $ph): ?>
                            <a class="file-link" onclick="openFile(<?= htmlspecialchars(json_encode("../$ph")) ?>)">Photo <?= $pi + 1 ?></a>
                        <?php endforeach; ?>
                        </div>
                    </td>
                    <td>
                        <a href="delete_dancer.php?id=<?= $d['id'] ?>" style="color:#ff5555;"
                           onclick="return confirm('Delete this dancer application?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($dancers)): ?>
                <tr><td colspan="7" style="text-align:center;">No dancer applications yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Choreographer Applications -->
    <div id="tab-choreos" class="tab-panel">
        <table>
            <thead>
                <tr><th>Date</th><th>Name</th><th>Email</th><th>Phone</th><th>Videos</th><th>Files</th><th></th></tr>
            </thead>
            <tbody>
                <?php foreach ($choreos as $c): ?>
                <tr>
                    <td style="white-space:nowrap;"><?= date('m/d/Y', strtotime($c['submitted_at'])) ?></td>
                    <td>
                        <?= htmlspecialchars($c['full_name']) ?>
                        <?php $ps = $c['payment_status'] ?? 'pending'; ?>
                        <span class="badge-<?= $ps ?>"><?= $ps === 'paid' ? '✓ paid' : 'pending' ?></span>
                    </td>
                    <td><a href="mailto:<?= htmlspecialchars($c['email']) ?>"><?= htmlspecialchars($c['email']) ?></a></td>
                    <td><?= htmlspecialchars($c['phone']) ?></td>
                    <td class="video-links">
                        <a href="<?= htmlspecialchars($c['video_link_1']) ?>" target="_blank">Video 1</a>
                        <a href="<?= htmlspecialchars($c['video_link_2']) ?>" target="_blank">Video 2</a>
                        <a href="<?= htmlspecialchars($c['video_link_3']) ?>" target="_blank">Video 3</a>
                    </td>
                    <td>
                        <div class="files-col">
                        <?php if ($c['resume_path']): ?>
                            <a class="file-link" onclick="openFile(<?= htmlspecialchars(json_encode('../' . $c['resume_path'])) ?>)">Résumé</a>
                        <?php endif; ?>
                        <?php if ($c['cover_letter_path']): ?>
                            <a class="file-link" onclick="openFile(<?= htmlspecialchars(json_encode('../' . $c['cover_letter_path'])) ?>)">Cover Letter</a>
                        <?php endif; ?>
                        <?php if ($c['bio_path']): ?>
                            <a class="file-link" onclick="openFile(<?= htmlspecialchars(json_encode('../' . $c['bio_path'])) ?>)">Bio</a>
                        <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <a href="delete_choreographer.php?id=<?= $c['id'] ?>" style="color:#ff5555;"
                           onclick="return confirm('Delete this choreographer application?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($choreos)): ?>
                <tr><td colspan="7" style="text-align:center;">No choreographer applications yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- File preview modal -->
    <div class="file-modal" id="file-modal" onclick="if(event.target===this)closeModal()">
        <button class="file-modal-close" onclick="closeModal()">&#x2715;</button>
        <div class="file-modal-box" id="file-modal-box"></div>
    </div>

    <script>
    function showTab(name, btn) {
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('tab-' + name).classList.add('active');
        (btn || event.currentTarget).classList.add('active');
    }

    // Restore active tab after delete redirect
    const urlTab = new URLSearchParams(location.search).get('tab');
    if (urlTab) {
        const btn = [...document.querySelectorAll('.tab-btn')]
            .find(b => b.getAttribute('onclick')?.includes("'" + urlTab + "'"));
        if (btn) showTab(urlTab, btn);
    }

    // File preview modal
    const IMG_EXTS = ['jpg','jpeg','png','webp','gif'];

    function openFile(url) {
        const ext = url.split('.').pop().toLowerCase();
        const box = document.getElementById('file-modal-box');
        box.innerHTML = '';

        if (IMG_EXTS.includes(ext)) {
            const img = document.createElement('img');
            img.src = url;
            img.style.cssText = 'max-width:88vw;max-height:88vh;display:block;border-radius:4px;';
            box.appendChild(img);
        } else if (ext === 'pdf') {
            const frame = document.createElement('iframe');
            frame.src = url;
            frame.style.cssText = 'width:88vw;height:88vh;border:none;display:block;';
            box.appendChild(frame);
        } else {
            box.style.padding = '48px 64px';
            const msg = document.createElement('div');
            msg.style.cssText = 'text-align:center;color:#aaa;';
            msg.innerHTML = '<p style="margin-bottom:20px;">Preview not available for this file type.</p>';
            const a = document.createElement('a');
            a.href = url; a.download = ''; a.textContent = '⬇ Download file';
            a.style.cssText = 'color:#7af;font-size:1rem;';
            msg.appendChild(a);
            box.appendChild(msg);
        }

        document.getElementById('file-modal').classList.add('visible');
    }

    function closeModal() {
        document.getElementById('file-modal').classList.remove('visible');
        document.getElementById('file-modal-box').innerHTML = '';
    }

    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
    </script>
</body>
</html>
