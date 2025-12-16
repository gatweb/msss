# Guide de Développement du Projet MSSS

Ce document vise à fournir une compréhension complète du projet MSSS, incluant son architecture, ses fonctionnalités, l'état actuel des travaux (audit), les tâches restantes et les pistes d'évolution technique. Il est destiné à tout développeur souhaitant prendre en main ou contribuer au projet.

---

## 1. Vue d'ensemble de l'Application

Le projet MSSS est une application web PHP utilisant un pattern MVC (Model-View-Controller) pour organiser son code. Il gère la création de profils de créateurs, la gestion des donations, la messagerie, et propose une interface d'administration.

### 1.1 Fonctionnalités Clés

*   **Gestion des Créateurs**: Création, modification, et affichage des profils de créateurs avec des liens personnalisés et des outils.
*   **Système de Donations**: Processus de donation pour soutenir les créateurs.
*   **Messagerie Interne**: Communication entre utilisateurs/créateurs.
*   **Outils d'IA**: Intégration d'outils liés à l'IA pour les créateurs (par exemple, `ia/improve_text_api.php`).
*   **Administration**: Panneau pour la gestion des utilisateurs, des transactions, et des paramètres.
*   **Authentification et Autorisation**: Système de connexion, d'inscription et de gestion des rôles (Admin, Créateur).

### 1.2 Technologies Utilisées

*   **Langage**: PHP
*   **Base de Données**: SQLite (fichier `app/storage/database.sqlite3`)
*   **ORM/DBAL**: Utilisation directe de PDO pour les interactions base de données.
*   **Moteur de Template**: Twig (intégralement). Les vues PHP natives ont été supprimées.
*   **Dépendances**: Gérées via Composer.
*   **Routing**: Custom Router (`app/Core/Router.php`).
*   **Authentification**: Custom Auth system (`app/Core/Auth.php`).
*   **Gestion de Session**: Utilisation directe de `$_SESSION`.

---

## 2. Architecture de l'Application

L'application suit une architecture MVC avec des couches supplémentaires pour la gestion des données.

*   **Point d'entrée**: `public/index.php`
*   **Bootstrap**: `app/bootstrap.php` initialise l'application, l'autoloader (bien que redondant avec Composer), et la base de données.
*   **Routing**: Les requêtes sont acheminées par `app/Core/Router.php` vers les contrôleurs appropriés, définis dans `app/routes.php`.
*   **Contrôleurs (`app/Controllers/`)**: Gèrent la logique métier, interagissent avec les services et les dépôts, et préparent les données pour les vues.
    *   `app/Controllers/BaseController.php` fournit des fonctionnalités communes.
    *   Les sous-répertoires comme `app/Controllers/Api/` organisent les contrôleurs d'API.
*   **Services (`app/Services/`)**: Contiennent la logique métier spécifique et peuvent orchestrer plusieurs dépôts. Ex: `StripeService.php`.
*   **Modèles (`app/Models/`)**: Représentent les entités de la base de données. Ex: `Creator.php`, `Donation.php`.
    *   `app/Core/BaseModel.php` fournit des fonctionnalités de base pour les modèles.
*   **Dépôts (`app/Repositories/`)**: Abstraient les interactions avec la base de données pour des entités spécifiques, offrant une couche de persistance propre. Ex: `CreatorRepository.php`, `DonationRepository.php`.
*   **Vues (`resources/views/`)**: Affichent les données via des templates Twig (`.html.twig`). Le dossier `app/views/` a été supprimé.
    *   `app/Core/View.php` gère le rendu des vues.
*   **Base de Données (`database/`)**: Contient les migrations (`migrations/`) et les schémas (`schema.sql`). `database/migrate.php` est utilisé pour appliquer les migrations.

### 2.1 Flux d'une Requête Typique

1.  **Requête HTTP**: L'utilisateur envoie une requête (GET/POST) à `public/index.php`.
2.  **Initialisation**: `public/index.php` inclut `app/bootstrap.php` qui configure l'environnement, l'autoloader, la base de données, etc.
3.  **Routage**: `app/Core/Router.php` analyse l'URL et la méthode HTTP pour trouver le contrôleur et la méthode correspondante (`app/routes.php`).
4.  **Exécution du Contrôleur**: La méthode du contrôleur est appelée. Elle peut:
    *   Récupérer des données via les dépôts (`app/Repositories/`).
    *   Exécuter de la logique métier via les services (`app/Services/`).
    *   Stocker des données via les dépôts.
    *   Préparer les données pour l'affichage.
5.  **Rendu de la Vue**: Le contrôleur charge une vue (`app/views/` ou `resources/views/`) via `app/Core/View.php`, lui passant les données nécessaires.
6.  **Réponse HTTP**: Le contenu HTML généré est renvoyé au navigateur.

---

## 3. État Actuel du Projet (Post-Audit)

L'audit a mis en lumière les points suivants :

### 3.1 Points Forts

*   **Architecture solide**: Utilisation de patrons de conception modernes (injection de dépendances, pattern Repository).
*   **Protection SQL excellente**: Requêtes préparées systématiques, minimisant les risques d'injection SQL.

### 3.2 Risque Critique

*   **Faille de sécurité XSS (Cross-Site Scripting)**: Le principal point de vulnérabilité. Les vues PHP pures nécessitent un échappement manuel (`htmlspecialchars`), et tout oubli peut entraîner une faille majeure.

### 3.3 Dette Technique

*   **Chargeur de classes redondant**: Un autoloader personnalisé dans `app/bootstrap.php` coexiste avec celui de Composer.
*   **Utilisation de GET pour des actions mutatives**: Certaines actions (ex: suppression) utilisent des requêtes GET au lieu de POST, ce qui est une mauvaise pratique en termes de sécurité et de sémantique HTTP.
*   **Accès direct à `$_SESSION`**: Des classes comme `DonationRepository` accèdent directement à `$_SESSION`, violant la séparation des responsabilités et rendant le code plus difficile à tester.

---

## 4. Travail Effectué

1.  **Audit complet du codebase**: recensement des points forts/faiblesses et cartographie des dettes techniques prioritaires.
2.  **Uniformisation de la gestion des erreurs**: le routeur, les helpers et les contrôleurs utilisent désormais exclusivement les templates Twig (`errors/404.html.twig`, `errors/500.html.twig`), supprimant la dépendance aux anciennes vues PHP pour les pages d'erreur.
3.  **Migration de la page publique créatrice vers Twig**:
    *   Création d'un layout Twig `layouts/public.twig` et de ses partiels (`public_header.twig`, `public_footer.twig`) pour factoriser l'UI publique.
    *   Conversion complète de `app/views/public/creator.php` en `resources/views/public/creator.html.twig` avec échappement systématique et ajout d'un champ CSRF.
    *   Injection des métadonnées nécessaires côté contrôleur (`PublicController::showCreator`) pour simplifier les templates (listes de dons, types de dons, token CSRF, etc.).
4.  **Stabilisation de l'IoC container**: enregistrement explicite de `Database` (singleton) et de `PDO`, suppression des injections invalides sur les modèles, et branchement des repositories sur `Database` pour que la résolution automatique des contrôleurs fonctionne réellement.
5.  **Flux de don public sécurisé**: ajout d'une route `POST /donations/add`, vérification CSRF côté `DonationsController`, validations serveur et persistance via `DonationRepository::addDonation($creatorId, …)` qui n'accède plus directement à `$_SESSION`.
6.  **Migration complète des vues publiques**:
    *   `app/views/public/*.php` (index, creator, creator_profile) ont été supprimées au profit de `resources/views/public/*.html.twig`.
    *   Le layout legacy `app/views/layouts/main.php` n'est plus utilisé (remplacé par `resources/views/layouts/public.twig`).
    *   La page d’accueil (`/`) est désormais rendue via `PublicController::index` → `resources/views/public/index.html.twig`, avec gestion de l’état vide et échappement automatique.
7.  **Hydratation des packs côté public**: `PublicController::showCreator` injecte désormais `PackRepository` et transmet la liste des packs actifs (`PackRepository::getPublicPacksByCreator`) à la vue Twig pour supprimer les sections vides.
8.  **Sécurisation des actions mutatives (packs)**: suppression des routes GET pour la suppression/activation des packs, ajout de formulaires POST avec CSRF côté `resources/views/creator/packs.html.twig` et vérifications serveur dans `PackController`.
9.  **Nettoyage de l’ancien stack de vues PHP**: l’ensemble du dossier `app/views` a été retiré (anciennes pages Auth, Admin, Creator, Donation, etc.). Les contrôleurs restants (`HomeController::index`, `ProfileController::edit`, `DashboardController::profile`, `MediaController::{index,upload}`) redirigent désormais vers les écrans Twig existants plutôt que de rendre ces vues obsolètes. Cela évite toute régression XSS si un rendu PHP était involontairement appelé.

---

## 5. Tâches Restantes et Prochaines Étapes

### 5.1 Priorité Absolue (Sécurité)

1.  **Migration des Vues PHP vers Twig**: ✅ Terminée. Toutes les vues `app/views/**/*.php` ont été converties en templates Twig (`resources/views/`) et le dossier `app/views` a été supprimé.
    *   ✅ Dashboard Admin : Migré vers `resources/views/admin/` (`index.html.twig`, `stats.html.twig`, etc.) avec le layout `creator_dashboard.twig`.
    *   ✅ Code mort supprimé : Routes (`/profile/edit`) et contrôleurs (`MediaController`) inutilisés ont été retirés.
    *   🔜 Prochaine priorité : Injection de dépendances pour la session et refactorisation des accès directs à `$_SESSION`.

### 5.2 Améliorations Backend (Dette Technique)

1.  **Suppression de l'Autoloader Redondant**: Supprimer l'autoloader personnalisé dans `app/bootstrap.php` et s'assurer que Composer gère toutes les dépendances et l'autoloading.
2.  **Refactorisation des Actions Mutatives**: Remplacer les requêtes GET utilisées pour des actions qui modifient les données par des requêtes POST, PUT ou DELETE appropriées (avec protection CSRF si nécessaire). ✅ Packs : suppression/toggle sont désormais en POST + CSRF ; à appliquer aux autres modules (liens, messages, etc.).
3.  **Injection de Dépendances pour `$_SESSION`**: Remplacer les accès directs à `$_SESSION` (notamment dans `DonationRepository`) par une approche basée sur l'injection de dépendances pour un meilleur découplage et testabilité.

### 5.3 Évolutions Techniques Futures (Propositions Utilisateur)

Ces points représentent des évolutions majeures pour moderniser et scaler l'application. Ils seront abordés après la stabilisation et la sécurisation de la base existante.

#### 5.3.1 Stack Frontend

*   **Framework**: Migration vers **Vue.js 3 avec Composition API**.
*   **Styling**: Adoption de **Tailwind CSS** avec un design system personnalisé.
*   **Gestion d’état**: Implémentation de **Pinia**.
*   **Outil de build**: Utilisation de **Vite**.
*   **Tests**: Intégration de **Vitest** pour les tests unitaires et **Cypress** pour les tests end-to-end.

#### 5.3.2 Améliorations Backend

*   **Architecture API**: Évolution vers une API **REST** robuste, avec une option pour explorer **GraphQL** pour des cas d'usage spécifiques.
*   **Temps réel**: Intégration de **connexions WebSocket** pour des fonctionnalités en temps réel (ex: notifications, chat).
*   **Cache**: Implémentation de **Redis** pour optimiser les performances de l'application.
*   **Système de files (Queues)**: Mise en place d'un système de traitement des tâches en arrière-plan (ex: envoi d'emails, traitements longs) pour améliorer la réactivité.
*   **Sécurité**: Renforcement de l'authentification et de l'autorisation (ex: jetons JWT, politiques d'accès plus granulaires).

---

### 5.4 Points à clarifier / anomalies repérées

*   **Schéma Don/Timer**: le repository travaille avec des colonnes (`comment`, `timer_end`, `status`) absentes du `schema.sql` historique. Vérifier que les migrations 004/006 ont bien été exécutées sur chaque environnement ou prévoir un script de migration cohérent.
*   **Routes legacy**: `/profile/edit`, `/dashboard/profile`, `/media` redirigent désormais vers les écrans modernes. Supprimer le code mort correspondant lorsque les derniers usages auront été confirmés.

Ce guide sera mis à jour au fur et à mesure de l'avancement du projet.

## 6. Analyse et Résolution des Erreurs API (en cours)

### 6.1 Problématique Initiale

Un utilisateur a signalé l'erreur `[API Error: An unknown error occurred.]`. L'objectif est de comprendre l'origine de ce message et d'améliorer la gestion des erreurs API pour fournir des retours plus clairs.

### 6.2 Investigation du Codebase

1.  **Recherche du message d'erreur**: Une recherche textuelle de la chaîne `[API Error: An unknown error occurred.]` dans le codebase n'a retourné aucun résultat. Cela suggère que le message n'est pas une constante interne de l'application, mais pourrait provenir d'une source externe ou être généré dynamiquement côté client.

2.  **Gestionnaire global d'exceptions (`public/index.php`)**: Le fichier `public/index.php` contient un gestionnaire d'exceptions global (`set_exception_handler`). Ce dernier affiche un message générique "Une erreur est survenue. Vérifiez les logs pour plus de détails." pour les erreurs non gérées. Cependant, l'erreur signalée par l'utilisateur est différente, ce qui indique qu'elle n'est probablement pas interceptée par ce gestionnaire par défaut, ou qu'elle est traitée spécifiquement pour les requêtes API.

3.  **Contrôleurs API de base (`app/Core/BaseController.php` et `app/Controllers/Api/BaseApiController.php`)**:
    *   `BaseApiController.php` est minimal et hérite de `BaseController.php`.
    *   `BaseController.php` implémente les méthodes `jsonResponse($data, $status)` et `jsonError($message, $status)`. La méthode `jsonError` est conçue pour renvoyer des réponses JSON en cas d'erreur.

4.  **Contrôleur des Outils IA (`app/Controllers/AiToolsController.php`)**:
    *   Ce contrôleur est le point de contact pour les fonctionnalités d'IA et interagit avec l'API Mistral AI via la méthode privée `callMistralApi`.
    *   Les méthodes publiques `enhanceText` et `suggestReply` gèrent la logique métier spécifique et appellent `callMistralApi`. Elles incluent des blocs `try-catch` qui interceptent les exceptions et retournent des réponses JSON structurées du type `json_encode(['error' => 'Erreur interne... Détails: ' . $e->getMessage()])`.
    *   La méthode `callMistralApi` utilise cURL pour communiquer avec l'API Mistral. Elle contient une logique robuste de gestion des erreurs pour les échecs cURL, les problèmes de décodage JSON, et les réponses API malformées.
    *   Un point clé est le bloc suivant dans `callMistralApi`:
        ```php
        if ($apiContent === null) {
             $errorDetail = $responseData['error']['message'] ?? json_encode($responseData);
             error_log("Réponse API invalide ou contenu manquant. Réponse brute: " . $response);
            throw new \Exception("Impossible d'extraire le contenu de la réponse de l'API. Détail: " . $errorDetail);
        }
        ```
        Ce code intercepte les réponses de l'API Mistral qui ne contiennent pas le champ `content` attendu, et tente d'extraire un message d'erreur du champ `error['message']` de la réponse Mistral si présent, sinon il encode la réponse complète. Cette exception est ensuite propagée et attrapée par `enhanceText` ou `suggestReply`.

### 6.3 Hypothèse Actuelle

L'erreur `[API Error: An unknown error occurred.]` provient très probablement de l'API externe Mistral AI elle-même. Lorsque l'API Mistral rencontre une erreur non spécifique, elle renvoie une réponse JSON contenant `{"error": {"message": "An unknown error occurred."}}`.

Cette réponse est alors interceptée par la méthode `callMistralApi` de `AiToolsController`. Le bloc de gestion d'erreur dans `callMistralApi` (décrit ci-dessus) va créer une exception PHP avec ce message d'erreur de l'API Mistral comme détail.

Cette exception est ensuite attrapée par le bloc `try-catch` dans `enhanceText` ou `suggestReply`, qui formate une réponse JSON contenant ce détail d'erreur (par exemple, `{"error": "Erreur interne... Détails: Impossible d'extraire le contenu de la réponse de l'API. Détail: {\"error\":{\"message\":\"An unknown error occurred.\"}}"`).

Il est probable que le code JavaScript côté client (`public/assets/js/ai_tools.js`) qui consomme cette réponse API, interprète ce JSON, extrait la partie `An unknown error occurred.` et la réaffiche à l'utilisateur, potentiellement en la préfixant avec `[API Error: ]`.

### 6.4 Prochaines Étapes

1.  **Vérifier le code JavaScript côté client**: Examiner `public/assets/js/ai_tools.js` pour confirmer comment les erreurs API sont traitées et affichées.
2.  **Améliorer la gestion des erreurs côté serveur**: Rendre les messages d'erreur API plus spécifiques et conviviaux, potentiellement en mappant les codes d'erreur ou messages de l'API Mistral à des messages internes plus clairs.
