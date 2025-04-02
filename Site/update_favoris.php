<?php
    ob_start();
    session_start();

    // connecteur pour requête
    include "../SQL/connection_local.php";
    
    var_dump($_SESSION);
    var_dump($_GET);

    if (isset($_GET['idoffre']) && isset($_GET['add'])) {
        $idoffre = $_GET['idoffre'];
        $add = $_GET['add'] === 'true';
        var_dump($idoffre);
        var_dump($add);

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

        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    } else {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
?>