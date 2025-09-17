-- Sauvegarde des données existantes
CREATE TEMPORARY TABLE temp_donations AS SELECT * FROM donations;

-- Table des créatrices
CREATE TABLE IF NOT EXISTS creators (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    tagline VARCHAR(255),
    description TEXT,
    profile_pic_url VARCHAR(255),
    banner_url VARCHAR(255),
    donation_goal DECIMAL(10,2) DEFAULT 500.00,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des liens des créatrices
CREATE TABLE IF NOT EXISTS creator_links (
    id INT PRIMARY KEY AUTO_INCREMENT,
    creator_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    url VARCHAR(512) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (creator_id) REFERENCES creators(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des packs
CREATE TABLE IF NOT EXISTS packs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    creator_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image_url VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (creator_id) REFERENCES creators(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Suppression de la table donations existante
DROP TABLE IF EXISTS donations;

-- Recréation de la table donations avec la nouvelle structure
CREATE TABLE donations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    creator_id INT NOT NULL,
    donor_name VARCHAR(255) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    donation_type ENUM('PayPal','Photo','Cadeau','Autre') NOT NULL,
    donation_timestamp TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    comment TEXT DEFAULT NULL,
    timer_start_time TIMESTAMP NULL DEFAULT NULL,
    timer_elapsed_seconds INT(11) DEFAULT 0,
    timer_status ENUM('stopped','running') DEFAULT 'stopped',
    FOREIGN KEY (creator_id) REFERENCES creators(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
