<?php
    include "../../SQL/connection_local.php";

    // On passe la clé API comme devoilée
    $idcompte = $_POST['idcompte'];
    $mdp = $_POST['mdp'];
    $mdp = password_hash($mdp, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE _compte SET hashmdpcompte = :mdp WHERE idcompte = :idcompte");
    $stmt->bindParam(':idcompte', $idcompte, PDO::PARAM_INT);
    $stmt->bindParam(':mdp', $mdp, PDO::PARAM_STR);
    $stmt->execute();