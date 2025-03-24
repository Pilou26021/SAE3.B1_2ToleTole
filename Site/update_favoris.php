<?php
if (isset($_GET['idoffre']) && isset($_GET['add']) && $membre) {
    $idoffre = $_GET['idoffre'];
    $add = $_GET['add'] === 'true';

    if ($add) {
        // Ajoute l'offre aux favoris
        // Ton code pour ajouter l'offre aux favoris
    } else {
        // Supprime l'offre des favoris
        // Ton code pour supprimer l'offre des favoris
    }

    // Réponse JSON
    echo json_encode(['status' => 'success']);
    exit();
} else {
    echo json_encode(['status' => 'error']);
    exit();
}
?>