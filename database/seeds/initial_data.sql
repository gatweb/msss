-- Insertion de la créatrice principale
INSERT OR REPLACE INTO creators (
    id, name, username, email, password, tagline, bio, profile_pic_url, banner_url,
    donation_goal, status, role, is_active, is_admin
) VALUES (
    1,
    'Msss',
    'msss',
    'contact@msss.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Créatrice passionnée',
    'Bienvenue sur ma page ! Je suis Msss, passionnée par la création de contenu unique et personnalisé.',
    NULL,
    NULL,
    1000,
    'active',
    'admin',
    1,
    1
);

-- Ajout des liens sociaux
INSERT OR REPLACE INTO creator_links (creator_id, title, url, icon, position) VALUES
(1, 'Twitter', 'https://twitter.com/msss', 'fab fa-twitter', 1),
(1, 'Instagram', 'https://instagram.com/msss', 'fab fa-instagram', 2);

-- Ajout des packs de dons
INSERT OR REPLACE INTO packs (creator_id, name, description, price, currency, perks, icon, color, position) VALUES
(1, 'Pack Découverte', 'Un pack parfait pour commencer l''aventure', 25.00, 'EUR', '["Accès au chat privé"]', 'fas fa-star', '#4CAF50', 1),
(1, 'Pack Premium', 'Une expérience complète et personnalisée', 50.00, 'EUR', '["Contenu exclusif"]', 'fas fa-gem', '#2196F3', 2),
(1, 'Pack VIP', 'Pour les plus dévoués, avec un accès privilégié', 100.00, 'EUR', '["Rencontre mensuelle"]', 'fas fa-crown', '#FFC107', 3);
