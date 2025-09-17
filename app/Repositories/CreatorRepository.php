<?php
namespace App\Repositories;

use App\Core\Database;

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

    // Ajoute d'autres méthodes métier ici (findAll, findByEmail, etc.)
}
