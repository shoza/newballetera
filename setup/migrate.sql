-- Migration: add new columns to existing productions table
ALTER TABLE productions
    ADD COLUMN IF NOT EXISTS slug VARCHAR(100) NULL,
    ADD COLUMN IF NOT EXISTS tagline VARCHAR(255) NULL,
    ADD COLUMN IF NOT EXISTS gallery_images TEXT NULL;

-- Add unique index on slug if not already present
ALTER TABLE productions ADD UNIQUE INDEX IF NOT EXISTS idx_slug (slug);

-- Set slug for existing Legendary Women row
UPDATE productions SET slug = 'legendary-women', tagline = 'Celebrating extraordinary women who changed the world'
WHERE title = 'Legendary Women' AND (slug IS NULL OR slug = '');

-- Create application tables
CREATE TABLE IF NOT EXISTS dancer_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    headshot_path VARCHAR(500) NULL,
    dance_photos TEXT NULL,
    resume_path VARCHAR(500) NULL,
    video_link_1 VARCHAR(500) NOT NULL,
    video_link_2 VARCHAR(500) NOT NULL,
    video_link_3 VARCHAR(500) NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS choreographer_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    message TEXT NULL,
    resume_path VARCHAR(500) NULL,
    cover_letter_path VARCHAR(500) NULL,
    bio_path VARCHAR(500) NULL,
    video_link_1 VARCHAR(500) NOT NULL,
    video_link_2 VARCHAR(500) NOT NULL,
    video_link_3 VARCHAR(500) NOT NULL,
    artistic_statement TEXT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
