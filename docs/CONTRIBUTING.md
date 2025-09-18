# Guide de contribution

## Préparer votre environnement

1. **Forker et cloner** le dépôt.
2. Créer une branche descriptive (`feature/ajout-stripe`, `fix/bug-route`, etc.).
3. Installer les dépendances PHP :
   ```bash
   composer install
   ```
4. Copier `.env.example` vers `.env` et ajuster les variables locales si vous avez besoin d'exécuter l'application.

## Workflow de développement

1. **Planifier la modification** en ouvrant un ticket ou en commentant un ticket existant.
2. **Coder** en respectant les conventions définies dans le `README.md`.
3. **Documenter** toute modification significative dans les fichiers README/Docs concernés.
4. **Mettre à jour ou ajouter des tests** lorsque du code applicatif est modifié.

## Vérifications automatiques

Les scripts Composer suivants centralisent les contrôles qualité :

- `composer test` — exécute PHPUnit (tests unitaires/services et tests légers de routes).
- `composer check:types` — lance PHPStan avec la configuration du projet.
- `composer lint` — vérifie le style de code via PHP-CS-Fixer en mode dry-run.
- `composer lint:fix` — applique les corrections automatiques de style.
- `composer check` — exécute successivement lint, analyse statique et tests.

> 💡 **Astuce** : exécutez `composer check` avant d'ouvrir une Pull Request pour détecter les régressions en une seule commande.

## Bonnes pratiques Git

- Commits atomiques avec un message impératif court (`fix`, `add`, `update`).
- Rebase avant d'ouvrir la PR pour garder un historique propre.
- Nettoyer les fichiers temporaires et vérifier `git status` avant de pousser.

## Processus de revue

1. Créer la Pull Request en détaillant le contexte, la solution retenue et les impacts.
2. Vérifier que la CI passe et que les reviewers ont accès aux captures d'écran lorsque l'interface change.
3. Intégrer les retours en ajoutant des commits (ne pas forcer le push sur la branche partagée sans discussion).
4. Une fois approuvée, la PR peut être fusionnée après validation finale.

Merci de contribuer à MSSS ! Chaque amélioration aide l'équipe à livrer une plateforme robuste et maintenable.
