document.addEventListener('DOMContentLoaded', function() {
    // Gestion des timers
    const timers = document.querySelectorAll('.donation-timer');
    timers.forEach(initializeTimer);

    // Gestion des actions sur les dons
    document.querySelectorAll('.donation-actions button').forEach(button => {
        button.addEventListener('click', handleDonationAction);
    });

    // Gestion des commentaires
    document.querySelectorAll('.donation-comment button').forEach(button => {
        button.addEventListener('click', handleCommentSave);
    });
});

function initializeTimer(timerElement) {
    const status = timerElement.dataset.status;
    const elapsed = parseInt(timerElement.dataset.elapsed);
    const display = timerElement.querySelector('.timer-display');
    let seconds = elapsed;
    
    updateTimerDisplay(display, seconds);
    
    if (status === 'running') {
        const interval = setInterval(() => {
            seconds++;
            updateTimerDisplay(display, seconds);
        }, 1000);
        
        timerElement.dataset.interval = interval;
    }
    
    const button = timerElement.querySelector('button');
    button.addEventListener('click', () => handleTimerAction(timerElement));
}

function updateTimerDisplay(display, totalSeconds) {
    const hours = Math.floor(totalSeconds / 3600);
    const minutes = Math.floor((totalSeconds % 3600) / 60);
    const seconds = totalSeconds % 60;
    
    display.textContent = [
        hours.toString().padStart(2, '0'),
        minutes.toString().padStart(2, '0'),
        seconds.toString().padStart(2, '0')
    ].join(':');
}

async function handleTimerAction(timerElement) {
    const donationId = timerElement.dataset.id;
    const action = timerElement.querySelector('button').dataset.action;
    
    try {
        const response = await fetch('/donations/timer', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                donation_id: donationId,
                action: action
            })
        });
        
        if (!response.ok) throw new Error('Erreur réseau');
        
        const data = await response.json();
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Une erreur est survenue');
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Une erreur est survenue');
    }
}

async function handleDonationAction(event) {
    const button = event.currentTarget;
    const action = button.classList.contains('button-delete') ? 'delete' : 'edit';
    const donationId = button.dataset.id;
    
    if (action === 'delete') {
        if (!confirm('Êtes-vous sûr de vouloir supprimer ce don ?')) return;
        
        try {
            const response = await fetch(`/donations/${donationId}`, {
                method: 'DELETE'
            });
            
            if (!response.ok) throw new Error('Erreur réseau');
            
            const data = await response.json();
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Une erreur est survenue');
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert('Une erreur est survenue');
        }
    }
}

async function handleCommentSave(event) {
    const button = event.currentTarget;
    const donationId = button.dataset.id;
    const textarea = button.parentElement.querySelector('textarea');
    const comment = textarea.value;
    
    try {
        const response = await fetch(`/donations/${donationId}/comment`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ comment })
        });
        
        if (!response.ok) throw new Error('Erreur réseau');
        
        const data = await response.json();
        if (data.success) {
            alert('Commentaire sauvegardé');
        } else {
            alert(data.message || 'Une erreur est survenue');
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Une erreur est survenue');
    }
}
