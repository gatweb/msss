-- Migration : Création de la table creator_stats
DROP TABLE IF EXISTS creator_stats;

CREATE TABLE creator_stats (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    creator_id INTEGER NOT NULL,
    total_donations INTEGER DEFAULT 0,
    total_amount REAL DEFAULT 0.00,
    unique_donors INTEGER DEFAULT 0,
    average_donation REAL DEFAULT 0.00,
    last_donation_at DATETIME DEFAULT NULL,
    stats_date DATE NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (creator_id) REFERENCES creators(id) ON DELETE CASCADE,
    UNIQUE (creator_id, stats_date)
);

CREATE INDEX IF NOT EXISTS idx_creator_id ON creator_stats(creator_id);
CREATE INDEX IF NOT EXISTS idx_stats_date ON creator_stats(stats_date);
