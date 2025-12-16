<?php
namespace App\Repositories;

use App\Core\Database;
use PDO;

class CreatorRepository
{
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function findById($id)
    {
        $sql = "SELECT * FROM creators WHERE id = :id";
        $stmt = $this->db->execute($sql, [':id' => $id]);
        return $stmt->fetch();
    }

    public function getActiveCreators()
    {
        $sql = "SELECT * FROM creators WHERE is_active = 1";
        $stmt = $this->db->execute($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ajoute d'autres méthodes métier ici (findAll, findByEmail, etc.)

    public function getCreatorByUsername($username)
    {
        $sql = "SELECT * FROM creators WHERE username = :username OR id = :username"; // Support ID or Username
        $stmt = $this->db->execute($sql, [':username' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getCreatorLinks($creatorId)
    {
        $sql = "SELECT * FROM creator_links WHERE creator_id = :creator_id AND is_active = 1 ORDER BY position ASC";
        $stmt = $this->db->execute($sql, [':creator_id' => $creatorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
