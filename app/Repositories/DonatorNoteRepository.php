<?php
namespace App\Repositories;

use App\Core\Database;

class DonatorNoteRepository
{
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function getNote($creatorId, $donorEmail)
    {
        $sql = "SELECT notes_json FROM donator_notes WHERE creator_id = :creator_id AND donor_email = :donor_email";
        $stmt = $this->db->execute($sql, [
            ':creator_id' => $creatorId,
            ':donor_email' => $donorEmail
        ]);
        $row = $stmt->fetch();
        return $row ? json_decode($row['notes_json'], true) : null;
    }

    public function saveNote($creatorId, $donorEmail, $notes)
    {
        $json = json_encode($notes);
        $sql = "INSERT INTO donator_notes (creator_id, donor_email, notes_json, updated_at) VALUES (:creator_id, :donor_email, :notes_json, CURRENT_TIMESTAMP)
                ON CONFLICT(creator_id, donor_email) DO UPDATE SET notes_json = excluded.notes_json, updated_at = CURRENT_TIMESTAMP";
        $this->db->execute($sql, [
            ':creator_id' => $creatorId,
            ':donor_email' => $donorEmail,
            ':notes_json' => $json
        ]);
    }
}
