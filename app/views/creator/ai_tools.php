<?php error_log("=== DEBUG: ai_tools.php appelé ==="); ?>
<div class="row">
    <!-- Card 1: Suggestion de Réponse -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-lightbulb-fill me-2"></i>Suggérer une Réponse
            </div>
            <div class="card-body">
                <form id="suggestReplyForm">
                    <input type="hidden" name="csrf_token_suggest" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="mb-3">
                        <label for="donorMessage" class="form-label">Message Reçu du Donateur</label>
                        <textarea class="form-control" id="donorMessage" name="donorMessage" rows="5" required placeholder="Collez ici le message reçu..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="toneSelectorSuggest" class="form-label">Ton souhaité pour la Réponse</label>
                        <select class="form-select" id="toneSelectorSuggest" name="tone">
                            <option value="normal" selected>Normal / Poli</option>
                            <option value="informel">Informel</option>
                            <option value="drole">Drôle</option>
                            <option value="commercial">Commercial</option>
                            <option value="strict">Strict</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success" id="suggestBtn">
                        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true" style="display: none;"></span>
                        Suggérer
                    </button>
                </form>
                <!-- Zone de résultat Suggestion -->
                <div id="suggestResultArea" class="mt-3" style="display: none;">
                    <h6>Suggestion :</h6>
                    <div id="suggestErrorDiv" class="alert alert-danger" style="display: none;"></div>
                    <textarea class="form-control" id="suggestedReplyText" rows="6" readonly></textarea>
                    <button class="btn btn-sm btn-secondary mt-2 copyBtn" data-clipboard-target="#suggestedReplyText">
                        <i class="bi bi-clipboard"></i> Copier
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Card 2: Améliorer un Texte -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil-fill me-2"></i>Améliorer un Texte
            </div>
            <div class="card-body">
                <form id="enhanceTextForm">
                     <input type="hidden" name="csrf_token_enhance" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="mb-3">
                        <label for="originalTextEnhance" class="form-label">Votre Texte Original</label>
                        <textarea class="form-control" id="originalTextEnhance" name="originalText" rows="5" required placeholder="Écrivez ou collez votre texte ici..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="toneSelectorEnhance" class="form-label">Ton souhaité pour l'Amélioration</label>
                        <select class="form-select" id="toneSelectorEnhance" name="tone">
                             <option value="normal" selected>Normal / Poli</option>
                            <option value="informel">Informel</option>
                            <option value="drole">Drôle</option>
                            <option value="commercial">Commercial</option>
                            <option value="strict">Strict</option>
                        </select>
                    </div>
                     <button type="submit" class="btn btn-primary" id="enhanceBtn">
                        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true" style="display: none;"></span>
                        Améliorer
                    </button>
                </form>
                 <!-- Zone de résultat Amélioration -->
                <div id="enhanceResultArea" class="mt-3" style="display: none;">
                    <h6>Texte Amélioré :</h6>
                    <div id="enhanceErrorDiv" class="alert alert-danger" style="display: none;"></div>
                    <textarea class="form-control" id="enhancedText" rows="6" readonly></textarea>
                    <button class="btn btn-sm btn-secondary mt-2 copyBtn" data-clipboard-target="#enhancedText">
                        <i class="bi bi-clipboard"></i> Copier
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/clipboard@2/dist/clipboard.min.js"></script>
<script>
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
</script>
