<?php
include 'connection_local.php';

// Récupère le script de création de la base de données
$sql = file_get_contents(__DIR__ . '/cr_bdd_sprint1_postgresql.sql');

// Exécute le script sur la base de données
if ($conn->exec($sql) === false) {
    echo "Erreur lors de la création de la base de données";
} else {
    echo "Base de données créée avec succès";
}

// Récupère le script de peuplage de la base de données
$sql = file_get_contents(__DIR__ . '/pop_bdd_sprint1_postgresql.sql');

// Exécute le script sur la base de données
if ($conn->exec($sql) === false) {
    echo "Erreur lors du peuplage de la base de données";
} else {
    echo "Base de données peuplée avec succès";
}

?>

