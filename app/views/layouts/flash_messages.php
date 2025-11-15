<?php
use App\Core\Flash;

$flashMessages = [];

if (isset($flash) && $flash instanceof Flash) {
    $single = $flash->get();
    if ($single && !empty($single['message'])) {
        $flashMessages[] = [
            'type' => $single['type'] ?? 'info',
            'message' => $single['message'],
        ];
    }
}

if (!empty($_SESSION['flash']) && is_array($_SESSION['flash']) && isset($_SESSION['flash']['message'])) {
    $flashMessages[] = [
        'type' => $_SESSION['flash']['type'] ?? 'info',
        'message' => $_SESSION['flash']['message'],
    ];
    unset($_SESSION['flash']);
}

if (!empty($_SESSION['flash_messages']) && is_array($_SESSION['flash_messages'])) {
    foreach ($_SESSION['flash_messages'] as $type => $messages) {
        foreach ((array) $messages as $message) {
            $flashMessages[] = [
                'type' => $type,
                'message' => $message,
            ];
        }
    }
    unset($_SESSION['flash_messages']);
}

if (!empty($flashMessages)): ?>
    <div class="flash-stack">
        <?php foreach ($flashMessages as $item): ?>
            <div class="flash-message flash-message--<?= htmlspecialchars(strtolower($item['type'])) ?>">
                <span><?= htmlspecialchars($item['message']) ?></span>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
