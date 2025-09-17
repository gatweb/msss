<?php

namespace App\Core;

use PDO;

class BaseModel {
    protected $pdo;
    protected $table;

    public function __construct($pdo = null) {
        $this->pdo = $pdo ?: Database::getInstance()->getConnection();
    }

    public function getById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = :id");
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération par ID : " . $e->getMessage());
            return null;
        }
    }

    public function getAll() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM {$this->table}");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération de tous les éléments : " . $e->getMessage());
            return [];
        }
    }

    public function create($data) {
        try {
            $fields = array_keys($data);
            $values = array_map(fn($field) => ":{$field}", $fields);
            
            $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") 
                   VALUES (" . implode(', ', $values) . ")";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($data);
            
            return $this->pdo->lastInsertId();
        } catch (\PDOException $e) {
            error_log("Erreur lors de la création : " . $e->getMessage());
            return false;
        }
    }

    public function update($id, $data) {
        try {
            $fields = array_map(fn($field) => "{$field} = :{$field}", array_keys($data));
            
            $sql = "UPDATE {$this->table} 
                   SET " . implode(', ', $fields) . "
                   WHERE id = :id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array_merge($data, ['id' => $id]));
            
            return true;
        } catch (\PDOException $e) {
            error_log("Erreur lors de la mise à jour : " . $e->getMessage());
            return false;
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id");
            return $stmt->execute(['id' => $id]);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la suppression : " . $e->getMessage());
            return false;
        }
    }
}
