
<div class="donation-error">
    <div class="error-message">
        <i class="fas fa-exclamation-circle"></i>
        <h1>Une erreur est survenue</h1>
        <p>Le traitement de votre don n'a pas pu être complété.</p>
        <p>Veuillez réessayer ou contacter le support si le problème persiste.</p>
        
        <div class="actions">
            <a href="javascript:history.back()" class="btn btn-secondary">Retour</a>
            <a href="/" class="btn btn-primary">Accueil</a>
        </div>
    </div>
</div>

<style>
.donation-error {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 60vh;
    padding: 2rem;
}

.error-message {
    text-align: center;
    max-width: 600px;
    padding: 2rem;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.error-message i {
    font-size: 4rem;
    color: #f44336;
    margin-bottom: 1rem;
}

.error-message h1 {
    color: #333;
    margin-bottom: 1rem;
}

.error-message p {
    color: #666;
    margin-bottom: 1rem;
}

.actions {
    margin-top: 2rem;
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.btn {
    padding: 0.8rem 1.5rem;
    border-radius: 4px;
    text-decoration: none;
    transition: background 0.3s;
}

.btn-primary {
    background: #4CAF50;
    color: white;
}

.btn-secondary {
    background: #757575;
    color: white;
}

.btn-primary:hover {
    background: #45a049;
}

.btn-secondary:hover {
    background: #616161;
}
</style>

