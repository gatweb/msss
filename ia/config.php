<?php
// config.php

// --- Configuration de l'API IA ---

// Choisissez votre fournisseur et décommentez la configuration correspondante
// Assurez-vous que l'URL de l'endpoint est correcte pour le modèle choisi

// --- Option 1: OpenAI ---
// define('AI_API_PROVIDER', 'openai');
// define('AI_API_KEY', 'votre_cle_api_openai_ici'); // Remplacez par votre vraie clé
// define('AI_API_ENDPOINT', 'https://api.openai.com/v1/chat/completions');
// define('AI_MODEL', 'gpt-3.5-turbo'); // ou 'gpt-4', 'gpt-4-turbo-preview', etc.

// --- Option 2: Mistral AI ---
define('AI_API_PROVIDER', 'mistral');
define('AI_API_KEY', 'wUJZGG4WfMddqBNlEbAlOjyWOPjXn1SA'); // Remplacez par votre vraie clé
define('AI_API_ENDPOINT', 'https://api.mistral.ai/v1/chat/completions');
define('AI_MODEL', 'mistral-small-latest'); // ou 'mistral-medium-latest', 'mistral-large-latest'

// --- Option 3: Groq ---
// Note: Groq utilise souvent des clés compatibles OpenAI et le même format d'API.
// Vérifiez leur documentation pour l'endpoint exact.
// define('AI_API_PROVIDER', 'groq');
// define('AI_API_KEY', 'votre_cle_api_groq_ici'); // Remplacez par votre vraie clé
// define('AI_API_ENDPOINT', 'https://api.groq.com/openai/v1/chat/completions'); // Endpoint Exemple, à vérifier
// define('AI_MODEL', 'mixtral-8x7b-32768'); // ou 'llama2-70b-4096', etc.


// --- Paramètres Généraux ---
define('API_TIMEOUT_SECONDS', 10); // Timeout pour l'appel cURL

// --- Logging ---
define('LOG_FILE', __DIR__ . '/../storage/logs/api_errors.log'); // Chemin vers le fichier log spécifique à l'API IA

// Fonction helper pour le logging (Optionnel mais recommandé)
function log_api_error($message) {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[{$timestamp}] - {$message}\n";
    // Ensure the directory exists
    $logDir = dirname(LOG_FILE);
    if (!is_dir($logDir)) {
        mkdir($logDir, 0775, true); // Create directory if it doesn't exist
    }
    // Append to the log file
    error_log($logMessage, 3, LOG_FILE);
}

// --- Validation ---
// Assurez-vous qu'une configuration a été choisie et que la clé est définie
if (!defined('AI_API_KEY') || AI_API_KEY === 'wUJZGG4WfMddqBNlEbAlOjyWOPjXn1SA' || AI_API_KEY === '') {
    log_api_error("ERREUR CRITIQUE: Clé API (AI_API_KEY) non configurée dans config.php");
    // Vous pourriez vouloir arrêter l'exécution ici dans un vrai scénario
    // die("Erreur de configuration serveur.");
}
?>
