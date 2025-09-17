<?php
// La variable $creator est fournie par le layout 'creator_dashboard'
// Les variables $messages et $otherUser sont fournies par le contrôleur
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Conversation avec <?= htmlspecialchars($otherUser['name']) ?></h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Messages</h6>
        </div>
        <div class="card-body" style="height: 400px; overflow-y: scroll;">
            <?php foreach ($messages as $message) : ?>
                <?php if ($message->sender_id == $creator['id']) : ?>
                    <!-- Message envoyé par le créateur -->
                    <div class="text-right">
                        <p class="bg-primary text-white p-2 rounded d-inline-block"><strong>Vous:</strong><br><?= htmlspecialchars($message->content) ?></p>
                        <br><small class="text-muted"><?= date('d/m/Y H:i', strtotime($message->created_at)) ?></small>
                    </div>
                <?php else : ?>
                    <!-- Message reçu -->
                    <div>
                        <p class="bg-light p-2 rounded d-inline-block"><strong><?= htmlspecialchars($otherUser['name']) ?>:</strong><br><?= htmlspecialchars($message->content) ?></p>
                        <br><small class="text-muted"><?= date('d/m/Y H:i', strtotime($message->created_at)) ?></small>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <div class="card-footer">
            <form action="/profile/messages/reply/<?= $otherUser['id'] ?>" method="post">
                <div class="input-group">
                    <input type="text" name="content" class="form-control" placeholder="Écrire un message..." required>
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">Envoyer</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
