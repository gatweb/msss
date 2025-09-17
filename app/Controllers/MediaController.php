<?php

namespace App\Controllers;

use App\Models\Media;

class MediaController extends BaseController {
    private $mediaModel;
    
    public function __construct() {
        parent::__construct();
        $this->mediaModel = new Media();
    }
    
    /**
     * Liste les médias d'une créatrice
     */
    public function index() {
        $this->requireAuth();
        
        $type = $_GET['type'] ?? null;
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = 20;
        
        $media = $this->mediaModel->listByCreator(
            $_SESSION['user_id'],
            $type,
            $page,
            $limit
        );
        
        $this->render('media/index', [
            'pageTitle' => 'Mes médias',
            'media' => $media,
            'currentPage' => $page,
            'type' => $type
        ]);
    }
    
    /**
     * Affiche le formulaire d'upload
     */
    public function upload() {
        $this->requireAuth();
        
        $this->render('media/upload', [
            'pageTitle' => 'Ajouter un média',
            'maxFileSize' => $this->mediaModel->maxFileSize
        ]);
    }
    
    /**
     * Traite l'upload d'un fichier
     */
    public function store() {
        $this->requireAuth();
        
        try {
            if (!isset($_FILES['file'])) {
                throw new \Exception('Aucun fichier uploadé');
            }
            
            $file = $_FILES['file'];
            if ($file['error'] !== UPLOAD_ERR_OK) {
                throw new \Exception('Erreur lors de l\'upload');
            }
            
            // Vérifications de base
            if (!$this->mediaModel->isAllowedType($file['type'])) {
                throw new \Exception('Type de fichier non autorisé');
            }
            
            if (!$this->mediaModel->isAllowedSize($file['size'])) {
                throw new \Exception('Fichier trop volumineux');
            }
            
            // Génération du nom de fichier unique
            $filename = $this->mediaModel->generateFilename(
                $file['name'],
                $file['type']
            );
            
            // Déplacement du fichier
            $uploadPath = UPLOAD_PATH . '/' . $filename;
            if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                throw new \Exception('Erreur lors du déplacement du fichier');
            }
            
            // Création de l'enregistrement
            $mediaData = [
                'creator_id' => $_SESSION['user_id'],
                'type' => strpos($file['type'], 'image/') === 0 ? 'image' : 'video',
                'title' => $_POST['title'] ?? null,
                'description' => $_POST['description'] ?? null,
                'filename' => $filename,
                'original_filename' => $file['name'],
                'mime_type' => $file['type'],
                'size' => $file['size']
            ];
            
            // Pour les images, récupérer les dimensions
            if ($mediaData['type'] === 'image') {
                $imageInfo = getimagesize($uploadPath);
                if ($imageInfo) {
                    $mediaData['width'] = $imageInfo[0];
                    $mediaData['height'] = $imageInfo[1];
                }
            }
            
            $mediaId = $this->mediaModel->create($mediaData);
            
            if ($mediaId) {
                $this->jsonResponse([
                    'status' => 'success',
                    'id' => $mediaId,
                    'message' => 'Média uploadé avec succès'
                ]);
            } else {
                throw new \Exception('Erreur lors de l\'enregistrement');
            }
            
        } catch (\Exception $e) {
            $this->jsonResponse([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
    /**
     * Met à jour les informations d'un média
     */
    public function update($id) {
        $this->requireAuth();
        
        try {
            $media = $this->mediaModel->getById($id, $_SESSION['user_id']);
            if (!$media) {
                throw new \Exception('Média non trouvé');
            }
            
            $result = $this->mediaModel->update($id, [
                'creator_id' => $_SESSION['user_id'],
                'title' => $_POST['title'] ?? null,
                'description' => $_POST['description'] ?? null,
                'status' => $_POST['status'] ?? 'active'
            ]);
            
            if ($result) {
                $this->jsonResponse([
                    'status' => 'success',
                    'message' => 'Média mis à jour avec succès'
                ]);
            } else {
                throw new \Exception('Erreur lors de la mise à jour');
            }
            
        } catch (\Exception $e) {
            $this->jsonResponse([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
    /**
     * Supprime un média
     */
    public function delete($id) {
        $this->requireAuth();
        
        try {
            $media = $this->mediaModel->getById($id, $_SESSION['user_id']);
            if (!$media) {
                throw new \Exception('Média non trouvé');
            }
            
            if ($this->mediaModel->deleteMedia($id, $_SESSION['user_id'])) {
                $this->jsonResponse([
                    'status' => 'success',
                    'message' => 'Média supprimé avec succès'
                ]);
            } else {
                throw new \Exception('Erreur lors de la suppression');
            }
            
        } catch (\Exception $e) {
            $this->jsonResponse([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
