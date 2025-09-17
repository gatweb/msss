<?php

namespace App\Core;

class Csrf
{
    /**
     * Génère un token CSRF unique, le stocke en session et le retourne.
     *
     * @return string Le token CSRF généré.
     */
    public static function generateToken(): string
    {
        // S'assure que la session est démarrée
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Génère un token sécurisé
        $token = bin2hex(random_bytes(32));

        // Stocke le token en session
        $_SESSION['csrf_token'] = $token;

        return $token;
    }

    /**
     * Vérifie si le token fourni correspond à celui stocké en session.
     *
     * @param string|null $token Le token à vérifier (provenant du formulaire/requête).
     * @return bool True si le token est valide, false sinon.
     */
    public static function verifyToken(?string $token): bool
    {
        // S'assure que la session est démarrée
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Vérifie si un token est présent en session et si le token fourni correspond
        if (isset($_SESSION['csrf_token']) && $token !== null && hash_equals($_SESSION['csrf_token'], $token)) {
            // Optionnel : Supprimer le token après usage pour éviter les attaques de rejeu (single-use tokens)
            // unset($_SESSION['csrf_token']);
            return true;
        }

        return false;
    }
}
