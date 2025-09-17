
<div class="upload-container">
    <div class="upload-header">
        <h1>Ajouter un média</h1>
        <a href="/media" class="btn-back">
            <i class="fas fa-arrow-left"></i> Retour à la galerie
        </a>
    </div>

    <div class="upload-area" id="dropZone">
        <div class="upload-message">
            <i class="fas fa-cloud-upload-alt"></i>
            <h3>Glissez vos fichiers ici</h3>
            <p>ou</p>
            <label for="fileInput" class="btn-browse">
                Parcourir vos fichiers
            </label>
        </div>
        <input type="file" id="fileInput" multiple accept="image/*,video/*" hidden>
    </div>

    <div class="upload-list" id="uploadList"></div>
</div>

<template id="uploadItemTemplate">
    <div class="upload-item">
        <div class="upload-preview">
            <img src="" alt="Aperçu">
        </div>
        <div class="upload-info">
            <div class="upload-details">
                <h4 class="filename">nom_du_fichier.jpg</h4>
                <span class="filesize">0 MB</span>
            </div>
            <div class="upload-progress">
                <div class="progress-bar">
                    <div class="progress" style="width: 0%"></div>
                </div>
                <span class="progress-text">0%</span>
            </div>
            <div class="upload-actions">
                <button type="button" class="btn-remove">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<style>
.upload-container {
    max-width: 800px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.upload-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.btn-back {
    padding: 0.5rem 1rem;
    text-decoration: none;
    color: #666;
    border: 1px solid #ddd;
    border-radius: 4px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.upload-area {
    border: 2px dashed #ddd;
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    background: #f8f9fa;
    cursor: pointer;
    transition: all 0.3s;
}

.upload-area.dragover {
    border-color: #4CAF50;
    background: #e8f5e9;
}

.upload-message {
    color: #666;
}

.upload-message i {
    font-size: 3rem;
    color: #4CAF50;
    margin-bottom: 1rem;
}

.upload-message h3 {
    margin-bottom: 0.5rem;
}

.btn-browse {
    display: inline-block;
    padding: 0.8rem 1.5rem;
    background: #4CAF50;
    color: white;
    border-radius: 4px;
    cursor: pointer;
    margin-top: 1rem;
}

.upload-list {
    margin-top: 2rem;
}

.upload-item {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 1rem;
}

.upload-preview {
    width: 100px;
    height: 100px;
    border-radius: 4px;
    overflow: hidden;
    background: #f0f0f0;
}

.upload-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.upload-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.upload-details {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.filename {
    margin: 0;
    font-size: 1rem;
    color: #333;
}

.filesize {
    color: #666;
    font-size: 0.9rem;
}

.upload-progress {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.progress-bar {
    flex: 1;
    height: 4px;
    background: #f0f0f0;
    border-radius: 2px;
    overflow: hidden;
}

.progress {
    height: 100%;
    background: #4CAF50;
    transition: width 0.3s;
}

.progress-text {
    min-width: 40px;
    text-align: right;
    font-size: 0.9rem;
    color: #666;
}

.upload-actions {
    display: flex;
    justify-content: flex-end;
}

.btn-remove {
    padding: 0.5rem;
    border: none;
    background: none;
    color: #666;
    cursor: pointer;
}

.btn-remove:hover {
    color: #f44336;
}

@media (max-width: 480px) {
    .upload-item {
        flex-direction: column;
    }
    
    .upload-preview {
        width: 100%;
        height: 200px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const uploadList = document.getElementById('uploadList');
    const template = document.getElementById('uploadItemTemplate');
    
    // Prévenir le comportement par défaut du navigateur
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    // Gérer les effets visuels
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });
    
    function highlight(e) {
        dropZone.classList.add('dragover');
    }
    
    function unhighlight(e) {
        dropZone.classList.remove('dragover');
    }
    
    // Gérer le drop
    dropZone.addEventListener('drop', handleDrop, false);
    
    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles(files);
    }
    
    // Gérer la sélection de fichiers
    fileInput.addEventListener('change', function() {
        handleFiles(this.files);
    });
    
    function handleFiles(files) {
        [...files].forEach(uploadFile);
    }
    
    function uploadFile(file) {
        // Vérifier le type de fichier
        if (!file.type.match(/^(image|video)\//)) {
            alert('Type de fichier non supporté');
            return;
        }
        
        // Créer l'élément d'upload
        const uploadItem = template.content.cloneNode(true);
        const preview = uploadItem.querySelector('.upload-preview img');
        const filename = uploadItem.querySelector('.filename');
        const filesize = uploadItem.querySelector('.filesize');
        const progress = uploadItem.querySelector('.progress');
        const progressText = uploadItem.querySelector('.progress-text');
        
        // Configurer l'aperçu
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = e => preview.src = e.target.result;
            reader.readAsDataURL(file);
        } else {
            preview.src = '/assets/images/video-placeholder.jpg';
        }
        
        // Mettre à jour les informations
        filename.textContent = file.name;
        filesize.textContent = formatFileSize(file.size);
        
        // Ajouter à la liste
        uploadList.appendChild(uploadItem);
        
        // Créer et envoyer le FormData
        const formData = new FormData();
        formData.append('file', file);
        
        // Envoyer le fichier
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/media/store');
        
        xhr.upload.addEventListener('progress', e => {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;
                progress.style.width = percentComplete + '%';
                progressText.textContent = Math.round(percentComplete) + '%';
            }
        });
        
        xhr.onload = function() {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.response);
                if (response.status === 'success') {
                    // Animation de succès
                    progress.style.background = '#4CAF50';
                    setTimeout(() => {
                        uploadItem.querySelector('.upload-item').remove();
                    }, 1000);
                } else {
                    showError(uploadItem, response.message);
                }
            } else {
                showError(uploadItem, 'Erreur lors de l\'upload');
            }
        };
        
        xhr.onerror = () => showError(uploadItem, 'Erreur réseau');
        
        xhr.send(formData);
    }
    
    function showError(uploadItem, message) {
        const progress = uploadItem.querySelector('.progress');
        progress.style.background = '#f44336';
        uploadItem.querySelector('.progress-text').textContent = 'Erreur';
        alert(message);
    }
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    // Gérer la suppression
    uploadList.addEventListener('click', e => {
        if (e.target.closest('.btn-remove')) {
            e.target.closest('.upload-item').remove();
        }
    });
});
</script>

