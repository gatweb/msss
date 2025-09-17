-- Ajout des colonnes pour les liens de paiement
ALTER TABLE creators ADD COLUMN paypal_link TEXT DEFAULT '';
ALTER TABLE creators ADD COLUMN amazon_link TEXT DEFAULT '';
ALTER TABLE creators ADD COLUMN throne_link TEXT DEFAULT '';
ALTER TABLE creators ADD COLUMN other_links TEXT DEFAULT '';

-- Mise à jour de la structure pour les dons
ALTER TABLE donations ADD COLUMN platform VARCHAR(50) DEFAULT 'other';
ALTER TABLE donations ADD COLUMN external_reference VARCHAR(255);
