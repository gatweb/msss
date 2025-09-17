document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('donationForm');
    if (!form) return;

    const amountInput = document.getElementById('amount');
    const amountButtons = document.querySelectorAll('.amount-btn');
    const submitButton = form.querySelector('button[type="submit"]');

    // Gestion des boutons de montant
    amountButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const amount = this.dataset.amount;
            amountInput.value = amount;
            
            // Mise à jour de l'état actif des boutons
            amountButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            // Animation du bouton
            this.classList.add('clicked');
            setTimeout(() => this.classList.remove('clicked'), 200);
        });
    });

    // Validation du montant en temps réel
    amountInput.addEventListener('input', function() {
        const amount = parseFloat(this.value);
        if (amount < 1) {
            this.classList.add('error');
            submitButton.disabled = true;
        } else {
            this.classList.remove('error');
            submitButton.disabled = false;
        }
    });

    // Gestion du formulaire
    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        const amount = parseFloat(amountInput.value);
        if (amount < 1) {
            showError('Le montant minimum est de 1€');
            return;
        }

        // Désactiver le bouton pendant le traitement
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Traitement...';

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
                showError(data.error);
                return;
            }

            // Redirection vers Stripe Checkout
            window.location.href = data.url;

        } catch (error) {
            console.error('Erreur:', error);
            showError('Une erreur est survenue. Veuillez réessayer.');
        } finally {
            // Réactiver le bouton
            submitButton.disabled = false;
            submitButton.innerHTML = '<i class="fas fa-heart"></i> Faire un don';
        }
    });
});

function showError(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'alert alert-error';
    errorDiv.innerHTML = `
        <i class="fas fa-exclamation-circle"></i>
        ${message}
        <button type="button" class="close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;

    const container = document.querySelector('.donation-form-container');
    container.insertBefore(errorDiv, container.firstChild);

    // Auto-disparition après 5 secondes
    setTimeout(() => errorDiv.remove(), 5000);
}
