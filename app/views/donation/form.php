<?php require_once APP_PATH . '/views/layouts/header.php'; ?>

<div class="donation-form-container">
    <div class="donation-form">
        <h1>Faire un don à <?= htmlspecialchars($creator['name']) ?></h1>
        
        <form id="donationForm" class="form">
            <input type="hidden" name="creator_id" value="<?= $creator['id'] ?>">
            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
            
            <div class="form-group">
                <label for="amount">Montant (€)</label>
                <div class="amount-buttons">
                    <button type="button" class="amount-btn" data-amount="5">5€</button>
                    <button type="button" class="amount-btn" data-amount="10">10€</button>
                    <button type="button" class="amount-btn" data-amount="20">20€</button>
                    <button type="button" class="amount-btn" data-amount="50">50€</button>
                </div>
                <div class="custom-amount">
                    <input type="number" id="amount" name="amount" min="1" step="1" required
                           placeholder="Montant personnalisé">
                    <span class="currency">€</span>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required
                       placeholder="Votre adresse email">
                <small>Pour recevoir votre reçu de don</small>
            </div>

            <div class="form-group">
                <label for="message">Message (optionnel)</label>
                <textarea id="message" name="message" rows="3"
                          placeholder="Laissez un message à <?= htmlspecialchars($creator['name']) ?>"></textarea>
            </div>

            <button type="submit" class="btn-donate">
                <i class="fas fa-heart"></i> Faire un don
            </button>
        </form>
    </div>
</div>

<style>
.donation-form-container {
    max-width: 600px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.donation-form {
    background: #fff;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.donation-form h1 {
    text-align: center;
    color: #333;
    margin-bottom: 2rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #555;
}

.amount-buttons {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.amount-btn {
    padding: 0.8rem;
    border: 2px solid #4CAF50;
    background: white;
    color: #4CAF50;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s;
}

.amount-btn:hover,
.amount-btn.active {
    background: #4CAF50;
    color: white;
}

.custom-amount {
    position: relative;
}

.custom-amount input {
    width: 100%;
    padding: 0.8rem;
    padding-right: 2rem;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.custom-amount .currency {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #666;
}

input[type="email"],
textarea {
    width: 100%;
    padding: 0.8rem;
    border: 1px solid #ddd;
    border-radius: 4px;
}

small {
    color: #666;
    font-size: 0.8rem;
}

.btn-donate {
    width: 100%;
    padding: 1rem;
    background: #4CAF50;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 1.1rem;
    cursor: pointer;
    transition: background 0.3s;
}

.btn-donate:hover {
    background: #45a049;
}

.btn-donate i {
    margin-right: 0.5rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('donationForm');
    const amountInput = document.getElementById('amount');
    const amountButtons = document.querySelectorAll('.amount-btn');

    // Gestion des boutons de montant
    amountButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const amount = this.dataset.amount;
            amountInput.value = amount;
            
            // Mise à jour de l'état actif des boutons
            amountButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Gestion du formulaire
    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        const amount = parseFloat(amountInput.value);
        if (amount < 1) {
            alert('Le montant minimum est de 1€');
            return;
        }

        try {
            const response = await fetch('/donation/initiate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(new FormData(form))
            });

            const data = await response.json();
            
            if (data.error) {
                alert(data.error);
                return;
            }

            // Redirection vers Stripe Checkout
            window.location.href = data.url;

        } catch (error) {
            console.error('Erreur:', error);
            alert('Une erreur est survenue. Veuillez réessayer.');
        }
    });
});
</script>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
