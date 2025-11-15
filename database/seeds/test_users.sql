-- Comptes de test supplémentaires
INSERT OR REPLACE INTO creators (
    id, name, username, email, password, tagline, bio, donation_goal, status, role, is_active, is_admin
) VALUES
(2, 'Admin Test', 'admin-test', 'admin@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Gestionnaire', 'Compte administrateur de démonstration', 0, 'active', 'admin', 1, 1),
(3, 'Sarah Créatrice', 'sarah-creatrice', 'creatrice@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Artiste digitale & streameuse', 'Je crée des illustrations et partage ma passion en direct !', 500, 'active', 'creator', 1, 0);

-- Packs de démonstration
INSERT OR REPLACE INTO packs (creator_id, name, description, price, currency, perks, is_active, position) VALUES
(3, 'Pack Découverte', 'Parfait pour commencer !', 5.00, 'EUR', '["Accès au chat privé"]', 1, 1),
(3, 'Pack Premium', 'Pour les vrais fans', 15.00, 'EUR', '["Badge premium", "Contenu exclusif"]', 1, 2);

-- Dons de test
INSERT INTO donations (creator_id, donor_name, donor_email, amount, donation_type, message, donation_timestamp, timer_status) VALUES
(3, 'Thomas Donateur', 'donateur@test.com', 10.00, 'one_time', 'Super travail !', CURRENT_TIMESTAMP, 'completed'),
(3, 'Thomas Donateur', 'donateur@test.com', 15.00, 'monthly', 'Je soutiens ton art chaque mois', CURRENT_TIMESTAMP, 'completed');
