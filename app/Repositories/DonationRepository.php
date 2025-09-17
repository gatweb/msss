<?php
namespace App\Repositories;

use App\Core\Database;

class DonationRepository
{
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function getDonationsByCreator($creatorId, $limit = null)
    {
        $sql = "SELECT * FROM donations WHERE creator_id = :creator_id ORDER BY created_at DESC";
        if ($limit !== null) {
            $sql .= " LIMIT :limit";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':creator_id', $creatorId, \PDO::PARAM_INT);
            $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
            $stmt->execute();
        } else {
            $stmt = $this->db->execute($sql, [':creator_id' => $creatorId]);
        }
        return $stmt->fetchAll();
    }

    public function getTotalAmount($creatorId)
    {
        $sql = "SELECT SUM(amount) as total FROM donations WHERE creator_id = :creator_id";
        $stmt = $this->db->execute($sql, [':creator_id' => $creatorId]);
        $row = $stmt->fetch();
        return $row ? $row['total'] : 0;
    }

    public function getUniqueDonorsCount($creatorId)
    {
        $sql = "SELECT COUNT(DISTINCT donor_email) as count FROM donations WHERE creator_id = :creator_id";
        $stmt = $this->db->execute($sql, [':creator_id' => $creatorId]);
        $row = $stmt->fetch();
        return $row ? $row['count'] : 0;
    }

    public function getAllDonations()
{
    $sql = "SELECT * FROM donations ORDER BY created_at DESC";
    $stmt = $this->db->execute($sql);
    return $stmt->fetchAll();
}
public function getAverageDonation($creatorId)
{
    $sql = "SELECT AVG(amount) as avg FROM donations WHERE creator_id = :creator_id AND status = 'completed'";
    $stmt = $this->db->execute($sql, [':creator_id' => $creatorId]);
    $row = $stmt->fetch();
    return $row ? (float)$row['avg'] : 0;
}
    /**
     * Retourne la liste des donateurs uniques d'une créatrice
     * @param int $creatorId
     * @return array
     */
    public function getDonatorsByCreator($creatorId)
    {
        $sql = "SELECT d.donor_name, d.donor_email, COUNT(*) as donation_count,
                       SUM(d.amount) as total_amount, MAX(d.created_at) as last_donation,
                       t.timer_end, t.donation_type
                FROM donations d
                LEFT JOIN (
                    SELECT donor_email, creator_id, MAX(timer_end) as timer_end, donation_type
                    FROM donations
                    WHERE timer_end IS NOT NULL
                    GROUP BY donor_email, creator_id
                ) t ON t.donor_email = d.donor_email AND t.creator_id = d.creator_id
                WHERE d.creator_id = :creator_id
                GROUP BY d.donor_name, d.donor_email
                ORDER BY total_amount DESC";
        $stmt = $this->db->execute($sql, [':creator_id' => $creatorId]);
        return $stmt->fetchAll();
    }

    /**
     * Ajoute un don dans la base de données
     * @param string $donorName
     * @param float $amount
     * @param string $donationType
     * @param string|null $comment
     * @return bool
     */
    public function addDonation($donorName, $donorEmail, $amount, $donationType, $comment = null)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $creatorId = isset($_SESSION['creator_id']) ? intval($_SESSION['creator_id']) : null;
        if (!$creatorId) return false;
        // Gestion du timer pour fan fidèle
        $timerEnd = null;
        if (strtolower($donationType) === 'fan_fidele' || strtolower($donationType) === 'fan fidèle') {
            $timerEnd = date('Y-m-d H:i:s', strtotime('+1 month'));
        }
        $sql = "INSERT INTO donations (creator_id, donor_name, donor_email, amount, donation_type, comment, created_at, timer_end) VALUES (:creator_id, :donor_name, :donor_email, :amount, :donation_type, :comment, CURRENT_TIMESTAMP, :timer_end)";
        $params = [
            ':creator_id' => $creatorId,
            ':donor_name' => $donorName,
            ':donor_email' => $donorEmail,
            ':amount' => $amount,
            ':donation_type' => $donationType,
            ':comment' => $comment,
            ':timer_end' => $timerEnd
        ];
        try {
            $this->db->execute($sql, $params);
            return true;
        } catch (\Exception $e) {
            error_log('Erreur ajout don: ' . $e->getMessage());
            return false;
        }
    }

// Ajoute d'autres méthodes métier ici (type_stats, pagination, etc.)
}
