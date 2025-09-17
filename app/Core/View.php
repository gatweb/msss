<?php

namespace App\Core;

class View {
    private $layout = 'default'; // Default layout if not specified
    private $viewPath;
    private $data = [];
    private $title = '';
    private $scripts = [];

    public function __construct() {
        $this->viewPath = __DIR__ . '/../../app/views/';
    }

    public function setLayout($layout) {
        $this->layout = $layout;
    }

    public function render($view, $data = [], $layout = null) {
        $this->data = array_merge($this->data, $data);
        $viewFile = $this->viewPath . $view . '.php';
        
        if (!file_exists($viewFile)) {
            throw new \Exception("Vue non trouvée : $view");
        }

        // Extraire les données pour les rendre disponibles dans la vue et le layout
        extract($this->data);

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // Déterminer le layout à utiliser
        $layoutToUse = $layout ?? $this->layout;

        // Par défaut, la sortie finale est juste le contenu de la vue
        $finalOutput = $content;

        // Charger le layout
        $layoutFile = $this->viewPath . 'layouts/' . $layoutToUse . '.php';
        if (file_exists($layoutFile)) {
            ob_start();
            require $layoutFile; // Le layout utilise la variable $content
            $finalOutput = ob_get_clean(); // Capturer la sortie complète du layout
        }

        echo $finalOutput; // Afficher le résultat final
    }

    public function partial($partial, $data = []) {
        $partialFile = $this->viewPath . 'partials/' . $partial . '.php';
        if (!file_exists($partialFile)) {
            throw new \Exception("Partial non trouvé : $partial");
        }
        extract(array_merge($this->data, $data));
        require $partialFile;
    }

    public function escape($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getTitle() {
        return $this->title;
    }

    public function addScript($script) {
        $this->scripts[] = $script;
    }

    public function getScripts() {
        return $this->scripts;
    }

    public function setData($data) {
        $this->data = array_merge($this->data, $data);
    }
}
