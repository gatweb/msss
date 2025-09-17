# Audit exhaustif des liens - Msss (avril 2025)

Ce document liste tous les liens (href, actions, redirections JS) présents dans les vues, organisés par fichier et par espace. Il permet de vérifier la cohérence, la sécurité et la logique de navigation.

---

## Légende
- **[PUB]** : Espace public (visiteurs, non connectés)
- **[CREA]** : Dashboard créatrice
- **[ADMIN]** : Dashboard admin
- **[DONOR]** : Espace donateur
- **[USER]** : Espace utilisateur classique

---

## Liens par fichier

### /app/views/layouts/default.php [PUB]
- Accueil : `/`
- Accès conditionnel :
  - Mon Profil : `/profile`
  - Administration : `/profile/admin`
  - Connexion : `/login`
  - Inscription : `/register`
  - Déconnexion : `/logout`

### /app/views/layouts/creator_header.php [CREA]
- Sidebar :
  - Accueil dashboard : `/dashboard`
  - Statistiques : `/dashboard` ou `/creator/stats`
  - Packs : `/creator/packs`
  - Publications : `/creator/posts`
  - Galerie : `/creator/gallery`
  - Dons : `/dashboard/donations`
  - Donateurs : `/dashboard/donators`
  - Messages : `/creator/messages`
  - Profil : `/creator/profile`
  - Liens : `/creator/links`
  - Paramètres : `/creator/settings`
  - Voir ma page : `/creator/{id}` (target blank)
  - Déconnexion : `/logout`

### /app/views/layouts/admin_header.php [ADMIN]
- Sidebar :
  - Tableau de bord : `/profile/admin`
  - Transactions : `/dashboard/donations`
  - Créatrices : `/profile/admin/creators`
  - Paramètres : `/admin/settings`
  - Retour au site : `/`
  - Déconnexion : `/logout`

### /app/views/layouts/donor_header.php [DONOR]
- Sidebar :
  - Créatrices suivies : `/donor`
  - Découvrir : `/donor/discover`
  - Historique des dons : `/donor/donations`
  - Abonnements : `/donor/subscriptions`
  - Profil : `/donor/profile`
  - Notifications : `/donor/notifications`
  - Paramètres : `/donor/settings`
  - Déconnexion : `/logout`

### /app/views/layouts/user_header.php [USER]
- Topbar :
  - Accueil : `/`
  - Mon Profil : `/profile`
  - Déconnexion : `/logout`
  - Connexion : `/login` (si non connecté)
  - Inscription : `/register` (si non connecté)

### /app/views/public/index.php [PUB]
- Voir une créatrice : `/creator/{id}`

### /app/views/public/creator_profile.php [PUB]
- Voir un pack : `/donate/{creator_id}/pack/{pack_id}`
- Lien externe : `{link['url']}` (target blank)
- Se connecter : `/login`

### /app/views/public/creator.php [PUB]
- Formulaire de don : `/donations/add`
- Lien externe : `{link['url']}` (target blank)

### /app/views/donation/links.php [PUB]
- Lien PayPal : `{creator['paypal_link']}` (target blank)
- Lien Throne : `{creator['throne_link']}` (target blank)
- Lien Amazon : `{creator['amazon_link']}` (target blank)
- Autres liens : `{link}` (target blank)

### /app/views/profile/view.php [USER]
- Éditer profil : `/profile/edit`
- Voir liens de soutien : `/donation/links/{creator_id}`
- Contacter : `mailto:{creator['email']}`
- Voir galerie : `/media/gallery/{creator_id}`

### /app/views/profile/edit.php [USER]
- Voir profil public : `/profile`

### /app/views/errors/403.php, /500.php [PUB]
- Retour JS : `javascript:history.back()`
- Accueil : `/`

### /app/views/donation/form.php [PUB]
- fetch `/donation/initiate`
- Redirection JS : `window.location.href = data.url;`

---

## Points de contrôle
- **Aucun menu public dans les dashboards** (créatrice, admin, donateur)
- **Liens critiques** protégés côté serveur
- **Liens externes** toujours en `target="_blank"` + `rel="noopener noreferrer"`
- **Navigation claire, sans doublon**

---

_Mis à jour automatiquement le 21/04/2025 par Cascade AI._
