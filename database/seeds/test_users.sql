-- Administrateur de test
INSERT INTO users (email, password, name, role, created_at) VALUES
('admin@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin Test', 'admin', NOW());

-- Créatrice de test
INSERT INTO creators (email, password, name, tagline, description, role, status, created_at) VALUES
('creatrice@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sarah Créatrice', 'Artiste digitale & streameuse', 'Je crée des illustrations et partage ma passion en direct !', 'creator', 'active', NOW());

-- Donateur de test
INSERT INTO users (email, password, name, role, created_at) VALUES
('donateur@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Thomas Donateur', 'user', NOW());

-- Ajout de quelques packs pour la créatrice de test
INSERT INTO packs (creator_id, name, description, price, benefits, created_at) VALUES
((SELECT id FROM creators WHERE email = 'creatrice@test.com'), 'Pack Découverte', 'Parfait pour commencer !', 5.00, '["Accès au chat privé", "Badge spécial"]', NOW()),
((SELECT id FROM creators WHERE email = 'creatrice@test.com'), 'Pack Premium', 'Pour les vrais fans', 15.00, '["Accès au chat privé", "Badge premium", "Contenu exclusif", "1 commission par mois"]', NOW());

-- Ajout de quelques liens pour la créatrice de test
INSERT INTO creator_links (creator_id, title, url, icon, created_at) VALUES
((SELECT id FROM creators WHERE email = 'creatrice@test.com'), 'Twitter', 'https://twitter.com/sarahcreatrice', 'fab fa-twitter', NOW()),
((SELECT id FROM creators WHERE email = 'creatrice@test.com'), 'Instagram', 'https://instagram.com/sarahcreatrice', 'fab fa-instagram', NOW()),
((SELECT id FROM creators WHERE email = 'creatrice@test.com'), 'Twitch', 'https://twitch.tv/sarahcreatrice', 'fab fa-twitch', NOW());

-- Ajout de quelques dons de test
INSERT INTO donations (creator_id, user_id, amount, donation_type, comment, created_at) VALUES
((SELECT id FROM creators WHERE email = 'creatrice@test.com'), (SELECT id FROM users WHERE email = 'donateur@test.com'), 10.00, 'one_time', 'Super travail !', NOW()),
((SELECT id FROM creators WHERE email = 'creatrice@test.com'), (SELECT id FROM users WHERE email = 'donateur@test.com'), 15.00, 'monthly', 'Je soutiens ton art chaque mois', NOW());
