-- Migration : Création de la table creators
CREATE TABLE IF NOT EXISTS creators (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    avatar TEXT DEFAULT NULL,
    banner TEXT DEFAULT NULL,
    bio TEXT,
    username TEXT UNIQUE,
    status TEXT CHECK(status IN ('pending', 'active', 'suspended', 'banned')) DEFAULT 'pending',
    role TEXT CHECK(role IN ('creator', 'admin')) DEFAULT 'creator',
    verification_token TEXT DEFAULT NULL,
    reset_token TEXT DEFAULT NULL,
    reset_token_expires DATETIME DEFAULT NULL,
    remember_token TEXT DEFAULT NULL,
    remember_token_expires DATETIME DEFAULT NULL,
    last_login DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_username ON creators(username);
CREATE INDEX IF NOT EXISTS idx_email ON creators(email);
CREATE INDEX IF NOT EXISTS idx_status ON creators(status);
CREATE INDEX IF NOT EXISTS idx_verification_token ON creators(verification_token);
CREATE INDEX IF NOT EXISTS idx_reset_token ON creators(reset_token);
CREATE INDEX IF NOT EXISTS idx_remember_token ON creators(remember_token);
