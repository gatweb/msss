<?php

namespace App\Models;

class Media extends BaseModel {
    protected $table = 'media';
    private $allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif'];
    private $allowedVideoTypes = ['video/mp4', 'video/webm'];
    private $maxFileSize = 50 * 1024 * 1024; // 50MB
    
    /**
     * Crée un nouveau média
     */
    public function create($data) {
        try {
            $sql = "INSERT INTO media (
                creator_id, type, title, description, filename,
                original_filename, mime_type, size, width, height,
                duration, thumbnail, status
            ) VALUES (
                :creator_id, :type, :title, :description, :filename,
                :original_filename, :mime_type, :size, :width, :height,
                :duration, :thumbnail, :status
            )";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'creator_id' => $data['creator_id'],
                'type' => $data['type'],
                'title' => $data['title'] ?? null,
                'description' => $data['description'] ?? null,
                'filename' => $data['filename'],
                'original_filename' => $data['original_filename'],
                'mime_type' => $data['mime_type'],
                'size' => $data['size'],
                'width' => $data['width'] ?? null,
                'height' => $data['height'] ?? null,
                'duration' => $data['duration'] ?? null,
                'thumbnail' => $data['thumbnail'] ?? null,
                'status' => 'active'
            ]);
            
            return $this->pdo->lastInsertId();
        } catch (\PDOException $e) {
            error_log("Erreur lors de la création du média : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Met à jour un média
     */
    public function update($id, $data) {
        try {
            $sql = "UPDATE media SET 
                title = :title,
                description = :description,
                status = :status,
                updated_at = CURRENT_TIMESTAMP
                WHERE id = :id AND creator_id = :creator_id";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'id' => $id,
                'creator_id' => $data['creator_id'],
                'title' => $data['title'],
                'description' => $data['description'],
                'status' => $data['status']
            ]);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la mise à jour du média : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Supprime un média et ses fichiers associés
     */
    public function deleteMedia($id, $creatorId) {
        try {
            // Récupérer les informations du fichier avant la suppression
            $media = $this->getById($id, $creatorId);
            if (!$media) {
                return false;
            }
            
            // Supprimer le fichier physique
            $filepath = UPLOAD_PATH . '/' . $media['filename'];
            if (file_exists($filepath)) {
                unlink($filepath);
            }
            
            // Supprimer la miniature si elle existe
            if ($media['thumbnail']) {
                $thumbnailPath = UPLOAD_PATH . '/' . $media['thumbnail'];
                if (file_exists($thumbnailPath)) {
                    unlink($thumbnailPath);
                }
            }
            
            // Supprimer l'enregistrement de la base de données
            $sql = "DELETE FROM media WHERE id = :id AND creator_id = :creator_id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'id' => $id,
                'creator_id' => $creatorId
            ]);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la suppression du média : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupère un média par son ID
     */
    public function getById($id, $creatorId) {
        try {
            $sql = "SELECT * FROM media WHERE id = :id AND creator_id = :creator_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'id' => $id,
                'creator_id' => $creatorId
            ]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération du média : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Liste les médias d'une créatrice
     */
    public function listByCreator($creatorId, $type = null, $page = 1, $limit = 20) {
        try {
            $offset = ($page - 1) * $limit;
            $params = ['creator_id' => $creatorId];
            
            $sql = "SELECT * FROM media WHERE creator_id = :creator_id";
            if ($type) {
                $sql .= " AND type = :type";
                $params['type'] = $type;
            }
            
            $sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération des médias : " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Vérifie si un type de fichier est autorisé
     */
    public function isAllowedType($mimeType) {
        return in_array($mimeType, array_merge($this->allowedImageTypes, $this->allowedVideoTypes));
    }
    
    /**
     * Vérifie si la taille du fichier est autorisée
     */
    public function isAllowedSize($size) {
        return $size <= $this->maxFileSize;
    }
    
    /**
     * Génère un nom de fichier unique
     */
    public function generateFilename($originalFilename, $mimeType) {
        $extension = $this->getExtensionFromMimeType($mimeType);
        return uniqid() . '_' . time() . '.' . $extension;
    }
    
    /**
     * Obtient l'extension à partir du type MIME
     */
    private function getExtensionFromMimeType($mimeType) {
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'video/mp4' => 'mp4',
            'video/webm' => 'webm'
        ];
        
        return $extensions[$mimeType] ?? 'bin';
    }
}
