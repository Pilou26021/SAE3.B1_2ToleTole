<?php 

    ob_start();
    session_start();
    include "../SQL/connection_local.php";

    $raison = $_POST['raison'];
    $idavis = $_POST['idavis'];

    var_dump($raison);
    var_dump($idavis);

    $sql = "INSERT INTO _alerteravis (idsignalement, idavis) VALUES ($raison, $idavis)";
    $stmt = $conn->prepare($sql);
    try {
        $stmt->execute();
        $_SESSION['signalement_avis_ok'] = true;
    } catch (Exception $e) {
        $_SESSION['signalement_avis_ok'] = false;
    }
    
?>