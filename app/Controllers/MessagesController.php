<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Repositories\MessageRepository;
use App\Models\Message;

class MessagesController extends BaseController
{
    protected $messageRepo;

    public function __construct()
    {
        parent::__construct();
        // Instancie le repository avec la connexion DB du singleton
        $this->messageRepo = new MessageRepository(\App\Core\Database::getInstance());
    }

    public function index()
    {
        $this->requireCreator();
        $creatorId = $this->creator['id'];
        $messagesData = $this->messageRepo->getByCreator($creatorId);
        // Transforme les tableaux en objets Message
        $messages = array_map(fn($data) => new Message($data), $messagesData);

        // $creator est automatiquement injecté par BaseController::render pour le layout 'creator_dashboard'
        $this->render('creator/messages', ['messages' => $messages], 'creator_dashboard');
    }

    public function showConversation(int $otherUserId)
    {
        $this->requireCreator();
        $creatorId = $this->creator['id'];

        // Récupérer les messages de la conversation
        $messagesData = $this->messageRepo->getConversation($creatorId, $otherUserId);
        $messages = array_map(fn($data) => new Message($data), $messagesData);

        // Récupérer les informations de l'autre utilisateur (simplifié)
        // Dans une vraie app, on utiliserait un UserRepository
        $userRepo = new \App\Repositories\CreatorRepository(\App\Core\Database::getInstance());
        $otherUser = $userRepo->find($otherUserId);

        $this->render('creator/conversation', [
            'messages' => $messages,
            'otherUser' => $otherUser
        ], 'creator_dashboard');
    }

    public function reply(int $otherUserId)
    {
        $this->requireCreator();
        $creatorId = $this->creator['id'];

        if ($this->isPost()) {
            $content = $_POST['content'] ?? '';

            if (!empty($content)) {
                $this->messageRepo->create([
                    'sender_id' => $creatorId,
                    'receiver_id' => $otherUserId,
                    'content' => $content
                ]);
            }
        }

        // Rediriger vers la conversation
        $this->redirect('/profile/messages/conversation/' . $otherUserId);
    }

    // --- API Methods ---

    public function getMessage(int $messageId)
    {
        $this->requireCreator();
        // Dans une vraie app, on vérifierait que le message appartient bien au créateur
        $message = $this->messageRepo->find($messageId); // find() n'existe pas encore
        $this->jsonResponse($message);
    }

    public function markAsRead(int $messageId)
    {
        $this->requireCreator();
        $this->messageRepo->markAsRead($messageId); // markAsRead() n'existe pas encore
        $this->jsonResponse(['success' => true]);
    }

    public function archiveMessage(int $messageId)
    {
        $this->requireCreator();
        $this->messageRepo->archive($messageId); // archive() n'existe pas encore
        $this->jsonResponse(['success' => true]);
    }
}
