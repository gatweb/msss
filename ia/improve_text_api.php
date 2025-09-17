<?php
// improve_text_api.php

// Définir le type de contenu de la réponse comme JSON
header('Content-Type: application/json');

// Inclure le fichier de configuration sécurisé
// Ajustez le chemin si nécessaire en fonction de l'emplacement de votre config.php
require_once __DIR__ . 'config/config.php'; // Exemple: si config.php est 2 niveaux au-dessus

// Vérifier si la méthode de requête est POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée. Utilisez POST.']);
    exit;
}

// Récupérer le corps de la requête JSON envoyé par le frontend
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE); // Convertit en tableau associatif PHP

// Validation basique de l'entrée
if (!$input || !isset($input['text']) || trim($input['text']) === '') {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'error' => 'Texte manquant ou invalide dans la requête.']);
    exit;
}

// Nettoyage très basique (plus de nettoyage peut être nécessaire selon le contexte)
$originalText = trim($input['text']);
// Vous pourriez ajouter htmlspecialchars ici si le texte peut contenir du HTML,
// mais attention à ne pas encoder si l'IA doit comprendre des balises spécifiques.
// Pour l'envoi à l'IA, le texte brut est généralement préférable.
// $originalText = htmlspecialchars(trim($input['text']), ENT_QUOTES, 'UTF-8');

// --- Préparation de l'appel API ---

// Construction du prompt pour l'IA
$prompt = "Améliore ce texte pour le rendre plus professionnel, clair et engageant pour un client, sans changer le fond : \n\n\"" . $originalText . "\"";

// Préparation du payload pour l'API (Format compatible OpenAI Chat Completions)
$payload = [
    'model' => AI_MODEL,
    'messages' => [
        ['role' => 'system', 'content' => 'Tu es un assistant expert en communication client.'],
        ['role' => 'user', 'content' => $prompt]
    ],
    'max_tokens' => 1000, // Ajustez si nécessaire
    'temperature' => 0.7, // Contrôle la créativité (0=déterministe, 1=très créatif)
];

// Initialisation de cURL
$ch = curl_init();

// Configuration des options cURL
curl_setopt($ch, CURLOPT_URL, AI_API_ENDPOINT);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Retourne la réponse au lieu de l'afficher
curl_setopt($ch, CURLOPT_POST, true);           // Méthode POST
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload)); // Données POST en JSON
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . AI_API_KEY
]);
curl_setopt($ch, CURLOPT_TIMEOUT, API_TIMEOUT_SECONDS); // Timeout en secondes
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Important pour la sécurité HTTPS

// Exécution de la requête cURL
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Récupère le code de statut HTTP
$curlError = curl_error($ch);                    // Récupère les erreurs cURL

// Fermeture de la session cURL
curl_close($ch);

// --- Gestion de la réponse API ---

$responseData = [];

if ($curlError) {
    // Erreur cURL (timeout, connexion impossible, etc.)
    http_response_code(500); // Internal Server Error
    $errorMessage = "Erreur cURL lors de l'appel API: " . $curlError;
    log_api_error($errorMessage); // Log l'erreur détaillée
    $responseData = ['success' => false, 'error' => "Impossible de contacter le service d'amélioration de texte pour le moment. (Code: C1)"];
} elseif ($httpCode >= 400) {
    // Erreur HTTP renvoyée par l'API (clé invalide, quota dépassé, mauvaise requête, etc.)
    http_response_code($httpCode); // Renvoyer le même code d'erreur
    $apiErrorBody = json_decode($response, true);
    $apiErrorMessage = isset($apiErrorBody['error']['message']) ? $apiErrorBody['error']['message'] : $response;
    $errorMessage = "Erreur API (HTTP {$httpCode}): " . $apiErrorMessage;
    log_api_error($errorMessage); // Log l'erreur détaillée
    $responseData = ['success' => false, 'error' => "Une erreur s'est produite avec le service d'amélioration. (Code: A{$httpCode})"];
} else {
    // Succès de l'appel API
    $apiResponse = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        // Erreur lors du décodage de la réponse JSON de l'API
        http_response_code(500);
        $errorMessage = "Erreur de décodage JSON de la réponse API: " . json_last_error_msg() . " | Réponse brute: " . substr($response, 0, 500);
        log_api_error($errorMessage);
        $responseData = ['success' => false, 'error' => "Réponse invalide reçue du service d'amélioration. (Code: J1)"];
    } elseif (isset($apiResponse['choices'][0]['message']['content'])) {
        // Extraction du texte amélioré (structure standard OpenAI)
        $improvedText = trim($apiResponse['choices'][0]['message']['content']);
        $responseData = ['success' => true, 'improved_text' => $improvedText];
    } elseif (isset($apiResponse['error'])) {
         // L'API a renvoyé un code 2xx mais avec une structure d'erreur dans le JSON
        http_response_code(500); // Ou un autre code approprié
        $errorMessage = "Erreur renvoyée dans la réponse API (JSON): " . json_encode($apiResponse['error']);
        log_api_error($errorMessage);
        $responseData = ['success' => false, 'error' => "Le service d'amélioration a retourné une erreur interne. (Code: A2)"];
    }
    else {
        // Structure de réponse inattendue
        http_response_code(500);
        $errorMessage = "Structure de réponse API inattendue. Réponse brute: " . substr($response, 0, 500);
        log_api_error($errorMessage);
        $responseData = ['success' => false, 'error' => "Réponse inattendue reçue du service d'amélioration. (Code: S1)"];
    }
}

// Renvoyer la réponse JSON finale au frontend
echo json_encode($responseData);
exit;

?>
