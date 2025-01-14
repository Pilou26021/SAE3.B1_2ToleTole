<?php
    include "../../SQL/connection_local.php";

    // On passe la clé API comme devoilée
    $idcompte = $_POST['idcompte'];
    $cleapi = $_POST['cleapi'];
    $stmt = $conn->prepare("UPDATE _compte SET chat_cleapi = :cleapi, chat_cledevoile = false WHERE idcompte = :idcompte"); 
    $stmt->bindParam(':idcompte', $idcompte, PDO::PARAM_INT);
    $stmt->bindParam(':cleapi', $cleapi, PDO::PARAM_STR);
    $stmt->execute();

