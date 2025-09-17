<?php

namespace App\Models;

use PDO;
use PDOException;

class Pack extends BaseModel {
    protected $table = 'packs';
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getPacksByCreator($creatorId) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM packs 
            WHERE creator_id = :creator_id 
            ORDER BY price ASC
        ");
        $stmt->execute(['creator_id' => $creatorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function createPack($data) {
        // Log temporaire pour débogage
        error_log('PackModel::createPack - Data received: ' . print_r($data, true));

        $stmt = $this->pdo->prepare("
            INSERT INTO packs (creator_id, name, description, price, is_active)
            VALUES (:creator_id, :name, :description, :price, :is_active)
        ");
        
        return $stmt->execute([
            'creator_id' => $data['creator_id'],
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'] ?? $data['amount'],
            'is_active' => $data['is_active'] ?? true
        ]);
    }
    
    public function updatePack($packId, $data) {
        $fields = [];
        $params = ['id' => $packId];
        
        foreach ($data as $key => $value) {
            if ($key !== 'id') {
                $fields[] = "$key = :$key";
                $params[$key] = $value;
            }
        }
        
        $stmt = $this->pdo->prepare("
            UPDATE packs 
            SET " . implode(', ', $fields) . "
            WHERE id = :id
        ");
        
        return $stmt->execute($params);
    }
    
    public function deletePack($packId, $creatorId) {
        $stmt = $this->pdo->prepare("
            DELETE FROM packs 
            WHERE id = :id AND creator_id = :creator_id
        ");
        
        return $stmt->execute([
            'id' => $packId,
            'creator_id' => $creatorId
        ]);
    }
    
    public function getPackById($packId) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM packs 
            WHERE id = :id
        ");
        $stmt->execute(['id' => $packId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
