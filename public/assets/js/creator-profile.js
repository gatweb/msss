document.addEventListener('DOMContentLoaded', function() {
    const donationForm = document.querySelector('.donation-form');
    if (donationForm) {
        donationForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const amount = parseFloat(document.getElementById('amount').value);
            if (isNaN(amount) || amount <= 0) {
                alert('Veuillez entrer un montant valide.');
                return;
            }
            
            // Ici, nous pourrions ajouter la logique pour traiter le don
            // Pour l'instant, nous allons juste simuler une soumission réussie
            alert('Merci pour votre don ! Vous allez être redirigé vers la page de paiement.');
            this.submit();
        });
    }
});
