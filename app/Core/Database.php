<?php

namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        try {
            // Créer le dossier storage s'il n'existe pas
            if (!is_dir(dirname(DB_DATABASE))) {
                mkdir(dirname(DB_DATABASE), 0777, true);
            }

            // Créer la base de données SQLite si elle n'existe pas
            if (!file_exists(DB_DATABASE)) {
                touch(DB_DATABASE);
                chmod(DB_DATABASE, 0777);
            }

            $this->pdo = new PDO(
                DB_CONNECTION . ':' . DB_DATABASE,
                null,
                null,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );

            // Activer les clés étrangères
            $this->pdo->exec('PRAGMA foreign_keys = ON');

        } catch (PDOException $e) {
            error_log('Erreur de connexion à la base de données : ' . $e->getMessage());
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }

    public function query($query) {
        try {
            return $this->pdo->query($query);
        } catch (PDOException $e) {
            error_log('Erreur lors de l\'exécution de la requête : ' . $e->getMessage());
            throw $e;
        }
    }

    public function prepare($query) {
        try {
            return $this->pdo->prepare($query);
        } catch (PDOException $e) {
            error_log('Erreur lors de la préparation de la requête : ' . $e->getMessage());
            throw $e;
        }
    }

    public function execute($query, $params = []) {
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log('Erreur lors de l\'exécution de la requête : ' . $e->getMessage());
            throw $e;
        }
    }

    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }

    public function beginTransaction() {
        try {
            return $this->pdo->beginTransaction();
        } catch (PDOException $e) {
            error_log('Erreur lors du début de la transaction : ' . $e->getMessage());
            throw $e;
        }
    }

    public function commit() {
        try {
            return $this->pdo->commit();
        } catch (PDOException $e) {
            error_log('Erreur lors du commit de la transaction : ' . $e->getMessage());
            throw $e;
        }
    }

    public function rollBack() {
        try {
            return $this->pdo->rollBack();
        } catch (PDOException $e) {
            error_log('Erreur lors du rollback de la transaction : ' . $e->getMessage());
            throw $e;
        }
    }

    public function run($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log('Erreur SQL : ' . $e->getMessage());
            throw $e;
        }
    }

    // Empêcher le clonage de l'instance
    private function __clone() {}

    // Empêcher la désérialisation de l'instance
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
