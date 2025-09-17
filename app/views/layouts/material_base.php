<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Mon Application' ?></title>
    <!-- Materialize CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    
    <!-- Material Icons -->
    <link href="/assets/css/material-icons-local.css" rel="stylesheet">
    <!-- Roboto Font -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet">
    <style>
        body, html { font-family: 'Roboto', Arial, sans-serif; background: #f5f5f5; }
        main { min-height: 70vh; }
        .brand-logo { font-weight: 500; }
        /* Style boutons neutre et moderne */
        a.btn, button.btn, .btn {
          background: #fff !important;
          color: #222 !important;
          border: 1px solid #e0e0e0 !important;
          border-radius: 24px !important;
          box-shadow: none !important;
          font-weight: 500;
          letter-spacing: 0.2px;
          transition: background 0.18s, color 0.18s, box-shadow 0.18s;
        }
        a.btn:hover, button.btn:hover, .btn:hover {
          background: #f2f2f2 !important;
          color: #111 !important;
          box-shadow: 0 2px 8px 0 rgba(60,60,60,0.07);
        }
        /* Boutons secondaires */
        .btn.btn-secondary {
          background: #f7f7f7 !important;
          color: #666 !important;
          border: 1px solid #e0e0e0 !important;
        }
        .btn.btn-secondary:hover {
          background: #ededed !important;
          color: #333 !important;
        }
        /* Boutons outline */
        .btn-outline {
          background: transparent !important;
          color: #666 !important;
          border: 2px solid #bbb !important;
        }
        .btn-outline:hover {
          background: #f7f7f7 !important;
          color: #222 !important;
        }
        /* Petits boutons */
        .btn.btn-sm {
          padding: 0.3rem 1rem;
          font-size: 0.95em;
        }

        a, a:visited {
          color: #333;
          text-decoration: none;
        }
        a:hover, a:focus {
          color: #1976d2;
          text-decoration: underline;
        }
        a:focus, button:focus {
          outline: 2px solid #1976d2;
          outline-offset: 2px;
        }
    </style>
    <?php if (isset($head)) echo $head; ?>

<!-- TEST HEAD MATERIAL BASE -->
</head>
<body>
    <header>
        <nav class="grey lighten-2 z-depth-0">
            <div class="nav-wrapper container">
                <a href="/" class="brand-logo grey-text text-darken-3">MSS Stephanie</a>
                <a href="#" data-target="mobile-nav" class="sidenav-trigger"><i class="material-icons">menu</i></a>
                <ul class="right hide-on-med-and-down">
                    <li><a href="/profile" class="btn waves-effect waves-light grey lighten-3 grey-text text-darken-3">Profil</a></li>
                    <li><a href="/messages" class="btn waves-effect waves-light grey lighten-3 grey-text text-darken-3">Messages</a></li>
                    <li><a href="/logout" class="btn waves-effect waves-light grey lighten-3 grey-text text-darken-3">Déconnexion</a></li>
                </ul>
            </div>
        </nav>
        <ul class="sidenav" id="mobile-nav">
            <li><a href="/profile" class="btn waves-effect waves-light grey lighten-3 grey-text text-darken-3">Profil</a></li>
            <li><a href="/messages" class="btn waves-effect waves-light grey lighten-3 grey-text text-darken-3">Messages</a></li>
            <li><a href="/logout" class="btn waves-effect waves-light grey lighten-3 grey-text text-darken-3">Déconnexion</a></li>
        </ul>
    </header>
    <main class="container" style="margin-top: 2rem;">
        <?php if (isset($content)) echo $content; ?>
    </main>
    <footer class="page-footer grey lighten-3">
        <div class="container">
            <div class="row">
                <div class="col l6 s12">
                    <h5 class="grey-text text-darken-3">MSS Stephanie</h5>
                    <p class="grey-text text-darken-2">Plateforme créatrice & donateur - Design Material</p>
                </div>
                <div class="col l4 offset-l2 s12">
                    <h5 class="grey-text text-darken-3">Navigation</h5>
                    <ul>
                        <li><a class="grey-text text-darken-2" href="/profile">Profil</a></li>
                        <li><a class="grey-text text-darken-2" href="/messages">Messages</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="footer-copyright grey-text text-darken-2">
            <div class="container">
            &copy; <?= date('Y') ?> MSS Stephanie
            © <?= date('Y') ?> MSS Stephanie
            </div>
        </div>
    </footer>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script>document.addEventListener('DOMContentLoaded', function() { M.AutoInit(); });</script>
    <?php if (isset($footer_scripts)) echo $footer_scripts; ?>
</body>
</html>
