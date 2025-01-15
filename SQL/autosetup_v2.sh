#!/bin/bash

REPO_URL="https://github.com/Pilou26021/SAE3.B1_2ToleTole.git"
BRANCH="dev"
DESTINATION_DIR="/docker/sae/data/html"
TEMP_DIR="/tmp/sae-repo"

echo "Clonage du dépôt..."
sudo git clone --branch $BRANCH $REPO_URL $TEMP_DIR

# check si ok
if [ $? -ne 0 ]; then
    echo "Erreur lors du clonage du dépôt."
    exit 1
fi

if [ -d "$DESTINATION_DIR" ]; then
    echo "Nettoyage du dossier de destination..."
    sudo rm -rf $DESTINATION_DIR/*
else
    echo "Le dossier de destination n'existe pas, création du dossier..."
    sudo mkdir -p $DESTINATION_DIR
fi

if [ -d "$TEMP_DIR/Site" ]; then
    echo "Déplacement des fichiers..."
    sudo cp -r $TEMP_DIR/Site/* $DESTINATION_DIR
    echo "Les fichiers ont été déplacés avec succès."
else
    echo "Le dossier Site/ n'existe pas dans le dépôt. L'architecture du repo à elle changé?"
    exit 1
fi

# déplacement des script de création et peuplement de la bdd
sudo rm /docker/sae/data/SQL/cr_bdd_sprint1_postgresql.sql 
sudo cp $TEMP_DIR/SQL/cr_bdd_sprint1_postgresql.sql /docker/sae/data/SQL
echo "cr_bdd_sprint1_postgresql.sql déplacé"

sudo rm /docker/sae/data/SQL/pop_bdd_sprint1_postgresql.sql
sudo cp $TEMP_DIR/SQL/pop_bdd_sprint1_postgresql.sql /docker/sae/data/SQL
echo "pop_bdd_sprint1_postgresql.sql déplacé"

# chmod 

# cleanup
sudo rm -rf $TEMP_DIR
echo "Nettoyage terminé."

php /docker/sae/data/SQL/pop_bdd.php
#!/bin/bash

REPO_URL="https://github.com/Pilou26021/SAE3.B1_2ToleTole.git"
BRANCH="dev"
DESTINATION_DIR="/docker/sae/data/html"
TEMP_DIR="/tmp/sae-repo"

echo "Clonage du dépôt..."
sudo git clone --branch $BRANCH $REPO_URL $TEMP_DIR

# check si ok
if [ $? -ne 0 ]; then
    echo "Erreur lors du clonage du dépôt."
    exit 1
fi

if [ -d "$DESTINATION_DIR" ]; then
    echo "Nettoyage du dossier de destination..."
    sudo rm -rf $DESTINATION_DIR/*
else
    echo "Le dossier de destination n'existe pas, création du dossier..."
    sudo mkdir -p $DESTINATION_DIR
fi

if [ -d "$TEMP_DIR/Site" ]; then
    echo "Déplacement des fichiers..."
    sudo cp -r $TEMP_DIR/Site/* $DESTINATION_DIR
    echo "Les fichiers ont été déplacés avec succès."
else
    echo "Le dossier Site/ n'existe pas dans le dépôt. L'architecture du repo à elle changé?"
    exit 1
fi

# déplacement des script de création et peuplement de la bdd
sudo rm /docker/sae/data/SQL/cr_bdd_sprint1_postgresql.sql 
sudo cp $TEMP_DIR/SQL/cr_bdd_sprint1_postgresql.sql /docker/sae/data/SQL
echo "cr_bdd_sprint1_postgresql.sql déplacé"

sudo rm /docker/sae/data/SQL/pop_bdd_sprint1_postgresql.sql
sudo cp $TEMP_DIR/SQL/pop_bdd_sprint1_postgresql.sql /docker/sae/data/SQL
echo "pop_bdd_sprint1_postgresql.sql déplacé"

# chmod image
sudo chmod 777 /docker/sae/data/html/img
sudo chmod 777 /docker/sae/data/html/img/*
sudo chmod 777 /docker/sae/data/html/img/icons
sudo chmod 777 /docker/sae/data/html/img/icons/*
sudo chmod 777 /docker/sae/data/html/img/logos
sudo chmod 777 /docker/sae/data/html/img/logos/*
sudo chmod 777 /docker/sae/data/html/img/uploaded
sudo chmod 777 /docker/sae/data/html/img/uploaded/*
echo "Droits images accordés"

# cleanup
sudo rm -rf $TEMP_DIR
echo "Nettoyage terminé."

php /docker/sae/data/SQL/pop_bdd.php
echo "Bdd nettoyé, crée et peuplé"

exit 0