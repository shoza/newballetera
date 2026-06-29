<?php
// Site-wide configuration

// Admin email: all application submissions are sent here
define('ADMIN_EMAIL', 'yarikfarifonov@gmail.com');

// Site name (used in emails)
define('SITE_NAME', 'New Ballet Era');

// Upload directory (relative to project root)
define('UPLOAD_DIR', __DIR__ . '/../uploads/');

// Max upload sizes (bytes)
define('MAX_HEADSHOT_SIZE', 5 * 1024 * 1024);  // 5 MB
define('MAX_PHOTOS_SIZE', 10 * 1024 * 1024); // 10 MB each
define('MAX_DOC_SIZE', 5 * 1024 * 1024);  // 5 MB

// Allowed MIME types for documents
define('ALLOWED_DOC_TYPES', [
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
]);

// Allowed MIME types for images
define('ALLOWED_IMG_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
