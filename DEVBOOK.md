# DEVBOOK - Msss Platform

---

## 🧩 Organisation des Layouts & Bonnes Pratiques

---

### 🚦 Nouvelle structure des menus & layouts (avril 2025)

Pour garantir une expérience utilisateur claire et éviter tout doublon de navigation :

- **Chaque espace (public, créatrice, admin, donateur, user) possède son propre layout dédié**.
- **Aucun menu public (`.nav-links`) n'apparaît dans les dashboards** (créatrice, admin, donateur).
- **Les menus sont centralisés dans les fichiers header/sidebar spécifiques** à chaque rôle.

#### Récapitulatif des layouts & menus

| Espace         | Layout principal           | Menu affiché                 |
|----------------|---------------------------|------------------------------|
| Public         | `default.php`             | Topbar `.nav-links`          |
| Créatrice      | `dashboard.php`           | Sidebar créatrice            |
| Admin          | `admin.php`               | Sidebar admin                |
| Donateur       | `donor_header.php`        | Sidebar donateur             |
| Utilisateur    | `user_header.php`         | Topbar utilisateur           |

#### Menus par profil

- **Public** : Accueil, Connexion, Inscription, Mon Profil, Déconnexion, Administration (si admin)
- **Créatrice** : Accueil dashboard, Statistiques, Packs, Publications, Galerie, Dons, Donateurs, Messages, Profil, Paramètres, Voir ma page, Déconnexion
- **Admin** : Tableau de bord, Créatrices, Transactions, Paramètres du site, Retour au site, Déconnexion
- **Donateur** : Créatrices suivies, Découvrir, Historique des dons, Abonnements, Profil, Notifications, Paramètres, Déconnexion

**Bonnes pratiques** :
- Ne jamais inclure le header/footer public dans les layouts dashboard.
- Les vues métiers ne doivent contenir que le contenu spécifique à la page (pas de navigation).
- Pour ajouter un lien ou modifier un menu, éditer uniquement le header/sidebar du layout concerné.

---

## 🔗 Audit des liens (avril 2025)

Un audit exhaustif de tous les liens (href, actions, redirections JS, formulaires) présents dans les vues a été généré automatiquement.

- **Fichier de référence** : `LINKS_AUDIT.md` à la racine du projet.
- **Utilité** :
  - Vérifier la cohérence de la navigation par espace (public, créatrice, admin, donateur, user)
  - Contrôler la sécurité (pas de liens admin dans le public, pas de doublons, liens externes sécurisés...)
  - Faciliter la QA et l'onboarding
- **Structure du fichier** :
  - Liens classés par fichier et par espace
  - Points de contrôle sécurité/cohérence en fin de document
- **Exemple** :

```markdown
### /app/views/layouts/creator_header.php [CREA]
- Packs : `/creator/packs`
- Publications : `/creator/posts`
- ...
```

**Bonnes pratiques** :
- Lors de l'ajout ou la modification d'un lien, vérifier la section concernée dans `LINKS_AUDIT.md`.
- Toujours respecter la séparation des espaces et la logique de navigation décrite ci-dessus.

---

## 🗂️ Tableau de vérification des vues (avril 2025)

Ce tableau permet de vérifier pour chaque vue :
- L’espace/role concerné
- Le layout utilisé
- Les liens affichés
- Les points de contrôle principaux (layout/navigation/sécurité/doublons)

| Fichier                          | Espace   | Layout attendu    | Liens principaux                                                                                  | Layout OK | Navigation OK | Sécurité OK | Doublon |
|-----------------------------------|----------|-------------------|---------------------------------------------------------------------------------------------------|-----------|---------------|-------------|---------|
| public/index.php                 | Public   | default.php       | Accueil `/`, Connexion `/login`, Inscription `/register`, Mon Profil `/profile`                   | ✅        | ✅            | ✅          | ❌      |
| creator/packs.php                | Créatrice| dashboard.php     | Packs `/creator/packs`, Créer un pack `/creator/packs_create`, Déconnexion `/logout`              | ✅        | ✅            | ✅          | ❌      |
| admin/index.php                  | Admin    | admin.php         | Dashboard `/profile/admin`, Transactions `/dashboard/donations`, Déconnexion `/logout`            | ✅        | ✅            | ✅          | ❌      |

> Le tableau complet peut être généré automatiquement ou fourni sur demande (voir Cascade).

---

## Navigation & Expérience Utilisateur (UX)

---

## Messagerie interne (Work in Progress)
- Un lien "Messages" est présent dans la navigation (navbar + footer)
- La page /messages affiche un placeholder : "La messagerie interne arrive bientôt !"
- Le système prévu : formulaire HTML permettant à un donateur d'envoyer un message à une créatrice sans révéler son email, et inversement. Les réponses se feront également par formulaire sécurisé.
- Objectif : garantir la confidentialité des adresses emails et la simplicité d'usage.

---

## Navigation
- Barre de navigation principale en haut, responsive (menu mobile inclus)
- Navigation rapide vers Profil, Packs, Donations, Donators, Messages (Liens déplacés dans le profil)
- Footer avec liens de rappel
- Tous les liens utilisent des boutons ou liens Material Design cohérents

---

## Installation & Configuration

1. Cloner le dépôt
2. Copier `.env.example` en `.env` et adapter les variables selon l’environnement
3. Installer les dépendances PHP :
   composer install
4. Configurer la base de données (voir bdd.txt)
5. Appliquer les migrations :
   php database/migrate.php
6. Configurer le serveur web (Apache/Nginx) pour pointer vers le dossier public/

---

## Migrations & Base de données

- Les fichiers de migration SQL sont dans `database/migrations/`
- Les seeds de données sont dans `database/seeds/`
- Pour réinitialiser la base :
   php database/migrate.php
- Le fichier de base SQLite est dans `storage/database.sqlite`
- Voir aussi `bdd.txt` pour la structure et les accès

---

## Dépendances & Mise à jour

### Backend (PHP)
- Liste dans composer.json
- Pour mettre à jour :
   composer update

### Frontend (JS/CSS)
- jQuery, Chart.js, Font Awesome chargés via CDN dans les layouts
- Pour mettre à jour, modifier les URLs CDN dans app/views/layouts/header.php

---

## Sécurité

- Protéger `.env`, `bdd.txt`, `storage/`, `logs/` via .htaccess ou config serveur
- Ne jamais exposer la base SQLite en production

---

## À Propos
## 📖 À Propos

Msss est une plateforme de gestion de dons pour créatrices de contenu. Elle permet aux créatrices de gérer leurs relations avec leurs donateurs, suivre leurs objectifs et offrir différents niveaux de récompenses via des packs.

## État du Projet (21/04/2025)

### Fonctionnalités Implémentées

#### Système d'Authentification et Sécurité
- ✅ Inscription et validation des créatrices
- ✅ Connexion/Déconnexion sécurisée
- ✅ Protection CSRF sur tous les formulaires
- ✅ Gestion des sessions PHP sécurisées
- ✅ Récupération de mot de passe
- ✅ Validation des entrées utilisateur
- ✅ Gestion des statuts utilisateur (is_active)

#### Interface d'Administration
- ✅ Tableau de bord avec statistiques globales
- ✅ Gestion des créatrices (activation/désactivation)
- ✅ Visualisation des données avec Chart.js
- ✅ Interface responsive et moderne
- ✅ Journalisation des actions
- ✅ Navigation séparée pour l'administration

#### Profil Créatrice
- ✅ Édition complète du profil
- ✅ Upload sécurisé des photos
- ✅ Gestion des informations (tagline, description)
- ✅ Tableau de bord personnel
- ✅ Statistiques détaillées
- ✅ Navigation intuitive et organisée

#### Système de Dons et Paiements
- ✅ Structure de base implémentée
- ✅ Pagination et filtrage
- ✅ Statistiques en temps réel
- 🟡 Liens PayPal implémentés
- ✅ Gestion des transactions (basique)
- ✅ Interface de gestion des dons séparée

#### Interface Publique
- ✅ Page d'accueil moderne
- ✅ Grille des créatrices
- ✅ Système de recherche
- ✅ Design responsive

### Fonctionnalités en Cours de Développement 🛠️

1. **Zone Créatrice**
   - ✅ Dashboard avec statistiques
   - ✅ Gestion des donateurs
   - ✅ Vue des messages
   - ✅ Gestion des packs
   - 🟡 Personnalisation avancée

2. **Système de Paiement**
   - 🟡 Intégration PayPal
   - 🟡 Gestion des transactions
   - 🟡 Notifications de paiement
   - 🔴 Abonnements récurrents

3. **Gestion des Packs**
   - ✅ Création et édition
   - ✅ Activation/désactivation
   - ✅ Statistiques par pack
   - 🟡 Niveaux d'accès

4. **Système de Messagerie**
   - ✅ Interface de messagerie
   - ✅ Filtres et recherche
   - 🟡 Notifications en temps réel
   - 🔴 Chat en direct

### Dernières Améliorations 🚀

1. **Zone Créatrice**
   - ✅ Nouveau dashboard avec statistiques détaillées
   - ✅ Interface de gestion des donateurs
   - ✅ Système de messagerie intégré
   - ✅ Gestion avancée des packs
   - ✅ Graphiques interactifs avec Chart.js

2. **Optimisation**
   - ✅ Nettoyage et simplification du code
   - ✅ Fusion des contrôleurs redondants
   - ✅ Organisation optimisée des vues
   - ✅ Amélioration des performances
   - ✅ Réduction de la complexité

3. **Interface**
   - ✅ Design moderne et cohérent
   - ✅ Navigation intuitive
   - ✅ Responsive sur tous les écrans
   - ✅ Animations fluides
   - ✅ Thèmes adaptés aux rôles

### Base de Données 💾

#### Tables Principales

1. **creators** - Informations des créatrices
   ```sql
   CREATE TABLE creators (
       id INT PRIMARY KEY AUTO_INCREMENT,
       name VARCHAR(255) NOT NULL,
       email VARCHAR(255) UNIQUE NOT NULL,
       password_hash VARCHAR(255) NOT NULL,
       tagline VARCHAR(255),
       description TEXT,
       profile_pic_url VARCHAR(255),
       banner_url VARCHAR(255),
       donation_goal DECIMAL(10,2) DEFAULT 0.00,
       is_active BOOLEAN DEFAULT true,
       is_admin BOOLEAN DEFAULT false,
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
   );
   ```

2. **donations** - Historique des dons
   ```sql
   CREATE TABLE donations (
       id INT PRIMARY KEY AUTO_INCREMENT,
       creator_id INT NOT NULL,
       donor_name VARCHAR(255),
       amount DECIMAL(10,2) NOT NULL,
       donation_type ENUM('PayPal','Stripe','Crypto','Other') NOT NULL,
       status ENUM('pending','completed','failed','refunded') DEFAULT 'pending',
       transaction_id VARCHAR(255),
       comment TEXT,
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       FOREIGN KEY (creator_id) REFERENCES creators(id)
   );
   ```

3. **creator_stats** - Statistiques agrégées
   ```sql
   CREATE TABLE creator_stats (
       creator_id INT PRIMARY KEY,
       total_donations DECIMAL(10,2) DEFAULT 0.00,
       donation_count INT DEFAULT 0,
       avg_donation DECIMAL(10,2) DEFAULT 0.00,
       last_donation_at TIMESTAMP,
       updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
       FOREIGN KEY (creator_id) REFERENCES creators(id)
   );
   ```

4. **packs** - Packs de récompenses
   ```sql
   CREATE TABLE packs (
       id INT PRIMARY KEY AUTO_INCREMENT,
       creator_id INT NOT NULL,
       name VARCHAR(255) NOT NULL,
       description TEXT,
       price DECIMAL(10,2) NOT NULL,
       image_url VARCHAR(255),
       is_active BOOLEAN DEFAULT true,
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       FOREIGN KEY (creator_id) REFERENCES creators(id)
   );
   ```

5. **sessions** - Gestion des sessions
   ```sql
   CREATE TABLE sessions (
       id VARCHAR(255) PRIMARY KEY,
       user_id INT NOT NULL,
       ip_address VARCHAR(45),
       user_agent TEXT,
       payload TEXT,
       last_activity INT,
       FOREIGN KEY (user_id) REFERENCES creators(id)
   );
   ```

#### Triggers et Procédures Stockées

```sql
-- Mise à jour automatique des statistiques après un don
DELIMITER //
CREATE TRIGGER after_donation_insert
AFTER INSERT ON donations
FOR EACH ROW
BEGIN
    INSERT INTO creator_stats (creator_id, total_donations, donation_count, last_donation_at)
    VALUES (NEW.creator_id, NEW.amount, 1, NOW())
    ON DUPLICATE KEY UPDATE
        total_donations = total_donations + NEW.amount,
        donation_count = donation_count + 1,
        avg_donation = total_donations / donation_count,
        last_donation_at = NOW();
END //
DELIMITER ;
```

### Structure du Projet

```plaintext
msss/
├── app/                    # Cœur de l'application
│   ├── Controllers/        # Contrôleurs de l'application
│   │   ├── AdminController.php
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   └── ProfileController.php
│   ├── Core/              # Classes principales du framework
│   │   ├── Auth.php       # Gestion de l'authentification
│   │   ├── BaseController.php
│   │   ├── Database.php   # Connexion base de données
│   │   ├── Router.php     # Système de routage
│   │   └── View.php       # Moteur de template
│   ├── Models/           # Modèles de données
│   │   ├── Creator.php
│   │   └── Donation.php
│   ├── config/          # Configuration de l'application
│   │   ├── app.php      # Configuration générale
│   │   └── stripe.php   # Configuration Stripe
│   ├── views/           # Templates des vues
│   │   ├── admin/
│   │   ├── auth/
│   │   ├── dashboard/
│   │   ├── profile/
│   │   └── layouts/
│   ├── bootstrap.php    # Initialisation de l'application
│   └── routes.php       # Définition des routes
├── public/              # Racine web publique
│   ├── assets/          # Ressources statiques
│   │   ├── css/
│   │   ├── js/
│   │   └── img/
│   └── index.php        # Point d'entrée unique
├── storage/             # Fichiers uploadés et cache
├── database/            # Migrations et seeds
├── logs/                # Fichiers de logs
└── vendor/              # Dépendances Composer
```

### Prochaines Étapes

1. **Système de Paiement**
   - Intégration d'une passerelle de paiement
   - Gestion des transactions
   - Notifications

2. **Amélioration UX**
   - Messages de confirmation
   - Validations côté client
   - Animations et transitions

3. **Sécurité**
   - Audit de sécurité
   - Rate limiting
   - Journalisation avancée

### Notes Techniques

- **Base de données** : MySQL/MariaDB via PDO
- **PHP Version** : 8.2+
- **Architecture** : MVC personnalisé
- **Authentification** : Sessions PHP avec CSRF protection
- **Frontend** : 
  - HTML5 & CSS3
  - JavaScript avec jQuery 3.6.0
  - Chart.js pour les graphiques
  - Font Awesome 6.0.0 pour les icônes
- **Dépendances** : Gérées via Composer

## 🎯 Vue d'ensemble

Application web permettant aux créatrices de gérer leurs dons et leurs relations avec les donateurs, avec une interface adaptée pour les donateurs.

## 🏗️ Architecture

### Structure MVC
1. **Models** (`app/models/`)
   - `Donation.php` : Gestion des dons et objectifs
   - À venir : Creator, Pack, User

2. **Views** (`app/views/`)
   - `layouts/` : Templates principaux
   - `partials/` : Composants réutilisables
   - `donations/` : Vues liées aux dons
   - `creators/` : Vues des créatrices
   - `packs/` : Vues des packs

3. **Controllers** (`app/controllers/`)
   - `DonationController.php` : Logique des dons
   - À venir : CreatorController, PackController

4. **Router** (`app/Router.php`)
   - Gestion des routes
   - Dispatch vers les contrôleurs

### Zones Principales
1. **Zone Publique** - Landing Page
   - Liste des créatrices
   - Interface de découverte
   - Tech: HTML, CSS, PHP

2. **Zone Donateur** - Interface par Créatrice
   - Thème sombre et mystique
   - Système de dons et packs
   - Liens externes

3. **Zone Créatrice** - Dashboard
   - Thème girly et motivant
   - Mini-CRM et gestion des dons
   - Système de badges/succès
   - Gestion des packs
   - Configuration du profil
   - Chronomètre de relance (15 jours)

4. **Zone Admin** - Back-office
   - Gestion des comptes
   - Configuration système
   - Statistiques globales

## 💾 Base de Données

### Structure Actuelle

```sql
-- Créatrices
creators (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    password_hash VARCHAR(255),
    tagline VARCHAR(255),
    description TEXT,
    profile_pic_url VARCHAR(255),
    banner_url VARCHAR(255),
    donation_goal DECIMAL(10,2),
    is_active BOOLEAN
)

-- Dons
donations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    creator_id INT,
    donor_name VARCHAR(255),
    amount DECIMAL(10,2),
    donation_type ENUM('PayPal','Photo','Cadeau','Autre'),
    donation_timestamp TIMESTAMP,
    comment TEXT,
    timer_start_time TIMESTAMP,
    timer_elapsed_seconds INT,
    timer_status ENUM('stopped','running')
)

-- Liens des créatrices
creator_links (
    id INT PRIMARY KEY AUTO_INCREMENT,
    creator_id INT,
    title VARCHAR(255),
    url VARCHAR(512)
)

-- Packs
packs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    creator_id INT,
    name VARCHAR(255),
    description TEXT,
    price DECIMAL(10,2),
    image_url VARCHAR(255),
    is_active BOOLEAN
)
```

### Migrations
Les scripts de migration sont disponibles dans :
- `database/migrations/creators.sql` : Structure des tables
- `database/seeds/initial_data.sql` : Données initiales

## 🛠️ Stack Technique

- **Frontend:**
  - HTML5
  - CSS3 (2 thèmes: sombre/mystique et girly)
  - JavaScript (Vanilla + possiblement framework)
  - Responsive Design

- **Backend:**
  - PHP
  - MySQL
  - API RESTful

- **Sécurité:**
  - Authentification sécurisée
  - Hachage des mots de passe
  - Validation des entrées
  - Protection contre les injections SQL
  - Sécurisation des uploads

- **Paiement:**
  - Liens PayPal pour les dons/packs (intégration API à venir)
  - Gestion sécurisée des transactions

## 📋 Fonctionnalités Clés

### Zone Publique
- Grille des créatrices
- Recherche et filtrage
- Accès aux pages individuelles

### Zone Donateur
- Profil créatrice
- Système de dons
- Gestion des packs
- Liens externes

### Zone Créatrice
- Dashboard avec statistiques
- Gestion des donateurs
- Système de badges/succès
- Gestion des packs
- Configuration du profil
- Chronomètre de relance (15 jours)

### Zone Admin
- Gestion des comptes
- Configuration système
- Statistiques globales

## ⚡ Points d'Attention

1. **Performance**
   - Optimisation des requêtes
   - Mise en cache
   - Chargement différé des images

2. **Sécurité**
   - Authentification robuste
   - Protection des données
   - Validation des entrées

3. **UX/UI**
   - Interface intuitive
   - Responsive design
   - Thèmes cohérents

4. **Maintenance**
   - Code modulaire
   - Documentation
   - Tests unitaires

## 📅 Priorités de Développement

1. Structure de base et authentification
2. Zone Créatrice (Dashboard)
3. Zone Donateur et système de paiement
4. Zone Publique
5. Zone Admin
6. Tests et optimisations

## 🔄 Workflow Git Suggéré

```
main
  ├── develop
  │   ├── feature/public-zone
  │   ├── feature/donor-interface
  │   ├── feature/creator-dashboard
  │   └── feature/admin-panel
  └── hotfix/*
```

## 🔒 Sécurité

### Mesures Implémentées

1. **Authentification**
   - Sessions PHP sécurisées
   - Protection CSRF sur tous les formulaires
   - Hachage des mots de passe avec `password_hash()`
   - Régénération d'ID de session à la connexion

2. **Protection des Données**
   - Requêtes préparées PDO
   - Validation et assainissement des entrées
   - Configuration sécurisée des cookies
   - En-têtes de sécurité HTTP

3. **Upload de Fichiers**
   - Validation des types MIME
   - Génération de noms de fichiers sécurisés
   - Stockage hors de la racine web
   - Limites de taille configurables

### À Implémenter

- Rate limiting sur l'API et les formulaires
- Authentification à deux facteurs (2FA)
- Journalisation des actions sensibles
- Audit de sécurité régulier

## 🚀 Déploiement

### Prérequis

```bash
# Serveur
PHP >= 8.2
MySQL >= 8.0
Apache/Nginx
Composer

# PHP Extensions
pdo_mysql
fileinfo
gd
mbstring
openssl
```

### Installation

1. **Cloner le projet**
   ```bash
   git clone https://github.com/votre-repo/msss.git
   cd msss
   ```

2. **Installer les dépendances**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

3. **Configuration**
   ```bash
   cp .env.example .env
   # Éditer .env avec vos paramètres
   ```

4. **Base de données**
   ```bash
   # Importer les migrations
   mysql -u user -p database < database/migrations/*.sql
   ```

5. **Permissions**
   ```bash
   chmod -R 755 public/
   chmod -R 777 storage/ logs/
   ```

### Configuration Serveur

**Apache** (.htaccess déjà configuré)
```apache
RewriteEngine On
RewriteBase /
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [QSA,L]
```

**Nginx**
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

## 🧪 Tests

### Tests Unitaires

```bash
# Installation des dépendances de dev
composer install --dev

# Lancer les tests
./vendor/bin/phpunit tests/
```

### Tests à Implémenter

1. **Tests Unitaires**
   - Modèles (Creator, Donation)
   - Services (Auth, Upload)
   - Helpers

2. **Tests d'Intégration**
   - Flux d'authentification
   - Processus de don
   - API endpoints

3. **Tests End-to-End**
   - Parcours utilisateur complet
   - Scénarios de paiement
   - Interface administrateur

## 📚 Documentation

### Structure de la Documentation

```plaintext
/docs
├── installation.md       # Guide d'installation
├── api/
│   ├── auth.md          # Endpoints d'authentification
│   ├── donations.md      # Endpoints de dons
│   └── creators.md       # Endpoints des créatrices
├── development/
│   ├── architecture.md   # Architecture du projet
│   ├── database.md       # Structure de la BDD
│   └── security.md       # Mesures de sécurité
└── contribution/
    ├── guidelines.md     # Guide de contribution
    ├── coding-style.md   # Standards de code
    └── testing.md        # Guide des tests
```

### Standards de Code

- PSR-1, PSR-4, PSR-12
- Documentation PHPDoc
- Messages de commit conventionnels
- Tests requis pour les nouvelles fonctionnalités
