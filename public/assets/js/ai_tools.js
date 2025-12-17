document.addEventListener('DOMContentLoaded', () => {
    const suggestReplyForm = document.getElementById('suggestReplyForm');
    const suggestBtn = document.getElementById('suggestBtn');
    const suggestResultArea = document.getElementById('suggestResultArea');
    const suggestedReplyText = document.getElementById('suggestedReplyText');
    const suggestErrorDiv = document.getElementById('suggestErrorDiv');
    const suggestCsrfInput = document.querySelector('#suggestReplyForm input[name="csrf_token_suggest"]');
    const copyBtns = document.querySelectorAll('.copyBtn');

    // Initialiser ClipboardJS
    copyBtns.forEach(button => {
        const clipboard = new ClipboardJS(button);
        clipboard.on('success', function (e) {
            const originalHTML = e.trigger.innerHTML;
            e.trigger.innerHTML = '<i class="fas fa-check"></i> Copié !';
            e.trigger.classList.add('text-green-600', 'bg-green-50');
            setTimeout(() => {
                e.trigger.innerHTML = originalHTML;
                e.trigger.classList.remove('text-green-600', 'bg-green-50');
            }, 2000);
            e.clearSelection();
        });
    });

    // Gestion du formulaire "Suggérer une réponse"
    suggestReplyForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const donorMessage = document.getElementById('donorMessage').value;
        const selectedTone = document.getElementById('toneSelectorSuggest').value;
        const suggestCsrfToken = suggestCsrfInput ? suggestCsrfInput.value : '';

        if (!donorMessage.trim()) {
            showError(suggestErrorDiv, 'Veuillez saisir le message du donateur.');
            suggestResultArea.classList.remove('hidden');
            suggestedReplyText.value = '';
            return;
        }

        setLoading(suggestBtn, true);
        suggestedReplyText.value = '';
        hideError(suggestErrorDiv);
        suggestResultArea.classList.remove('hidden');

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
                    errorMsg = errorData.error || errorMsg;
                } catch (e) { }
                throw new Error(errorMsg);
            }

            const data = await response.json();
            if (data.hasOwnProperty('suggested_reply')) {
                // Animation d'écriture "machine à écrire" optionnelle ou affichage direct
                suggestedReplyText.value = data.suggested_reply;
            } else {
                throw new Error('Réponse API invalide.');
            }

        } catch (error) {
            console.error('Erreur:', error);
            showError(suggestErrorDiv, `Oops ! ${error.message}`);
        } finally {
            setLoading(suggestBtn, false, 'Générer la réponse', '<i class="fas fa-wand-magic-sparkles"></i>');
        }
    });

    // Gestion du formulaire "Améliorer un texte"
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
            showError(enhanceErrorDiv, 'Veuillez saisir votre texte.');
            enhanceResultArea.classList.remove('hidden');
            enhancedText.value = '';
            return;
        }

        setLoading(enhanceBtn, true);
        enhancedText.value = '';
        hideError(enhanceErrorDiv);
        enhanceResultArea.classList.remove('hidden');

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
                    errorMsg = errorData.error || errorMsg;
                } catch (e) { }
                throw new Error(errorMsg);
            }

            const data = await response.json();
            if (data.hasOwnProperty('enhanced_text')) {
                enhancedText.value = data.enhanced_text;
            } else {
                throw new Error('Réponse API invalide.');
            }

        } catch (error) {
            console.error('Erreur:', error);
            showError(enhanceErrorDiv, `Impossible d'améliorer le texte : ${error.message}`);
        } finally {
            setLoading(enhanceBtn, false, 'Améliorer le texte', '<i class="fas fa-rocket"></i>');
        }
    });

    // Utilitaires UI
    function setLoading(button, isLoading, originalText = '', icon = '') {
        if (isLoading) {
            button.disabled = true;
            // Spinner Tailwind
            button.innerHTML = `
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                IA en réflexion...
            `;
            button.classList.add('opacity-75', 'cursor-wait');
        } else {
            button.disabled = false;
            button.innerHTML = `<span class="btn-text">${originalText}</span> ${icon}`;
            button.classList.remove('opacity-75', 'cursor-wait');
        }
    }

    function showError(element, message) {
        element.textContent = message;
        element.classList.remove('hidden');
    }

    function hideError(element) {
        element.classList.add('hidden');
    }
});
