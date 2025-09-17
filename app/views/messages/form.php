<?php
// Formulaire de contact pour la messagerie (placeholder)
?>
<div class="container" style="margin-top:2rem;">
    <div class="row">
        <div class="col s12 m8 offset-m2">
            <div class="card hoverable">
                <div class="card-content">
                    <span class="card-title"><i class="far fa-envelope left"></i>Envoyer un message</span>
                    <form method="post" action="/messages/send">
                        <div class="input-field">
                            <input id="subject" name="subject" type="text" required>
                            <label for="subject">Sujet</label>
                        </div>
                        <div class="input-field">
                            <textarea id="message" name="message" class="materialize-textarea" required></textarea>
                            <label for="message">Votre message</label>
                        </div>
                        <button type="submit" class="btn waves-effect waves-light grey lighten-3 grey-text text-darken-3">
                            <i class="fas fa-paper-plane left"></i>Envoyer
                        </button>
                    </form>
                    <p class="grey-text" style="margin-top:2rem;">L'adresse email du destinataire ne sera jamais affichée. La créatrice pourra répondre via un formulaire similaire.</p>
                </div>
            </div>
        </div>
    </div>
</div>
