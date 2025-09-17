<?php
namespace App\Repositories;

use App\Core\Database;

class PackRepository
{
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function getPacksByCreator($creatorId)
    {
        $sql = "SELECT * FROM packs WHERE creator_id = :creator_id";
        $stmt = $this->db->execute($sql, [':creator_id' => $creatorId]);
        return $stmt->fetchAll();
    }
    public function findById($id)
    {
        $sql = "SELECT * FROM packs WHERE id = :id";
        $stmt = $this->db->execute($sql, [':id' => $id]);
        return $stmt->fetch();
    }
    public function createPack($data)
    {
        $sql = "INSERT INTO packs (creator_id, name, description, price, perks, is_active) VALUES (:creator_id, :name, :description, :price, :perks, :is_active)";
        $params = [
            ':creator_id' => $data['creator_id'],
            ':name' => $data['name'],
            ':description' => $data['description'],
            ':price' => $data['price'],
            ':perks' => isset($data['perks']) ? $data['perks'] : '',
            ':is_active' => isset($data['is_active']) ? $data['is_active'] : 1
        ];
        return $this->db->execute($sql, $params);
    }

    public function updatePack($id, $data)
    {
        $sql = "UPDATE packs SET name = :name, description = :description, price = :price, perks = :perks, is_active = :is_active WHERE id = :id";
        $params = [
            ':id' => $id,
            ':name' => $data['name'],
            ':description' => $data['description'],
            ':price' => $data['price'],
            ':perks' => isset($data['perks']) ? $data['perks'] : '',
            ':is_active' => isset($data['is_active']) ? $data['is_active'] : 1
        ];
        return $this->db->execute($sql, $params);
    }

    public function deletePack($id, $creatorId)
    {
        $sql = "DELETE FROM packs WHERE id = :id AND creator_id = :creator_id";
        $params = [
            ':id' => $id,
            ':creator_id' => $creatorId
        ];
        return $this->db->execute($sql, $params);
    }
    // Ajoute d'autres méthodes métier ici (findById, create, update, etc.)
}
