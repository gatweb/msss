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
}
