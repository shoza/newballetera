<?php
require_once __DIR__ . '/auth.php';
requireLogin();
require_once __DIR__ . '/../config/database.php';

$error = '';
$success = '';
$production = null;

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: index.php");
    exit;
}

// Fetch existing production
if ($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM productions WHERE id = ?");
    $stmt->execute([$id]);
    $production = $stmt->fetch();
}

if (!$production) {
    echo "Production not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $date = trim($_POST['date'] ?? '');
    $image_url = trim($_POST['image_url'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $gallery_images = trim($_POST['gallery_images'] ?? '');

    if (empty($title) || empty($image_url)) {
        $error = "Title and Image URL are required.";
    } else {
        if ($pdo) {
            $stmt = $pdo->prepare("UPDATE productions SET title = ?, date = ?, image_url = ?, description = ?, gallery_images = ? WHERE id = ?");
            $dateVal = empty($date) ? null : $date;
            $galleryVal = empty($gallery_images) ? null : $gallery_images;

            if ($stmt->execute([$title, $dateVal, $image_url, $description, $galleryVal, $id])) {
                $success = "Production updated successfully!";
                $production['title'] = $title;
                $production['date'] = $dateVal;
                $production['image_url'] = $image_url;
                $production['description'] = $description;
                $production['gallery_images'] = $galleryVal;
            } else {
                $error = "Failed to update production.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Production - Admin</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body class="dashboard-body">
    <a href="index.php" class="back">&larr; Back to Dashboard</a>
    <div class="form-box">
        <h2>Edit Production</h2>
        <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
        <?php if ($success): ?><p class="success"><?= htmlspecialchars($success) ?></p><?php endif; ?>
        
        <form method="post">
            <label>Title *</label>
            <input type="text" name="title" value="<?= htmlspecialchars($production['title']) ?>" required>

            <label>Date (Leave empty for TBA/Upcoming)</label>
            <input type="date" name="date" value="<?= $production['date'] ? htmlspecialchars($production['date']) : '' ?>">

            <label>Cover Image *</label>
            <input type="hidden" name="image_url" id="image_url_input" value="<?= htmlspecialchars($production['image_url']) ?>" required>
            
            <div id="drop-zone" class="drop-zone">
                <span class="drop-text" style="<?= $production['image_url'] ? 'display:none;' : '' ?>">Drag & Drop Image Here or Click to Upload</span>
                <input type="file" id="file_input" accept="image/*" style="display:none;">
                <img id="image_preview" src="<?= $production['image_url'] ? '../' . htmlspecialchars($production['image_url']) : '' ?>" style="<?= $production['image_url'] ? 'display:block;' : 'display:none;' ?>" alt="Preview">
            </div>

            <div class="history-gallery" id="history_gallery">
                <!-- History thumbnails will be injected here -->
            </div>

            <label>Gallery Photos (for mosaic grid)</label>
            <input type="hidden" name="gallery_images" id="gallery_images_input" value="<?= htmlspecialchars($production['gallery_images'] ?? '') ?>">
            <div id="gallery-drop-zone" class="drop-zone" style="min-height:80px;">
                <span class="drop-text">Drag & Drop Photos Here or Click to Upload (multiple)</span>
                <input type="file" id="gallery_file_input" accept="image/*" multiple style="display:none;">
            </div>
            <div id="gallery-preview" style="display:flex;flex-wrap:wrap;gap:8px;margin-top:10px;">
                <?php
                $existingGallery = json_decode($production['gallery_images'] ?? '[]', true) ?: [];
                foreach ($existingGallery as $gImg): ?>
                    <div class="gallery-thumb-wrap" data-path="<?= htmlspecialchars($gImg) ?>">
                        <img src="../<?= htmlspecialchars($gImg) ?>" style="width:80px;height:80px;object-fit:cover;">
                        <button type="button" onclick="removeGalleryImg(this)" style="display:block;width:80px;font-size:11px;background:#c00;color:#fff;border:none;cursor:pointer;">Remove</button>
                    </div>
                <?php endforeach; ?>
            </div>

            <label>Description</label>
            <textarea name="description" rows="5"><?= htmlspecialchars($production['description'] ?? '') ?></textarea>

            <button type="submit">Update Production</button>
        </form>
    </div>

    <script>
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('file_input');
        const imagePreview = document.getElementById('image_preview');
        const imageUrlInput = document.getElementById('image_url_input');
        const dropText = dropZone.querySelector('.drop-text');
        const historyGallery = document.getElementById('history_gallery');

        // Handle Click to upload
        dropZone.addEventListener('click', () => fileInput.click());

        // Handle Drag & Drop
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });
        dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            if (e.dataTransfer.files.length) {
                uploadImage(e.dataTransfer.files[0]);
            }
        });

        // Handle File Selection
        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length) {
                uploadImage(e.target.files[0]);
            }
        });

        async function uploadImage(file) {
            const formData = new FormData();
            formData.append('image', file);
            dropText.style.display = 'block';
            dropText.textContent = "Uploading...";

            try {
                const res = await fetch('upload_image.php', { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) {
                    setPreview(data.path);
                    loadHistory();
                } else {
                    alert(data.error);
                    dropText.textContent = "Drag & Drop Image Here or Click to Upload";
                }
            } catch (err) {
                alert("Upload failed.");
                dropText.textContent = "Drag & Drop Image Here or Click to Upload";
            }
        }

        function setPreview(path) {
            imageUrlInput.value = path;
            imagePreview.src = '../' + path;
            imagePreview.style.display = 'block';
            dropText.style.display = 'none';
        }

        // Load History
        async function loadHistory() {
            try {
                const res = await fetch('get_images.php');
                const data = await res.json();
                if (data.success && data.images.length > 0) {
                    historyGallery.innerHTML = '';
                    data.images.forEach(path => {
                        const img = document.createElement('img');
                        img.src = '../' + path;
                        img.className = 'history-thumb';
                        img.onclick = (e) => {
                            e.stopPropagation();
                            setPreview(path);
                        };
                        historyGallery.appendChild(img);
                    });
                }
            } catch (err) {
                console.error("Failed to load history", err);
            }
        }

        loadHistory();

        // Gallery upload
        const galleryDropZone = document.getElementById('gallery-drop-zone');
        const galleryFileInput = document.getElementById('gallery_file_input');
        const galleryPreview = document.getElementById('gallery-preview');
        const galleryInput = document.getElementById('gallery_images_input');

        galleryDropZone.addEventListener('click', () => galleryFileInput.click());
        galleryDropZone.addEventListener('dragover', e => { e.preventDefault(); galleryDropZone.classList.add('dragover'); });
        galleryDropZone.addEventListener('dragleave', () => galleryDropZone.classList.remove('dragover'));
        galleryDropZone.addEventListener('drop', e => {
            e.preventDefault();
            galleryDropZone.classList.remove('dragover');
            [...e.dataTransfer.files].forEach(uploadGalleryImage);
        });
        galleryFileInput.addEventListener('change', e => [...e.target.files].forEach(uploadGalleryImage));

        function getGalleryPaths() {
            return [...galleryPreview.querySelectorAll('.gallery-thumb-wrap')].map(el => el.dataset.path);
        }

        function updateGalleryInput() {
            galleryInput.value = JSON.stringify(getGalleryPaths());
        }

        async function uploadGalleryImage(file) {
            const formData = new FormData();
            formData.append('image', file);
            try {
                const res = await fetch('upload_image.php', { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) {
                    const wrap = document.createElement('div');
                    wrap.className = 'gallery-thumb-wrap';
                    wrap.dataset.path = data.path;
                    wrap.innerHTML = `<img src="../${data.path}" style="width:80px;height:80px;object-fit:cover;"><button type="button" onclick="removeGalleryImg(this)" style="display:block;width:80px;font-size:11px;background:#c00;color:#fff;border:none;cursor:pointer;">Remove</button>`;
                    galleryPreview.appendChild(wrap);
                    updateGalleryInput();
                } else {
                    alert(data.error);
                }
            } catch (err) {
                alert("Upload failed.");
            }
        }

        window.removeGalleryImg = function(btn) {
            btn.closest('.gallery-thumb-wrap').remove();
            updateGalleryInput();
        };

        updateGalleryInput();
    </script>
</body>
</html>
