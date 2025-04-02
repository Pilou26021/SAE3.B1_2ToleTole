<?php
    ob_start();
    session_start();

    // connecteur pour requête
    include "../SQL/connection_local.php";   

    if (isset($_POST['idoffre']) && isset($_POST['add'])) {
        $idoffre = $_POST['idoffre'];
        $add = $_POST['add'] === 'true';

        if ($add) {
            // Ajoute l'offre aux favoris
            $stmt = $conn->prepare("INSERT INTO _favoris (idmembre, idoffre, dateajout) VALUES (:idmembre, :idoffre, NOW())");
            $stmt->bindParam(':idmembre', $_SESSION['idmembre']);
            $stmt->bindParam(':idoffre', $idoffre);
            $stmt->execute();

        } else {
            // Supprime l'offre des favoris
            $stmt = $conn->prepare("DELETE FROM _favoris WHERE idmembre = :idmembre AND idoffre = :idoffre");
            $stmt->bindParam(':idmembre', $_SESSION['idmembre']);
            $stmt->bindParam(':idoffre', $idoffre);
            $stmt->execute();
        }

        // Réponse JSON
        echo json_encode(['status' => 'success']);
        exit();
    } else {
        echo json_encode(['status' => 'error']);
        exit();
    }
?>