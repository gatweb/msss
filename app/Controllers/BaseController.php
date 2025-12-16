<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Auth;
use App\Core\Flash;
use App\Repositories\CreatorRepository;

class BaseController {
    protected $view;
    protected $auth;
    protected $flash;
    protected $creatorRepository;
    protected $user;
    protected $creator = null;
    protected $dailyTip = null;

    public function __construct(View $view, Auth $auth, Flash $flash, CreatorRepository $creatorRepository) {
        $this->view = $view;
        $this->auth = $auth;
        $this->flash = $flash;
        $this->creatorRepository = $creatorRepository;

        if ($this->auth->isLoggedIn()) {
            $userId = $_SESSION['user_id'];
            $this->user = [
                'id' => $userId,
                'username' => $_SESSION['username'] ?? '',
                'is_admin' => $_SESSION['is_admin'] ?? false
            ];

            if (isset($_SESSION['creator_id']) && $_SESSION['creator_id'] === $userId) {
                $creatorId = $_SESSION['creator_id'];
                $this->creator = $this->creatorRepository->findById($creatorId);
            }
        }
    }

    protected function getAppropriateLayout($layout = null) {
        if ($layout) {
            return $layout;
        }

        if (isset($this->creator)) {
            return 'creator_dashboard';
        }

        return 'user';
    }

    protected function render($view, $data = [], $layout = null) {
        $layoutName = $layout ?? $this->getAppropriateLayout();
        $layoutPath = APP_PATH . "/views/layouts/{$layoutName}.php";
        $viewPath = APP_PATH . "/views/{$view}.php";

        if (!file_exists($layoutPath)) {
            throw new \Exception("Layout file not found: {$layoutPath}");
        }
        if (!file_exists($viewPath)) {
            throw new \Exception("View file not found: {$viewPath}");
        }

        if ($layoutName === 'creator_dashboard') {
            if (!isset($data['creator'])) {
                if (isset($this->creator)) {
                    $data['creator'] = $this->creator;
                } else {
                    $data['creator'] = ['id' => null, 'name' => 'Inconnu'];
                }
            }

            if (!isset($data['dailyTip'])) {
                $data['dailyTip'] = $this->dailyTip;
            }
        }

        extract($data);
        $flashMessages = $this->flash->get();

        ob_start();

        require $viewPath;

        $content = ob_get_clean();

        require $layoutPath;
    }
}
