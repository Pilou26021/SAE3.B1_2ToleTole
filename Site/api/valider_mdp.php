<?php
    include "../../SQL/connection_local.php";

    // On passe la clé API comme devoilée
    $idcompte = $_POST['idcompte'];
    $mdp = $_POST['mdp'];
    $mdp = password_hash($mdp, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT * FROM _compte WHERE idcompte = :idcompte AND hashmdpcompte = :mdp");
    $stmt->bindParam(':idcompte', $idcompte, PDO::PARAM_INT);
    $stmt->bindParam(':mdp', $mdp, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo json_encode(array("success" => true));
    } else {
        echo json_encode(array("success" => false));
    }

