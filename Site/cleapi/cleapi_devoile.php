<?php
    include "../../SQL/connection_local.php";

    // On passe la clé API comme devoilée
    $idcompte = $_POST['idcompte'];
    $stmt = $conn->prepare("SELECT chat_cledevoile FROM _compte WHERE idcompte = :idcompte");
    $stmt->bindParam(':idcompte', $idcompte, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si false on envoie 0 sinon on envoie 1

    if ($result['chat_cledevoile'] == true) {
        echo 1;
    } else {
        echo 0;
    }
?>
    