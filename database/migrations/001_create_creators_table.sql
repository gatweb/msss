CREATE TABLE IF NOT EXISTS creators (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    username TEXT NOT NULL UNIQUE,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    tagline TEXT DEFAULT NULL,
    bio TEXT DEFAULT NULL,
    profile_pic_url TEXT DEFAULT NULL,
    banner_url TEXT DEFAULT NULL,
    donation_goal REAL DEFAULT 0,
    status TEXT CHECK(status IN ('pending', 'active', 'suspended', 'banned')) DEFAULT 'pending',
    role TEXT CHECK(role IN ('creator', 'admin')) DEFAULT 'creator',
    is_active INTEGER DEFAULT 0,
    is_admin INTEGER DEFAULT 0,
    verification_token TEXT DEFAULT NULL,
    reset_token TEXT DEFAULT NULL,
    reset_token_expires DATETIME DEFAULT NULL,
    remember_token TEXT DEFAULT NULL,
    remember_token_expires DATETIME DEFAULT NULL,
    last_login DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_creators_username ON creators(username);
CREATE INDEX IF NOT EXISTS idx_creators_email ON creators(email);
CREATE INDEX IF NOT EXISTS idx_creators_status ON creators(status);
CREATE INDEX IF NOT EXISTS idx_creators_verification_token ON creators(verification_token);
CREATE INDEX IF NOT EXISTS idx_creators_reset_token ON creators(reset_token);
CREATE INDEX IF NOT EXISTS idx_creators_remember_token ON creators(remember_token);
