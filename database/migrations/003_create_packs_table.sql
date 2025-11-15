-- Migration : Création de la table packs
CREATE TABLE IF NOT EXISTS packs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    creator_id INTEGER NOT NULL,
    name TEXT NOT NULL,
    description TEXT,
    price REAL NOT NULL,
    currency TEXT DEFAULT 'EUR',
    perks TEXT DEFAULT NULL,
    icon TEXT DEFAULT NULL,
    color TEXT DEFAULT NULL,
    is_active INTEGER DEFAULT 1,
    position INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (creator_id) REFERENCES creators(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_creator_id ON packs(creator_id);
CREATE INDEX IF NOT EXISTS idx_position ON packs(position);
CREATE INDEX IF NOT EXISTS idx_is_active ON packs(is_active);
