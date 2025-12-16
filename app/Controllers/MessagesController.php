<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\View;
use App\Core\Auth;
use App\Core\Flash;
use App\Repositories\CreatorRepository;
use App\Repositories\MessageRepository;
use App\Models\Message;
use App\Core\Csrf;

class MessagesController extends BaseController
{
    protected $messageRepo;

    public function __construct(
        View $view,
        Auth $auth,
        Flash $flash,
        CreatorRepository $creatorRepository,
        MessageRepository $messageRepo
    ) {
        parent::__construct($view, $auth, $flash, $creatorRepository);
        $this->messageRepo = $messageRepo;
    }

    public function index()
    {
        $this->requireCreator();
        $creatorId = $this->creator['id'];
        $messagesData = $this->messageRepo->getByCreator($creatorId);
        $messages = array_map(fn($data) => new Message($data), $messagesData);

        $this->view->addScript('/assets/js/messages.js');
        $this->render('creator/messages.html.twig', ['messages' => $messages], 'creator_dashboard');
    }

    public function showConversation(int $otherUserId)
    {
        $this->requireCreator();
        $creatorId = $this->creator['id'];

        $messagesData = $this->messageRepo->getConversation($creatorId, $otherUserId);
        $messages = array_map(fn($data) => new Message($data), $messagesData);

        $otherUser = $this->creatorRepository->findById($otherUserId);

        $this->render('creator/conversation.html.twig', [
            'messages' => $messages,
            'otherUser' => $otherUser,
            'csrf_token' => $this->generateCsrfToken()
        ], 'creator_dashboard');
    }

    public function reply(int $otherUserId)
    {
        $this->requireCreator();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->verifyCsrfToken($_POST['csrf_token'])) {
                $this->flash->error('Session invalide. Veuillez réessayer.');
                $this->redirect('/profile/messages/conversation/' . $otherUserId);
                return;
            }

            $content = $_POST['content'] ?? '';

            if (!empty($content)) {
                $this->messageRepo->create([
                    'sender_id' => $this->creator['id'],
                    'receiver_id' => $otherUserId,
                    'content' => $content
                ]);
            }
        }

        $this->redirect('/profile/messages/conversation/' . $otherUserId);
    }

    // --- API Methods ---

    public function getMessage(int $messageId)
    {
        $this->requireCreator();
        $message = $this->messageRepo->find($messageId);
        $this->jsonResponse($message);
    }

    public function markAsRead(int $messageId)
    {
        $this->requireCreator();
        $this->messageRepo->markAsRead($messageId);
        $this->jsonResponse(['success' => true]);
    }

    public function archiveMessage(int $messageId)
    {
        $this->requireCreator();
        $this->messageRepo->archive($messageId);
        $this->jsonResponse(['success' => true]);
    }
}
