<?php

class FileUploader {
    private $config;
    
    public function __construct() {
        $this->config = require_once APP_PATH . '/../config/upload.php';
    }
    
    public function uploadImage($file, $type) {
        // Vérifier si le type est valide
        if (!isset($this->config['max_size'][$type])) {
            throw new Exception("Type d'upload invalide");
        }
        
        // Vérifier si le fichier existe
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new Exception("Aucun fichier n'a été uploadé");
        }
        
        // Vérifier la taille du fichier
        if ($file['size'] > $this->config['max_size'][$type]) {
            throw new Exception("Le fichier est trop volumineux");
        }
        
        // Vérifier le type MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mime_type, $this->config['allowed_types'])) {
            throw new Exception("Type de fichier non autorisé");
        }
        
        // Vérifier les dimensions de l'image
        list($width, $height) = getimagesize($file['tmp_name']);
        if ($width > $this->config['max_dimensions'][$type]['width'] || 
            $height > $this->config['max_dimensions'][$type]['height']) {
            throw new Exception("Les dimensions de l'image sont trop grandes");
        }
        
        // Créer le dossier de destination s'il n'existe pas
        if (!is_dir($this->config['upload_paths'][$type])) {
            mkdir($this->config['upload_paths'][$type], 0755, true);
        }
        
        // Générer un nom de fichier unique
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid($type . '_') . '.' . $extension;
        $filepath = $this->config['upload_paths'][$type] . '/' . $filename;
        
        // Déplacer le fichier
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new Exception("Erreur lors de l'upload du fichier");
        }
        
        // Retourner l'URL publique
        return $this->config['public_urls'][$type] . '/' . $filename;
    }
    
    public function deleteFile($path) {
        $fullPath = PUBLIC_PATH . $path;
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }
}
