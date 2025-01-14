<?php
    include "../../SQL/connection_local.php";

    // On passe la clé API comme devoilée
    $idcompte = $_POST['idcompte'];
    $stmt = $conn->prepare("UPDATE _compte SET chat_cledevoile = true WHERE idcompte = :idcompte");
    $stmt->bindParam(':idcompte', $idcompte, PDO::PARAM_INT);
    $stmt->execute();
?>

