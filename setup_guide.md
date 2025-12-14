# Guide de Configuration Locale (Ubuntu)

Ce guide vous explique comment installer et lancer l'application sur votre machine locale.

## Prérequis

Assurez-vous d'avoir installé :
- **PHP 8.2** ou supérieur
- **Composer**
- **SQLite3**
- **Git**

## Installation

1.  **Installer les dépendances PHP**
    ```bash
    composer install
    ```

2.  **Configurer l'environnement**
    Copiez le fichier d'exemple `.env` :
    ```bash
    cp .env.example .env
    ```
    
    Ouvrez le fichier `.env` et modifiez les lignes suivantes :
    ```ini
    APP_URL=http://localhost:8000
    # Utilisez le chemin absolu vers votre fichier de base de données
    DB_DATABASE=/var/www/html/web/msss/database/database.sqlite
    ```
    *(Note : Remplacez `/var/www/html/web/msss` par le chemin réel de votre projet si différent)*

3.  **Initialiser la base de données**
    Créez le fichier de base de données vide et lancez les migrations :
    ```bash
    touch database/database.sqlite
    php database/migrate.php
    ```

4.  **Lancer le serveur de développement**
    Un script de routage `router.php` a été créé à la racine pour faciliter le lancement.
    
    Lancez la commande suivante :
    ```bash
    php -S localhost:8000 -t public router.php
    ```

5.  **Accéder à l'application**
    Ouvrez votre navigateur à l'adresse : [http://localhost:8000](http://localhost:8000)

## Dépannage

- **Erreur de permissions** : Assurez-vous que le dossier `database` et le fichier `database.sqlite` sont accessibles en écriture.
- **Extensions PHP manquantes** : Vérifiez que vous avez les extensions `sqlite3`, `pdo_sqlite`, `mbstring` installées.
