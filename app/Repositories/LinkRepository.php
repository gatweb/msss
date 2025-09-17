<?php
namespace App\Repositories;

use App\Core\Database;

class LinkRepository
{
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function getLinksByCreator($creatorId)
    {
        $sql = "SELECT * FROM links WHERE creator_id = :creator_id";
        $stmt = $this->db->execute($sql, [':creator_id' => $creatorId]);
        return $stmt->fetchAll();
    }

    /**
     * Trouve un lien par son ID.
     * @param int $id ID du lien.
     * @return array|false Retourne le lien ou false s'il n'est pas trouvé.
     */
    public function findById(int $id)
    {
        $sql = "SELECT * FROM links WHERE id = :id";
        $stmt = $this->db->execute($sql, [':id' => $id]);
        return $stmt->fetch(); // Utilise fetch() pour un seul résultat
    }

    /**
     * Crée un nouveau lien.
     * @param array $data Données du lien (doit inclure creator_id, title, url, et optionnellement icon).
     * @return int|false Retourne l'ID du lien créé ou false en cas d'échec.
     */
    public function create(array $data)
    {
        $sql = "INSERT INTO links (creator_id, title, url, icon) 
                VALUES (:creator_id, :title, :url, :icon)";
        
        $params = [
            ':creator_id' => $data['creator_id'],
            ':title' => $data['title'],
            ':url' => $data['url'],
            ':icon' => $data['icon'] ?? null
        ];
        
        if ($this->db->execute($sql, $params)) {
            return $this->db->lastInsertId();
        } else {
            error_log("LinkRepository::create - Erreur lors de l'exécution de la requête SQL.");
            return false;
        }
    }

    /**
     * Met à jour un lien existant.
     * @param int $id ID du lien à mettre à jour.
     * @param array $data Données à mettre à jour (title, url, icon).
     * @return bool Retourne true si la mise à jour a réussi, false sinon.
     */
    public function update(int $id, array $data)
    {
        $sql = "UPDATE links SET 
                    title = :title, 
                    url = :url, 
                    icon = :icon 
                WHERE id = :id";
        
        $params = [
            ':id' => $id,
            ':title' => $data['title'],
            ':url' => $data['url'],
            ':icon' => $data['icon'] ?? null
        ];
        
        $stmt = $this->db->execute($sql, $params);
        return $stmt->rowCount() > 0; // Vérifie si au moins une ligne a été affectée
    }

    /**
     * Supprime un lien par son ID, en vérifiant qu'il appartient au créateur donné.
     * @param int $id ID du lien à supprimer.
     * @param int $creatorId ID du créateur propriétaire.
     * @return bool Retourne true si la suppression a réussi, false sinon.
     */
    public function deleteByIdAndCreator(int $id, int $creatorId)
    {
        $sql = "DELETE FROM links WHERE id = :id AND creator_id = :creator_id";
        $params = [
            ':id' => $id,
            ':creator_id' => $creatorId
        ];
        
        $stmt = $this->db->execute($sql, $params);
        // Vérifie si exactement une ligne a été supprimée pour confirmer le succès
        return $stmt->rowCount() === 1; 
    }
}
