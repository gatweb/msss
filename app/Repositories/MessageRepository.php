<?php

namespace App\Repositories;

class MessageRepository
{
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Retourne tous les messages associés à un créateur (par son ID).
     */
    public function getByCreator($creatorId)
    {
        $sql = "SELECT m.*, s.name as sender_name, s.profile_pic_url as sender_avatar
                FROM messages m
                JOIN creators s ON m.sender_id = s.id
                WHERE m.receiver_id = :creator_id 
                ORDER BY m.created_at DESC";
        $params = [':creator_id' => $creatorId];
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Récupère la conversation entre deux utilisateurs.
     */
    public function getConversation(int $userId1, int $userId2)
    {
        $sql = "SELECT * FROM messages 
                WHERE (sender_id = :userId1 AND receiver_id = :userId2) 
                   OR (sender_id = :userId2 AND receiver_id = :userId1) 
                ORDER BY created_at ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':userId1' => $userId1, ':userId2' => $userId2]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Crée un nouveau message.
     */
    public function create(array $data)
    {
        $sql = "INSERT INTO messages (sender_id, receiver_id, content) 
                VALUES (:sender_id, :receiver_id, :content)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':sender_id' => $data['sender_id'],
            ':receiver_id' => $data['receiver_id'],
            ':content' => $data['content']
        ]);
    }

    public function find(int $id)
    {
        $sql = "SELECT m.*, s.name as sender_name, s.profile_pic_url as sender_avatar
                FROM messages m
                LEFT JOIN creators s ON m.sender_id = s.id
                WHERE m.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function markAsRead(int $id)
    {
        $stmt = $this->db->prepare("UPDATE messages SET is_read = 1 WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function archive(int $id)
    {
        // Pour l'instant, on supprime le message. Idéalement, on ajouterait une colonne 'archived_at'.
        $stmt = $this->db->prepare("DELETE FROM messages WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
