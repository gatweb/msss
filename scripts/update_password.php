<?php

require_once __DIR__ . '/../app/config/app.php';
require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

$email = 'contact@msss.com';
$password = '***REMOVED***';

// Générer le hash du mot de passe
$hash = password_hash($password, PASSWORD_DEFAULT);

// Mettre à jour le mot de passe dans la base de données
$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("UPDATE creators SET password = ? WHERE email = ?");
$result = $stmt->execute([$hash, $email]);

if ($result) {
    echo "Mot de passe mis à jour avec succès !\n";
    echo "Email: $email\n";
    echo "Hash: $hash\n";
} else {
    echo "Erreur lors de la mise à jour du mot de passe.\n";
}
