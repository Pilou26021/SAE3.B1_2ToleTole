<?php 

    ob_start();
    session_start();
    include "../SQL/connection_local.php";

    $id_avis = intval($_GET['id_avis']);

    $sql = "UPDATE public._avis SET scorepouce = scorepouce + 1 WHERE idavis = :idAvis";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':idAvis', $id_avis, PDO::PARAM_INT);
    $stmt->execute();
    $_SESSION['thumbed'][$id_avis] = true;
    
?>