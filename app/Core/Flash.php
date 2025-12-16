<?php

namespace App\Core;

class Flash {
    private $session;

    public function __construct(Session $session) {
        $this->session = $session;
    }

    /**
     * Ajoute un message flash de succès
     *
     * @param string $message Le message à afficher
     * @return void
     */
    public function success($message) {
        $this->session->set('flash', [
            'type' => 'success',
            'message' => $message
        ]);
    }

    /**
     * Ajoute un message flash d'erreur
     *
     * @param string $message Le message à afficher
     * @return void
     */
    public function error($message) {
        $this->session->set('flash', [
            'type' => 'error',
            'message' => $message
        ]);
    }

    /**
     * Récupère et supprime le message flash
     *
     * @return array|null Le message flash ou null s'il n'y en a pas
     */
    public function get() {
        $flash = $this->session->get('flash');
        $this->session->remove('flash');
        return $flash;
    }
}
