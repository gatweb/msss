# DEVBOOK - Architecture et Découplage

## Injection de Dépendances (DI)

### Principe
Au lieu d'utiliser des appels statiques (ex: `Database::getInstance()`), on injecte les dépendances (Database, Auth, View, etc.) dans les constructeurs des contrôleurs. Cela facilite les tests, réduit le couplage, et rend l'architecture plus flexible.

**Exemple avant**
```php
class DashboardController extends BaseController {
    public function index() {
        $db = Database::getInstance();
        // ...
    }
}
```

**Exemple après**
```php
use App\Core\Database;
use App\Core\View;
use App\Core\Auth;

class DashboardController {
    private $db;
    private $view;
    private $auth;

    public function __construct(Database $db, View $view, Auth $auth) {
        $this->db = $db;
        $this->view = $view;
        $this->auth = $auth;
    }

    public function index() {
        // Utiliser $this->db, $this->view, $this->auth
    }
}
```

### Adaptation du Router
Le Router doit être capable d'instancier les contrôleurs avec leurs dépendances. Exemple manuel :
```php
// Dans Router.php
$db = new Database(...);
$view = new View(...);
$auth = new Auth(...);
$controller = new DashboardController($db, $view, $auth);
```

## Repository Pattern

### Principe
On centralise la logique d'accès aux données dans des classes Repository (ex: `CreatorRepository`). Les modèles deviennent de simples objets de données (DTO), et les contrôleurs n'ont plus à manipuler directement la base de données.

**Exemple concret sur la méthode index()**

**Avant**
```php
public function index() {
    $creator = $this->creatorModel->getCreatorById($creatorId);
    // ...
}
```

**Après (avec DI et Repository)**
```php
public function index() {
    $creator = $this->creatorRepo->findById($creatorId);
    // ...
}
```

On utilise désormais le repository injecté pour accéder aux données, ce qui rend le contrôleur plus souple et testable.

## Bénéfices
- Testabilité accrue
- Couplage réduit
- Architecture plus claire et évolutive

## Pour aller plus loin
- Utiliser un Container DI externe (PHP-DI, Symfony DI, etc.)
- Ajouter des interfaces pour faciliter le remplacement des implémentations
- Étendre le pattern Repository à toutes les entités métier

---

# Design System & Charte Graphique

## Material Design
- Utilisation de Materialize CSS pour tous les composants (cards, boutons, navigation, modals)
- Palette de couleurs douce : gris clair (`grey lighten-3`), bleu foncé (`#1976d2`), texte gris/bleu foncé
- Icônes Material partout (`<i class="material-icons">`)
- Police Roboto, taille de base 16px
- Coins arrondis sur tous les boutons, cards et champs
- Effets hover/focus doux, accessibilité renforcée

## Conventions CSS
- Tous les boutons et liens principaux utilisent `.btn` avec Materialize (voir layout)
- Style global appliqué dans le layout pour garantir la cohérence
- Pas de CSS custom lourd, tout passe par Materialize + quelques overrides doux
- Les `<a>` simples sont sobres, soulignés uniquement au survol
- Responsive : grille Materialize (`row`, `col s12 m6 l4`, etc.)

## Structure des vues
- Toutes les vues sont rendues via un layout unique `material_base.php`
- Header, navigation, footer inclus dans le layout
- Les vues n’incluent que le contenu principal (`$content`)
- Utilisation de `ob_start()` / `ob_get_clean()` pour injecter le contenu dans le layout
- Les modals sont définis dans chaque vue si besoin, avec la classe `modal` Materialize

---

# Navigation & Expérience Utilisateur (UX)

## Navigation
- Barre de navigation principale en haut, responsive (menu mobile inclus)
- Navigation rapide vers Profil, Packs, Liens, Déconnexion
- Footer avec liens de rappel

## UX & Accessibilité
- Boutons et liens accessibles au clavier (focus visible)
- Couleurs contrastées pour le texte et les boutons
- Feedback visuel sur les actions (hover, focus, disabled)
- Formulaires avec labels clairs, validation côté client et côté serveur
- Modals utilisables au clavier et accessibles

---

# Pour aller plus loin (Design)
- Personnaliser la palette Materialize (voir [Materialize Color Tool](https://materializecss.com/color.html))
- Ajouter des animations douces (Materialize ou CSS)
- Définir des variables CSS custom si besoin
- Documenter les composants réutilisables (cards, modals, forms)

---
