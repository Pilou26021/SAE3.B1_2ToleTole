#!/bin/bash

REPO_URL="https://github.com/Pilou26021/SAE3.B1_2ToleTole.git"
BRANCH="dev"
TARGET="docker/sae/data/html"
TMP="/tmp/sae-repo"

echo "Clonage du repo..."
sudo git clone --branch $BRANCH $REPO_URL $TMP

#checks

if [ $? -ne 0 ]; then
    echo  "Erreur lors du clonage du repo."
    exit 1
fi

if [ -d "$TARGET"]; then
    echo "Nettoyage des dossiers de pull..."
    sudo rm -rf $TARGET/*
else
    echo "Le dossier de destination n'existe pas, création du dossier..."
    sudo mkdir -p $TARGET
fi

if [ -d "$TMP/Site"]; then
    echo "Déplacement des fichiers..."
        sudo cp -r $TMP/Site/* $TARGET
        echo "Les fichiers ont été déplacés avec succès."
    else
        echo "Le dossier Site/ n'existe pas dans le repo. L'architecture du repo à elle changé?"
        exit 1
fi

# cleaning
echo "Nettoyage..."
sudo rm -rf $TMP
echo "Nettoyage Terminé."

exit 0