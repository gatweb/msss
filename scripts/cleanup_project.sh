#!/bin/bash
# Script de nettoyage du projet Msss
# À utiliser avec précaution !

set -e

# 1. Dossiers/fichiers à conserver (aucune action)
# - app/
# - database/
# - public/
# - storage/
# - vendor/
# - .env .env.example
# - composer.json composer.lock composer.phar
# - DEVBOOK.md cdc.txt bdd.txt
# - scripts/ (sauf anciens scripts inutilisés)
# - logs/ images/ (tu as choisi de les garder)

# 2. Dossiers/fichiers à supprimer/archiver (exemple, à adapter selon ton usage)
# (Décommenter les lignes si tu veux vraiment supprimer)

# echo "Suppression d'anciens dumps SQL..."
# rm -vf database/*.sql 2>/dev/null || true

# echo "Suppression de scripts orphelins..."
# rm -vf scripts/old_script.php 2>/dev/null || true

# echo "Suppression de fichiers temporaires..."
# find . -type f -name "*~" -delete
# find . -type f -name "*.bak" -delete
# find . -type f -name "*.tmp" -delete

# 3. Sécurisation (rappel)
echo "\n[INFO] Pense à protéger tes fichiers sensibles (.env, bdd.txt, etc.) via .htaccess ou config serveur !"
echo "[INFO] Les dossiers uploads/, logs/, storage/ ne doivent pas être exposés publiquement."

echo "\nNettoyage terminé (aucune suppression automatique sans ton accord explicite)."
