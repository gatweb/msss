-- Migration : Table pour les notes et préférences CRM des donateurs
CREATE TABLE IF NOT EXISTS donator_notes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    creator_id INTEGER NOT NULL,
    donor_email TEXT NOT NULL,
    notes_json TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(creator_id, donor_email),
    FOREIGN KEY (creator_id) REFERENCES creators(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_donator_notes_creator ON donator_notes(creator_id);
CREATE INDEX IF NOT EXISTS idx_donator_notes_email ON donator_notes(donor_email);
