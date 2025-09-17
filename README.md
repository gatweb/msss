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
