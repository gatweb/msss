-- Migration : Création de la table donations
CREATE TABLE IF NOT EXISTS donations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    creator_id INTEGER NOT NULL,
    pack_id INTEGER,
    donor_id INTEGER,
    donor_name TEXT NOT NULL,
    donor_email TEXT,
    amount REAL NOT NULL,
    currency TEXT DEFAULT 'EUR',
    donation_type TEXT DEFAULT 'one_time',
    message TEXT,
    payment_method TEXT,
    payment_id TEXT,
    transaction_id TEXT,
    status TEXT CHECK(status IN ('pending', 'completed', 'failed', 'refunded')) DEFAULT 'pending',
    is_anonymous INTEGER DEFAULT 0,
    is_displayed INTEGER DEFAULT 1,
    donation_timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    timer_status TEXT DEFAULT 'stopped',
    timer_start_time DATETIME DEFAULT NULL,
    timer_end DATETIME DEFAULT NULL,
    timer_elapsed_seconds INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (creator_id) REFERENCES creators(id) ON DELETE CASCADE,
    FOREIGN KEY (pack_id) REFERENCES packs(id) ON DELETE SET NULL
);

CREATE INDEX IF NOT EXISTS idx_donations_creator_id ON donations(creator_id);
CREATE INDEX IF NOT EXISTS idx_donations_pack_id ON donations(pack_id);
CREATE INDEX IF NOT EXISTS idx_donations_status ON donations(status);
CREATE INDEX IF NOT EXISTS idx_donations_timestamp ON donations(donation_timestamp);
CREATE INDEX IF NOT EXISTS idx_donations_payment_id ON donations(payment_id);
CREATE INDEX IF NOT EXISTS idx_donations_transaction_id ON donations(transaction_id);
