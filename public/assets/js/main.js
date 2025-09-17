// Formatage du temps
function formatTime(totalSeconds) {
    const days = Math.floor(totalSeconds / 86400);
    const hours = Math.floor((totalSeconds % 86400) / 3600);
    const minutes = Math.floor((totalSeconds % 3600) / 60);
    const seconds = totalSeconds % 60;
    
    const paddedDays = String(days).padStart(2, '0');
    const paddedHours = String(hours).padStart(2, '0');
    const paddedMinutes = String(minutes).padStart(2, '0');
    const paddedSeconds = String(seconds).padStart(2, '0');
    
    return `${paddedDays}:${paddedHours}:${paddedMinutes}:${paddedSeconds}`;
}

// Mettre à jour les chronomètres
function updateTimers() {
    try {
        const timerElements = document.querySelectorAll('.timer');
        const now = Math.floor(Date.now() / 1000);
        
        timerElements.forEach(timer => {
            const status = timer.dataset.status;
            const startTime = parseInt(timer.dataset.startTime, 10);
            const elapsedSecondsStored = parseInt(timer.dataset.elapsedSeconds, 10);
            let currentTotalElapsed = isNaN(elapsedSecondsStored) ? 0 : elapsedSecondsStored;

            if (status === 'running' && !isNaN(startTime)) {
                const elapsedSinceLastStart = now - startTime;
                currentTotalElapsed += elapsedSinceLastStart;
                timer.classList.remove('timer-stopped');
            } else {
                timer.classList.add('timer-stopped');
            }
            
            timer.textContent = formatTime(currentTotalElapsed);
        });
    } catch (e) { 
        console.error("Erreur dans updateTimers:", e); 
    }
}

// Confirmation pour suppression de commentaire
function confirmCommentDelete(event) {
    const clickedButton = document.activeElement;
    if (clickedButton && clickedButton.getAttribute('name') === 'delete_comment') {
        if (!confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?')) {
            event.preventDefault();
            return false;
        }
    }
    return true;
}

// Attacher la confirmation aux formulaires de commentaire
function attachCommentFormListeners() {
    const commentForms = document.querySelectorAll('.comment-form');
    commentForms.forEach(form => {
        form.onsubmit = confirmCommentDelete;
    });
}

// Initialisation de la page
function initializePage() {
    // Mettre à jour les timers immédiatement
    updateTimers();
    
    // Mettre à jour toutes les secondes
    if (typeof setInterval === 'function') {
        setInterval(updateTimers, 1000);
    } else { 
        console.error("setInterval n'est pas disponible."); 
    }

    // Animation barre de progression
    const progressBar = document.getElementById('progress-bar');
    if (progressBar) {
        const targetWidth = progressBar.style.width;
        progressBar.style.width = '0%';
        void progressBar.offsetWidth; // Force reflow
        progressBar.style.width = targetWidth;
    }

    // Attacher les écouteurs pour la confirmation de suppression de commentaire
    attachCommentFormListeners();
}

// Exécuter l'initialisation quand le DOM est prêt
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializePage);
} else {
    initializePage();
}
