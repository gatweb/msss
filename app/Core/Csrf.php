<?php

namespace App\Core;

class Csrf
{
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * Génère un token CSRF unique, le stocke en session et le retourne.
     *
     * @return string Le token CSRF généré.
     */
    public function generateToken(): string
    {
        // Génère un token sécurisé
        $token = bin2hex(random_bytes(32));

        // Stocke le token en session
        $this->session->set('csrf_token', $token);

        return $token;
    }

    /**
     * Vérifie si le token fourni correspond à celui stocké en session.
     *
     * @param string|null $token Le token à vérifier (provenant du formulaire/requête).
     * @return bool True si le token est valide, false sinon.
     */
    public function verifyToken(?string $token): bool
    {
        // Vérifie si un token est présent en session et si le token fourni correspond
        $storedToken = $this->session->get('csrf_token');
        if ($storedToken && $token !== null && hash_equals($storedToken, $token)) {
            // Optionnel : Supprimer le token après usage
            // $this->session->remove('csrf_token');
            return true;
        }

        return false;
    }
}
