<?php 
    ob_start();
    session_start();
    include "../SQL/connection_local.php";

    $sql = "
        SELECT update_blacklist();
    ";
    // Préparer et exécuter la requête
    $stmt = $conn->prepare($sql);
    $stmt->execute();

?>