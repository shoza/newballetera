<?php
require_once __DIR__ . '/auth.php';
requireLogin();
require_once __DIR__ . '/../config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $date = trim($_POST['date'] ?? '');
    $image_url = trim($_POST['image_url'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // Basic validation
    if (empty($title) || empty($image_url)) {
        $error = "Title and Image URL are required.";
    } else {
        if ($pdo) {
            $stmt = $pdo->prepare("INSERT INTO productions (title, date, image_url, description) VALUES (?, ?, ?, ?)");
            // Convert empty date to NULL
            $dateVal = empty($date) ? null : $date;
            
            if ($stmt->execute([$title, $dateVal, $image_url, $description])) {
                $success = "Production added successfully!";
            } else {
                $error = "Failed to add production.";
            }
        } else {
            $error = "Database connection error.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Production - Admin</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body class="dashboard-body">
    <a href="index.php" class="back">&larr; Back to Dashboard</a>
    <div class="form-box">
        <h2>Add New Production</h2>
        <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
        <?php if ($success): ?><p class="success"><?= htmlspecialchars($success) ?></p><?php endif; ?>
        
        <form method="post">
            <label>Title *</label>
            <input type="text" name="title" required>

            <label>Date (Leave empty for TBA/Upcoming)</label>
            <input type="date" name="date">

            <label>Cover Image *</label>
            <input type="hidden" name="image_url" id="image_url_input" required>
            
            <div id="drop-zone" class="drop-zone">
                <span class="drop-text">Drag & Drop Image Here or Click to Upload</span>
                <input type="file" id="file_input" accept="image/*" style="display:none;">
                <img id="image_preview" src="" style="display:none;" alt="Preview">
            </div>

            <div class="history-gallery" id="history_gallery">
                <!-- History thumbnails will be injected here -->
            </div>

            <label>Description</label>
            <textarea name="description" rows="5"></textarea>

            <button type="submit">Save Production</button>
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
            dropText.textContent = "Uploading...";

            try {
                const res = await fetch('upload_image.php', { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) {
                    setPreview(data.path);
                    loadHistory(); // refresh history
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

        // Initialize history on load
        loadHistory();
    </script>
</body>
</html>
