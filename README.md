# README - MSSS

## Installation locale rapide

1. **Cloner le projet**
   ```bash
   git clone <repo> msss
   cd msss
   ```
2. **Vérifier les prérequis**
   - PHP >= 8.0
   - SQLite3
   - Composer
   - Apache2 (ou Nginx)

3. **Configurer l'environnement**
   - Copier `.env.example` vers `.env` et adapter les variables :
     - `APP_URL=http://msss.local`
     - `DB_DATABASE=/var/www/html/web/msss/database/database.sqlite`
   - Créer le fichier de base de données si besoin :
     ```bash
     touch database/database.sqlite
     ```

4. **Installer les dépendances**
   ```bash
   composer install
   ```

5. **Configurer Apache**
   - Créer un VirtualHost avec :
     ```
     DocumentRoot /var/www/html/web/msss/public
     ServerName msss.local
     <Directory /var/www/html/web/msss/public>
         AllowOverride All
         Require all granted
     </Directory>
     ```
   - Ajouter `127.0.0.1 msss.local` dans `/etc/hosts`.
   - Redémarrer Apache.

6. **Accéder à l’application**
   - Aller sur http://msss.local

## Checklist migration/renommage

- [x] Renommer tous les dossiers/fichiers au nouveau nom
- [x] Mettre à jour `.env` (APP_URL, DB_DATABASE)
- [x] Mettre à jour la config Apache
- [x] Vérifier que plus aucun chemin absolu ou ancien nom n’est présent dans le code
- [x] Nettoyer les logs (`> storage/logs/error.log`)
- [x] Tester l’accès local

---

**Conseil :**
Pour toute future migration, il suffit de refaire ces étapes, sans jamais coder en dur un chemin absolu dans le code.

## Architecture cible

L'application repose sur une architecture PHP modulaire inspirée de MVC :

- `app/Core` contient l'infrastructure partagée (router, base de données, vues, authentification, etc.).
- `app/Controllers` orchestre les requêtes HTTP et délègue aux services et dépôts.
- `app/Services` encapsule les intégrations externes (par exemple `StripeService`).
- `app/Repositories` centralise l'accès aux données applicatives.
- `app/Models` et `app/Utils` regroupent respectivement les objets métiers et les utilitaires transverses.
- `app/views` héberge les gabarits d'affichage.
- `app/config` stocke les paramètres applicatifs et les clés d'API.
- `public/` sert les actifs web (front) et le point d'entrée HTTP.
- `tests/` rassemble les tests automatisés (unitaires et fonctionnels légers sur le routage).

Cette séparation vise à conserver des responsabilités claires et facilite l'écriture de tests ciblés tout en gardant des composants réutilisables.

## Conventions de code

- Respecter les standards PSR-12 et privilégier les tableaux à syntaxe courte (`[]`).
- Injecter explicitement les dépendances afin de favoriser le test et la réutilisation des services.
- Utiliser les namespaces `App\` pour le code applicatif et `Tests\` pour les tests.
- Journaliser via `error_log` pour garder un suivi exploitable en développement.
- Préférer les exceptions précises et documenter les méthodes publiques avec des PHPDoc succincts.

Un profileur PHP-CS-Fixer est fourni pour uniformiser le style ; exécutez `composer lint` avant toute soumission.

## Instructions de contribution

1. Créer une branche dédiée et appliquer les modifications.
2. Installer les dépendances de développement si nécessaire : `composer install`.
3. Lancer l'ensemble des vérifications automatisées :
   - Tests unitaires et fonctionnels : `composer test`
   - Analyse statique (PHPStan) : `composer check:types`
   - Vérification du style (PHP-CS-Fixer en mode dry-run) : `composer lint`
4. Corriger les éventuels problèmes (`composer lint:fix` permet de corriger automatiquement le style) puis re-exécuter les commandes.
5. Documenter les changements dans la Pull Request et compléter `docs/CONTRIBUTING.md` si de nouvelles règles sont introduites.

Des détails supplémentaires (workflow Git, critères de revue, bonnes pratiques) sont disponibles dans `docs/CONTRIBUTING.md`.
