-- Migration : Table pour suivre les objectifs globaux
CREATE TABLE IF NOT EXISTS global_status (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    donation_goal REAL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT OR IGNORE INTO global_status (id, donation_goal)
VALUES (1, 0);
