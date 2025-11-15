<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\View;
use App\Core\Csrf; // Assurer l'importation correcte
use App\Core\Log;

class AiToolsController extends BaseController
{
    private bool $aiConfigLoaded = false;

    /**
     * Constructeur pour s'assurer que les dépendances parentes sont injectées.
     */
    public function __construct()
    {
        parent::__construct(); // Appel essentiel au constructeur parent
    }

    /**
     * Affiche la page principale des outils IA.
     */
    public function index()
    {
        // Charger le créateur explicitement avant la vérification
        $this->creator = null; // Initialiser à null
        if ($this->auth->isLoggedIn() && isset($_SESSION['creator_id'])) {
            $creatorId = $_SESSION['creator_id'];
            // Le creatorRepository devrait être initialisé par BaseController
            if (property_exists($this, 'creatorRepository') && $this->creatorRepository) {
                $this->creator = $this->creatorRepository->findById($creatorId);
                error_log("AiToolsController::index - Chargement creator ID {$creatorId}. Trouvé ? " . ($this->creator ? 'Oui' : 'Non'));
            } else {
                error_log("AiToolsController::index - ERREUR: creatorRepository non disponible ou non initialisé.");
            }
        }

        // Vérifier si l'utilisateur est connecté ET si c'est un créateur valide (creator chargé)
        error_log("DEBUG AiToolsController::index - Vérification accès. \$this->creator est : " . (isset($this->creator) ? print_r($this->creator, true) : 'NON DÉFINI')); // Ajout log
        if (!$this->auth->isLoggedIn() || !$this->creator) { 
            $this->flash->error('Vous devez être connecté en tant que créateur pour accéder à cette page.');
            $this->redirect('/login');
            return;
        }

        // Utiliser la méthode render héritée de BaseController
        // Le layout 'creator_dashboard' déclenchera l'injection auto de $creator et $dailyTip
        $this->render('creator/ai_tools', [ 
            'pageTitle' => 'Outils IA',
            'csrfToken' => Csrf::generateToken() // Utiliser la méthode statique cohérente
        ], 'creator_dashboard');
    }

    /**
     * Gère la requête API pour améliorer le texte.
     */
    public function enhanceText()
    {
        ob_start(); // Démarrer la bufferisation
        header('Content-Type: application/json');

        // 1. Vérification CSRF (ajoutée)
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_POST['csrf_token'] ?? '';
        if (!Csrf::verifyToken($token)) {
            ob_clean(); // Nettoyer le buffer
            http_response_code(403);
            echo json_encode(['error' => 'Jeton CSRF invalide ou manquant.']);
            error_log('Tentative enhanceText échouée: Jeton CSRF invalide.');
            return;
        }

        // Vérifier la méthode et l'authentification
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            ob_clean(); // Nettoyer le buffer
            http_response_code(405); // Method Not Allowed
            echo json_encode(['error' => 'Méthode non autorisée. Utilisez POST.']);
            return;
        }

        // Vérification d'authentification (gardée)
        $this->creator = null; // Initialiser
        if ($this->auth->isLoggedIn() && isset($_SESSION['creator_id'])) {
            $creatorId = $_SESSION['creator_id'];
            if (property_exists($this, 'creatorRepository') && $this->creatorRepository) {
                $this->creator = $this->creatorRepository->findById($creatorId);
            } else {
                error_log("enhanceText - ERREUR: creatorRepository non disponible.");
            }
        }
        if (!$this->creator) { 
            ob_clean(); // Nettoyer le buffer
            http_response_code(403); // Forbidden
            echo json_encode(['error' => 'Accès non autorisé.']);
            return;
        }

        // Récupérer le texte depuis $_POST
        $textToImprove = $_POST['originalText'] ?? null;
        $selectedTone = $_POST['tone'] ?? 'normal'; // Défaut à 'normal'

        if (empty($textToImprove)) {
            ob_clean(); // Nettoyer le buffer
            http_response_code(400);
            echo json_encode(['error' => 'Texte à améliorer manquant.']);
            return;
        }
        
        // Liste des tons valides
        $validTones = ['normal', 'poli', 'informel', 'drole', 'commercial', 'strict'];
        if (!in_array($selectedTone, $validTones)) {
            $selectedTone = 'normal'; // Sécurité: si ton invalide, utiliser 'normal'
        }

        // Construire le prompt en fonction du ton
        $creatorName = $this->creator->name ?? 'le créateur'; // Utiliser le nom chargé
        $toneInstruction = match ($selectedTone) {
            'poli' => 'en adoptant un ton poli et courtois', // Traduction du slug
            'informel' => 'en adoptant un ton informel', // Traduction du slug
            'drole' => 'en adoptant un ton drôle',       // Traduction du slug
            'commercial' => 'en adoptant un ton commercial', // Traduction du slug
            'strict' => 'en adoptant un ton strict et sévère, en utilisant le tutoiement (tu)', // Préciser l'usage de 'tu'
            default => 'neutre et professionnel', // Ton par défaut
        };

        $prompt = "En tant qu'assistant de rédaction pour {$creatorName}, un créateur de contenu, améliore le texte suivant pour le rendre plus clair, engageant et {$toneInstruction}. Corrige les fautes d'orthographe et de grammaire. Le texte doit rester fidèle à l'intention originale.\n\nTexte original:\n---\n" . addslashes($textToImprove) . "\n---\n\nTexte amélioré:";

        // Utiliser la méthode centralisée callMistralApi
        try {
            $this->ensureAiConfig();
            $enhancedResult = $this->callMistralApi($prompt, 0.7, 400); // Température et max tokens ajustables

            // Check if callMistralApi returned an error array or the string content
            if (is_array($enhancedResult) && isset($enhancedResult['error'])) {
                ob_clean(); // Nettoyer le buffer
                http_response_code(500);
                echo json_encode(['error' => $enhancedResult['error']]);
                exit; // Stop execution after sending error
            }

            // If it's not an error array, it must be the string content
            if (is_string($enhancedResult)) {
                ob_clean(); // Nettoyer le buffer avant la réponse succès
                header('Content-Type: application/json'); // Assurer l'en-tête
                $jsonResponse = json_encode(['enhanced_text' => $enhancedResult]);

                if ($jsonResponse === false) {
                    $jsonError = json_last_error_msg();
                    error_log('enhanceText - ERREUR json_encode: ' . $jsonError);
                    http_response_code(500);
                    ob_clean(); // Nettoyer à nouveau
                    echo json_encode(['error' => 'Erreur interne lors du formatage de la réponse JSON: ' . $jsonError]);
                } else {
                    error_log('enhanceText - Réponse JSON envoyée au client: ' . $jsonResponse);
                    echo $jsonResponse;
                }
                exit; // Stop execution immediately after sending the response
            } else {
                // Should not happen if callMistralApi returns string or error array
                ob_clean(); // Nettoyer le buffer
                http_response_code(500);
                $errorMsg = 'Erreur interne inattendue lors de la préparation de la réponse.';
                error_log('enhanceText - Type de retour inattendu depuis callMistralApi: ' . gettype($enhancedResult));
                echo json_encode(['error' => $errorMsg]);
                exit; // Stop execution
            }
        } catch (\Exception $e) {
            ob_clean(); // Nettoyer le buffer en cas d'exception
            http_response_code(500);
            error_log("Erreur lors de l'appel à callMistralApi dans enhanceText: " . $e->getMessage());
            echo json_encode(['error' => 'Erreur interne lors de l\'amélioration du texte. Détails: ' . $e->getMessage()]);
            exit; // Assurer l'arrêt
        }
    }

    /**
     * Gère la requête API pour suggérer une réponse à un message de donateur.
     * @return void
     */
    public function suggestReply()
    {
        ob_start(); // Démarrer la bufferisation de sortie
        header('Content-Type: application/json');
 
        // 1. Vérification CSRF
        // Chercher le token d'abord dans le header, puis dans POST
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_POST['csrf_token'] ?? '';
        if (!Csrf::verifyToken($token)) {
            ob_clean(); // Nettoyer le buffer avant d'envoyer l'erreur
            http_response_code(403);
            echo json_encode(['error' => 'Jeton CSRF invalide ou manquant.']);
            error_log('Tentative suggestReply échouée: Jeton CSRF invalide.');
            return;
        }

        // 2. Récupération des données (attend 'donorMessage' et 'tone')
        $donorMessage = $_POST['donorMessage'] ?? null;
        $selectedTone = $_POST['tone'] ?? 'normal';

        if (!$donorMessage) {
            ob_clean(); // Nettoyer le buffer
            http_response_code(400);
            echo json_encode(['error' => 'Message du donateur manquant.']);
            return;
        }

        // 3. Validation du ton (utiliser une liste adaptée pour les réponses)
        $validTones = ['normal', 'poli', 'informel', 'drole', 'commercial', 'strict']; // Même liste pour l'instant
        if (!in_array($selectedTone, $validTones)) {
            $selectedTone = 'normal';
        }

        // 4. Récupérer le nom du créateur via la session
        $creatorName = $_SESSION['username'] ?? 'le créateur';

        // 5. Construire le prompt pour la suggestion de réponse
        $toneInstruction = match ($selectedTone) {
            'poli' => 'polie et courtoise',
            'informel' => 'informelle et décontractée',
            'drole' => 'drôle et légère',
            'commercial' => 'professionnelle et orientée service',
            'strict' => 'directe et ferme',
            default => 'polie et chaleureuse', // 'normal' = poli et chaleureux
        };

        $prompt = "En tant que créateur de contenu, rédige une réponse {$toneInstruction} au message suivant reçu d'un donateur. Remercie-le sincèrement pour son soutien et sa générosité. Assure-toi que la réponse soit appropriée et respectueuse. Signe la réponse avec '{$creatorName}'.\n\nMessage du donateur:\n---\n" . addslashes($donorMessage) . "\n---\n\nRéponse suggérée:";

        // 6. Appel API via la méthode privée
        try {
            // Log plus détaillé avant l'appel
            error_log("SuggestReply - Appel API pour créateur {$creatorName}, Tone: {$selectedTone}, Msg Length: " . strlen($donorMessage));

            $this->ensureAiConfig();
            $suggestedReply = $this->callMistralApi($prompt, 0.75, 300); // Température légèrement augmentée

            // Check if callMistralApi returned an error array or the string content
            if (is_array($suggestedReply) && isset($suggestedReply['error'])) {
                ob_clean(); // Nettoyer le buffer
                http_response_code(500);
                echo json_encode(['error' => $suggestedReply['error']]);
                exit; // Stop execution after sending error
            }

            // If it's not an error array, it must be the string content
            if (is_string($suggestedReply)) {
                ob_clean(); // Nettoyer le buffer avant la réponse succès
                header('Content-Type: application/json');
                $jsonResponse = json_encode(['suggested_reply' => $suggestedReply]);

                // Vérifier si json_encode a échoué
                if ($jsonResponse === false) {
                    $jsonError = json_last_error_msg();
                    error_log('SuggestReply - ERREUR json_encode: ' . $jsonError);
                    http_response_code(500);
                    // Nettoyer à nouveau au cas où l'erreur précédente a laissé quelque chose
                    ob_clean(); 
                    echo json_encode(['error' => 'Erreur interne lors du formatage de la réponse JSON: ' . $jsonError]);
                } else {
                    // Log the exact JSON response being sent to the client
                    error_log('SuggestReply - Réponse JSON envoyée au client: ' . $jsonResponse);
                    echo $jsonResponse;
                }
                exit; // Stop execution immediately after sending the response
            } else {
                // Should not happen if callMistralApi returns string or error array
                ob_clean(); // Nettoyer le buffer
                http_response_code(500);
                $errorMsg = 'Erreur interne inattendue lors de la préparation de la réponse.';
                error_log('SuggestReply - Type de retour inattendu depuis callMistralApi: ' . gettype($suggestedReply));
                echo json_encode(['error' => $errorMsg]);
                exit; // Stop execution
            }
        } catch (\Exception $e) {
            ob_clean(); // Nettoyer le buffer en cas d'exception
            http_response_code(500);
            error_log("Erreur lors de l'appel à callMistralApi dans suggestReply: " . $e->getMessage());
            echo json_encode(['error' => 'Erreur interne lors de la génération de la suggestion. Détails: ' . $e->getMessage()]);
            exit; // Assurer l'arrêt
        }
    }

    /**
     * Génère ou récupère depuis le cache un conseil motivationnel quotidien pour les créateurs.
     *
     * @return string|null Le conseil du jour ou null en cas d'erreur.
     */
    public function getDailyMotivationalTip(): ?string
    {
        $cacheFilePath = BASE_PATH . '/storage/cache/daily_tip.json';
        $today = date('Y-m-d');
        $cachedData = null;

        // Créer le répertoire cache s'il n'existe pas
        if (!is_dir(dirname($cacheFilePath))) {
            mkdir(dirname($cacheFilePath), 0755, true);
        }

        // Essayer de lire le cache
        if (file_exists($cacheFilePath)) {
            $content = file_get_contents($cacheFilePath);
            if ($content) {
                $cachedData = json_decode($content, true);
            }
        }

        // Vérifier si le cache est valide pour aujourd'hui
        if ($cachedData && isset($cachedData['date']) && $cachedData['date'] === $today && isset($cachedData['tip'])) {
            error_log('Daily Tip: Using cached tip.');
            return $cachedData['tip'];
        }

        // Si pas de cache valide, appeler l'API
        error_log('Daily Tip: Cache invalid or missing, calling Mistral API.');
        $prompt = "Génère une phrase courte (max 20 mots), percutante et de style commercial, pour encourager un donateur à soutenir un créateur aujourd'hui. La phrase doit être positive et inspirante.";
        $tip = $this->callMistralApi($prompt); // Utilise la méthode existante

        if (is_string($tip) && !empty($tip)) {
            // Nettoyer un peu la réponse (au cas où l'API ajoute des guillemets)
            $tip = trim($tip, ' "');
            // Mettre en cache le nouveau tip
            $newCacheData = json_encode(['date' => $today, 'tip' => $tip]);
            file_put_contents($cacheFilePath, $newCacheData);
            error_log('Daily Tip: Fetched and cached new tip: ' . $tip);
            return $tip;
        } else {
            error_log('Daily Tip: Failed to fetch tip from Mistral API.');
            // Retourner l'ancien tip si disponible, sinon null
            return $cachedData['tip'] ?? 'Conseil du jour indisponible. Revenez demain !'; // Fournir un message par défaut
        }
    }

    /**
     * Méthode privée pour appeler l'API Mistral.
     * Factorise la logique cURL.
     *
     * @param string $prompt Le prompt à envoyer.
     * @param float $temperature Température pour la génération.
     * @param int $maxTokens Nombre maximum de tokens pour la réponse.
     * @return string Le contenu de la réponse de l'IA.
     * @throws \Exception En cas d'erreur API ou cURL.
     */
    private function callMistralApi(string $prompt, float $temperature = 0.7, int $maxTokens = 250): string
    {
        $this->ensureAiConfig();

        // Vérification de la configuration API
        if (!defined('AI_API_KEY') || empty(AI_API_KEY) ||
            !defined('AI_API_ENDPOINT') || empty(AI_API_ENDPOINT) ||
            !defined('AI_MODEL') || empty(AI_MODEL))
        {
            error_log("Erreur Configuration API incomplète lors de l'appel à callMistralApi.");
            throw new \Exception('Configuration API incomplète ou invalide.');
        }

        $apiKey = AI_API_KEY;
        $apiEndpoint = AI_API_ENDPOINT;
        $model = AI_MODEL;

        $data = [
            'model' => $model,
            'messages' => [['role' => 'user', 'content' => $prompt]],
            'temperature' => $temperature,
            'max_tokens' => $maxTokens
        ];

        $ch = curl_init($apiEndpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . $apiKey
        ]);
        // Augmenter le timeout général
        curl_setopt($ch, CURLOPT_TIMEOUT, 90);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20); // Timeout de connexion

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        // Log de la réponse brute et du code HTTP
        error_log("Réponse brute de l'API Mistral (HTTP {$httpCode}): " . $response);
        error_log("Erreur cURL (si existe): " . $curlError);

        if ($curlError) {
            error_log("Erreur cURL lors de l'appel API Mistral: " . $curlError);
            throw new \Exception("Erreur cURL: " . $curlError);
        }

        $responseData = json_decode($response, true);

        // Vérification si le décodage JSON a fonctionné et si la structure attendue est présente
        if (json_last_error() !== JSON_ERROR_NONE || !isset($responseData['choices'][0]['message']['content'])) {
            error_log("Erreur de décodage JSON ou structure de réponse invalide. Erreur JSON: " . json_last_error_msg());
            error_log("Réponse reçue qui a causé l'erreur: " . $response);
            throw new \Exception("Impossible d'extraire le contenu de la réponse de l'API. Détail: " . json_last_error_msg());
        }

        $apiContent = $responseData['choices'][0]['message']['content'] ?? null;

        if ($apiContent === null) {
             // Essayer de récupérer un message d'erreur plus précis si possible
             $errorDetail = $responseData['error']['message'] ?? json_encode($responseData);
             error_log("Réponse API invalide ou contenu manquant. Réponse brute: " . $response);
            throw new \Exception("Impossible d'extraire le contenu de la réponse de l'API. Détail: " . $errorDetail);
        }

        return $apiContent;
    }

    private function ensureAiConfig(): void
    {
        if ($this->aiConfigLoaded) {
            return;
        }

        if (!defined('BASE_PATH')) {
            throw new \RuntimeException('BASE_PATH non défini, impossible de charger la configuration IA.');
        }

        $configPath = BASE_PATH . '/ia/config.php';
        if (!file_exists($configPath)) {
            throw new \RuntimeException('Fichier de configuration IA introuvable.');
        }

        require_once $configPath;
        $this->aiConfigLoaded = true;
    }
}
