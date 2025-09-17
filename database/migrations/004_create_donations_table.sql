-- Migration : Création de la table donations
CREATE TABLE IF NOT EXISTS donations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    creator_id INTEGER NOT NULL,
    pack_id INTEGER,
    donor_name TEXT NOT NULL,
    donor_email TEXT,
    amount REAL NOT NULL,
    currency TEXT DEFAULT 'EUR',
    message TEXT,
    status TEXT CHECK(status IN ('pending', 'completed', 'failed', 'refunded')) DEFAULT 'pending',
    payment_method TEXT,
    payment_id TEXT,
    transaction_id TEXT,
    timer_duration INTEGER DEFAULT NULL,
    timer_started_at DATETIME DEFAULT NULL,
    timer_ended_at DATETIME DEFAULT NULL,
    is_anonymous INTEGER DEFAULT 0,
    is_displayed INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (creator_id) REFERENCES creators(id) ON DELETE CASCADE,
    FOREIGN KEY (pack_id) REFERENCES packs(id) ON DELETE SET NULL
);

CREATE INDEX IF NOT EXISTS idx_creator_id ON donations(creator_id);
CREATE INDEX IF NOT EXISTS idx_pack_id ON donations(pack_id);
CREATE INDEX IF NOT EXISTS idx_status ON donations(status);
CREATE INDEX IF NOT EXISTS idx_created_at ON donations(created_at);
CREATE INDEX IF NOT EXISTS idx_timer_started_at ON donations(timer_started_at);
CREATE INDEX IF NOT EXISTS idx_payment_id ON donations(payment_id);
CREATE INDEX IF NOT EXISTS idx_transaction_id ON donations(transaction_id);
