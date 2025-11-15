<?php

use App\Core\Database;
use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

$basePath = dirname(__DIR__);

if (file_exists($basePath . '/.env')) {
    Dotenv::createImmutable($basePath)->safeLoad();
}

require_once __DIR__ . '/../app/config/app.php';

$database = Database::getInstance();
$pdo = $database->getConnection();

function runMigration($pdo, $file) {
    try {
        $sql = file_get_contents($file);
        $pdo->exec($sql);
        echo "Migration réussie : " . basename($file) . "\n";
        return true;
    } catch (PDOException $e) {
        echo "Erreur lors de la migration " . basename($file) . " : " . $e->getMessage() . "\n";
        return false;
    }
}

// Suppression des tables existantes
$dropTables = [
    'DROP TABLE IF EXISTS creator_stats;',
    'DROP TABLE IF EXISTS donations;',
    'DROP TABLE IF EXISTS creator_links;',
    'DROP TABLE IF EXISTS packs;',
    'DROP TABLE IF EXISTS creators;'
];

foreach ($dropTables as $sql) {
    try {
        $pdo->exec($sql);
        echo "Table supprimée avec succès\n";
    } catch (PDOException $e) {
        echo "Erreur lors de la suppression : " . $e->getMessage() . "\n";
    }
}

// Exécuter toutes les migrations du dossier migrations/
echo "\n[INFO] Application des migrations...\n";
$migrationFiles = glob(__DIR__ . '/migrations/*.sql');
natsort($migrationFiles); // tri naturel (001, 002...)
foreach ($migrationFiles as $migration) {
    runMigration($pdo, $migration);
}

// Exécuter tous les seeds du dossier seeds/
echo "\n[INFO] Application des seeds...\n";
$seedFiles = glob(__DIR__ . '/seeds/*.sql');
natsort($seedFiles);
foreach ($seedFiles as $seed) {
    runMigration($pdo, $seed);
}

echo "\n[INFO] Migrations terminées !\n";
