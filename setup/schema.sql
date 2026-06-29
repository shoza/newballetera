-- Database Schema for New Ballet Era

CREATE TABLE IF NOT EXISTS productions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    tagline VARCHAR(255) NULL,
    date DATE NULL,
    image_url VARCHAR(255) NULL,
    gallery_images TEXT NULL,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

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

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample data
INSERT IGNORE INTO productions (title, slug, tagline, date, image_url, description) VALUES
('Legendary Women', 'legendary-women', 'Celebrating extraordinary women who changed the world', '2026-10-15', 'img/legendary.jpg',
'Legendary Women is an original full-length ballet celebrating extraordinary women who changed the world through art, fashion, music, ballet, sports, science, and leadership. Through a series of powerful and visually captivating scenes, the ballet honors iconic women whose achievements transcended their fields and continue to inspire generations around the world.');
