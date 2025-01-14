<?php
    include "../../SQL/connection_local.php";

    $idcompte = $_POST['idcompte'];
    $mdp = $_POST['mdp'];

    $stmt = $conn->prepare("SELECT hashmdpcompte FROM _compte WHERE idcompte = :idcompte");
    $stmt->bindParam(':idcompte', $idcompte, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && password_verify($mdp, $result['hashmdpcompte'])) {
        echo json_encode(array("success" => true));
    } else {
        echo json_encode(array("success" => false));
    }
?>
