<?php

namespace App\Core;

class Flash {
    /**
     * Ajoute un message flash de succès
     *
     * @param string $message Le message à afficher
     * @return void
     */
    public function success($message) {
        $_SESSION['flash'] = [
            'type' => 'success',
            'message' => $message
        ];
    }

    /**
     * Ajoute un message flash d'erreur
     *
     * @param string $message Le message à afficher
     * @return void
     */
    public function error($message) {
        $_SESSION['flash'] = [
            'type' => 'error',
            'message' => $message
        ];
    }

    /**
     * Récupère et supprime le message flash
     *
     * @return array|null Le message flash ou null s'il n'y en a pas
     */
    public function get() {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $flash;
    }
}
