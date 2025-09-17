<?php

namespace App\Models;

use PDO;
use PDOException;
use DateTime;

class Donation extends BaseModel {


    public function getAllDonations() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM donations ORDER BY donation_timestamp DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération des dons : " . $e->getMessage());
            return [];
        }
    }

    public function addDonation($donorName, $amount, $donationType, $comment = null) {
        try {
            $sql = "INSERT INTO donations (donor_name, amount, donation_type, comment) 
                    VALUES (:donor_name, :amount, :donation_type, :comment)";
            $stmt = $this->pdo->prepare($sql);
            
            return $stmt->execute([
                'donor_name' => $donorName,
                'amount' => $amount,
                'donation_type' => $donationType,
                'comment' => $comment
            ]);
        } catch (\PDOException $e) {
            error_log("Erreur lors de l'ajout du don : " . $e->getMessage());
            return false;
        }
    }

    public function getDonationGoalAndTotal() {
        try {
            // Récupérer l'objectif
            $stmt = $this->pdo->query("SELECT donation_goal FROM global_status WHERE id = 1");
            $goal = $stmt->fetchColumn();
            
            // Calculer le total des dons
            $stmt = $this->pdo->query("SELECT COALESCE(SUM(amount), 0) FROM donations");
            $total = $stmt->fetchColumn();
            
            return [
                'goal' => $goal,
                'total' => $total
            ];
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération de l'objectif et du total : " . $e->getMessage());
            return [
                'goal' => 0,
                'total' => 0
            ];
        }
    }

    public function updateTimer($donationId, $action) {
        try {
            if ($action === 'start') {
                $sql = "UPDATE donations SET 
                        timer_start_time = CURRENT_TIMESTAMP,
                        timer_status = 'running'
                        WHERE id = :id";
            } else {
                $sql = "UPDATE donations SET 
                        timer_elapsed_seconds = timer_elapsed_seconds + TIMESTAMPDIFF(SECOND, timer_start_time, CURRENT_TIMESTAMP),
                        timer_status = 'stopped',
                        timer_start_time = NULL
                        WHERE id = :id";
            }
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute(['id' => $donationId]);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la mise à jour du timer : " . $e->getMessage());
            return false;
        }
    }

    public function getDonationsByCreator($creatorId, $period, $page, $perPage) {
        try {
            $offset = ($page - 1) * $perPage;
            $whereClause = $this->getPeriodWhereClause($period);
            
            $sql = "SELECT * FROM donations 
                   WHERE creator_id = :creator_id 
                   $whereClause
                   ORDER BY donation_timestamp DESC 
                   LIMIT :limit OFFSET :offset";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':creator_id', $creatorId);
            $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération des dons par créateur : " . $e->getMessage());
            return [];
        }
    }

    public function getTotalDonationsCount($creatorId, $period) {
        try {
            $whereClause = $this->getPeriodWhereClause($period);
            
            $sql = "SELECT COUNT(*) FROM donations 
                   WHERE creator_id = :creator_id $whereClause";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':creator_id', $creatorId);
            $stmt->execute();
            
            return $stmt->fetchColumn();
        } catch (\PDOException $e) {
            error_log("Erreur lors du comptage des dons : " . $e->getMessage());
            return 0;
        }
    }

    public function getTotalAmount($creatorId, $period) {
        try {
            $whereClause = $this->getPeriodWhereClause($period);
            
            $sql = "SELECT COALESCE(SUM(amount), 0) FROM donations 
                   WHERE creator_id = :creator_id $whereClause";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':creator_id', $creatorId);
            $stmt->execute();
            
            return $stmt->fetchColumn();
        } catch (\PDOException $e) {
            error_log("Erreur lors du calcul du montant total : " . $e->getMessage());
            return 0;
        }
    }

    public function getUniqueDonorsCount($creatorId, $period) {
        try {
            $whereClause = $this->getPeriodWhereClause($period);
            
            $sql = "SELECT COUNT(DISTINCT donor_id) FROM donations 
                   WHERE creator_id = :creator_id $whereClause";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':creator_id', $creatorId);
            $stmt->execute();
            
            return $stmt->fetchColumn();
        } catch (\PDOException $e) {
            error_log("Erreur lors du comptage des donateurs uniques : " . $e->getMessage());
            return 0;
        }
    }

    public function getDonationTypeStats($creatorId, $period) {
        try {
            $whereClause = $this->getPeriodWhereClause($period);
            
            $sql = "SELECT donation_type, COUNT(*) as count 
                   FROM donations 
                   WHERE creator_id = :creator_id $whereClause
                   GROUP BY donation_type";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':creator_id', $creatorId);
            $stmt->execute();
            
            $result = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $result[$row['donation_type']] = (int)$row['count'];
            }
            
            return $result;
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération des statistiques par type : " . $e->getMessage());
            return [];
        }
    }

    public function getDonationsEvolution($creatorId, $period) {
        try {
            $intervals = $this->getIntervalsByPeriod($period);
            $sql = "SELECT 
                       DATE(donation_timestamp) as date,
                       SUM(amount) as total
                   FROM donations 
                   WHERE creator_id = :creator_id 
                   AND donation_timestamp >= :start_date
                   GROUP BY DATE(donation_timestamp)
                   ORDER BY date ASC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':creator_id', $creatorId);
            $stmt->bindValue(':start_date', $intervals['start']);
            $stmt->execute();
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $evolution = [
                'labels' => [],
                'values' => []
            ];
            
            // Remplir les dates manquantes avec des valeurs à 0
            $current = new DateTime($intervals['start']);
            $end = new DateTime($intervals['end']);
            $data = [];
            
            foreach ($results as $row) {
                $data[$row['date']] = floatval($row['total']);
            }
            
            while ($current <= $end) {
                $date = $current->format('Y-m-d');
                $evolution['labels'][] = $current->format('d/m');
                $evolution['values'][] = $data[$date] ?? 0;
                $current->modify('+1 day');
            }
            
            return $evolution;
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération de l'évolution des dons : " . $e->getMessage());
            return ['labels' => [], 'values' => []];
        }
    }

    public function getDonationById($id) {
        try {
            $sql = "SELECT * FROM donations WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération du don : " . $e->getMessage());
            return null;
        }
    }

    public function stopTimer($id) {
        try {
            $sql = "UPDATE donations 
                   SET timer_status = 'completed', 
                       timer_end = NOW() 
                   WHERE id = :id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id);
            
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Erreur lors de l'arrêt du timer : " . $e->getMessage());
            return false;
        }
    }

    public function deleteDonation($id) {
        try {
            $sql = "DELETE FROM donations WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id);
            
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Erreur lors de la suppression du don : " . $e->getMessage());
            return false;
        }
    }

    public function getDonationsForExport($creatorId, $period) {
        try {
            $whereClause = $this->getPeriodWhereClause($period);
            
            $sql = "SELECT * FROM donations 
                   WHERE creator_id = :creator_id $whereClause
                   ORDER BY donation_timestamp DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':creator_id', $creatorId);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur lors de l'export des dons : " . $e->getMessage());
            return [];
        }
    }

    private function getPeriodWhereClause($period) {
        $now = new DateTime();
        
        switch ($period) {
            case 'today':
                return "AND DATE(donation_timestamp) = CURDATE()";
                
            case 'week':
                $startOfWeek = $now->modify('monday this week')->format('Y-m-d');
                return "AND donation_timestamp >= '$startOfWeek'";
                
            case 'month':
                return "AND MONTH(donation_timestamp) = MONTH(CURDATE()) 
                        AND YEAR(donation_timestamp) = YEAR(CURDATE())";
                
            case 'last_month':
                return "AND MONTH(donation_timestamp) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) 
                        AND YEAR(donation_timestamp) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))";
                
            case 'year':
                return "AND YEAR(donation_timestamp) = YEAR(CURDATE())";
                
            default: // 'all'
                return "";
        }
    }

    private function getIntervalsByPeriod($period) {
        $now = new DateTime();
        $intervals = ['end' => $now->format('Y-m-d')];
        
        switch ($period) {
            case 'week':
                $intervals['start'] = $now->modify('-6 days')->format('Y-m-d');
                break;
                
            case 'month':
                $intervals['start'] = $now->modify('-29 days')->format('Y-m-d');
                break;
                
            case 'year':
                $intervals['start'] = $now->modify('-364 days')->format('Y-m-d');
                break;
                
            default:
                $intervals['start'] = $now->modify('-6 days')->format('Y-m-d');
        }
        
        return $intervals;
    }
}
