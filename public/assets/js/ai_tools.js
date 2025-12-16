document.addEventListener('DOMContentLoaded', () => {
    const suggestReplyForm = document.getElementById('suggestReplyForm');
    const suggestBtn = document.getElementById('suggestBtn');
    const suggestResultArea = document.getElementById('suggestResultArea');
    const suggestedReplyText = document.getElementById('suggestedReplyText');
    const suggestErrorDiv = document.getElementById('suggestErrorDiv');
    const suggestCsrfInput = document.querySelector('#suggestReplyForm input[name="csrf_token_suggest"]');
    const copyBtns = document.querySelectorAll('.copyBtn');
    // Initialiser ClipboardJS
    copyBtns.forEach(button => { new ClipboardJS(button); });
    suggestReplyForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const donorMessage = document.getElementById('donorMessage').value;
        const selectedTone = document.getElementById('toneSelectorSuggest').value;
        const suggestCsrfToken = suggestCsrfInput ? suggestCsrfInput.value : '';
        if (!donorMessage.trim()) {
            suggestErrorDiv.textContent = 'Veuillez saisir le message du donateur.';
            suggestErrorDiv.style.display = 'block';
            suggestResultArea.style.display = 'block';
            suggestedReplyText.value = '';
            return;
        }
        suggestBtn.disabled = true;
        suggestBtn.innerHTML = `<span class=\"spinner-border spinner-border-sm me-1\" role=\"status\" aria-hidden=\"true\"></span> Suggestion...`;
        suggestedReplyText.value = '';
        suggestErrorDiv.style.display = 'none';
        suggestResultArea.style.display = 'block';
        try {
            const formData = new FormData(suggestReplyForm);
            const response = await fetch('/api/ai/suggest-reply', {
                method: 'POST',
                headers: {
                    'X-CSRF-Token': suggestCsrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            });
            if (!response.ok) {
                let errorMsg = `Erreur HTTP ${response.status}.`;
                try {
                    const errorData = await response.json();
                    errorMsg += ` ${errorData.error || 'Détails indisponibles.'}`;
                } catch(e) {
                    errorMsg += ' Impossible de lire les détails de l\'erreur.';
                }
                throw new Error(errorMsg);
            }
            const data = await response.json();
            if (data.hasOwnProperty('suggested_reply')) {
                suggestedReplyText.value = data.suggested_reply;
                suggestedReplyText.readOnly = false;
            } else {
                throw new Error('Réponse API invalide (clé \'suggested_reply\' manquante).');
            }
        } catch (error) {
            console.error('Erreur lors de la suggestion de réponse:', error);
            suggestErrorDiv.textContent = `Une erreur est survenue: ${error.message || 'Vérifiez la console pour plus de détails.'}`;
            suggestErrorDiv.style.display = 'block';
            suggestedReplyText.value = '';
        } finally {
            suggestBtn.disabled = false;
            suggestBtn.innerHTML = `Suggérer`;
        }
    });
    // Ajouter les événements pour le formulaire d'amélioration de texte
    const enhanceTextForm = document.getElementById('enhanceTextForm');
    const enhanceBtn = document.getElementById('enhanceBtn');
    const enhanceResultArea = document.getElementById('enhanceResultArea');
    const enhancedText = document.getElementById('enhancedText');
    const enhanceErrorDiv = document.getElementById('enhanceErrorDiv');
    const enhanceCsrfInput = document.querySelector('#enhanceTextForm input[name="csrf_token_enhance"]');
    enhanceTextForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const originalText = document.getElementById('originalTextEnhance').value;
        const selectedTone = document.getElementById('toneSelectorEnhance').value;
        const enhanceCsrfToken = enhanceCsrfInput ? enhanceCsrfInput.value : '';
        if (!originalText.trim()) {
            enhanceErrorDiv.textContent = 'Veuillez saisir votre texte.';
            enhanceErrorDiv.style.display = 'block';
            enhanceResultArea.style.display = 'block';
            enhancedText.value = '';
            return;
        }
        enhanceBtn.disabled = true;
        enhanceBtn.innerHTML = `<span class=\"spinner-border spinner-border-sm me-1\" role=\"status\" aria-hidden=\"true\"></span> Amélioration...`;
        enhancedText.value = '';
        enhanceErrorDiv.style.display = 'none';
        enhanceResultArea.style.display = 'block';
        try {
            const formData = new FormData(enhanceTextForm);
            const response = await fetch('/api/ai/enhance-text', {
                method: 'POST',
                headers: {
                    'X-CSRF-Token': enhanceCsrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            });
            if (!response.ok) {
                let errorMsg = `Erreur HTTP ${response.status}.`;
                try {
                    const errorData = await response.json();
                    errorMsg += ` ${errorData.error || 'Détails indisponibles.'}`;
                } catch(e) {
                    errorMsg += ' Impossible de lire les détails de l\'erreur.';
                }
                throw new Error(errorMsg);
            }
            const data = await response.json();
            if (data.hasOwnProperty('enhanced_text')) {
                enhancedText.value = data.enhanced_text;
                enhancedText.readOnly = false;
            } else {
                throw new Error('Réponse API invalide (clé \'enhanced_text\' manquante).');
            }
        } catch (error) {
            console.error('Erreur lors de l\'amélioration de texte:', error);
            enhanceErrorDiv.textContent = `Une erreur est survenue: ${error.message || 'Vérifiez la console pour plus de détails.'}`;
            enhanceErrorDiv.style.display = 'block';
            enhancedText.value = '';
        } finally {
            enhanceBtn.disabled = false;
            enhanceBtn.innerHTML = `Améliorer`;
        }
    });
});
