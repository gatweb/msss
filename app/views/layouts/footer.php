<?php if (!empty($GLOBALS['LEGACY_LAYOUT'])): ?>
        </div>
    </main>
<?php endif; ?>

<footer class="site-footer">
    <div class="site-footer__inner">
        <p>&copy; <?= date('Y') ?> Msss &mdash; Tous droits réservés.</p>
        <nav class="site-footer__links">
            <a href="mailto:contact@maitress.es">Support</a>
            <a href="/mentions-legales">Mentions légales</a>
        </nav>
    </div>
</footer>

<?php if (!empty($GLOBALS['LEGACY_LAYOUT'])): ?>
</body>
</html>
<?php unset($GLOBALS['LEGACY_LAYOUT']); ?>
<?php endif; ?>
