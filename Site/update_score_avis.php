<?php
    error_reporting(E_ALL ^ E_WARNING);

    //start session
    ob_start();

    //connecteur pour requête
    include "../SQL/connection_local.php";

    $id_avis = $_GET['id_avis'];
    $score = $_GET['score'];

    if ($score == "plus") {
        $sql = "UPDATE public._avis SET scorePouce = scorePouce + 1 WHERE idavis = ?"; 
    } else {
        $sql = "UPDATE public._avis SET scorePouce = scorePouce - 1 WHERE idavis = ?"; 
    }
    $stmt = $conn->prepare($sql);
    $stmt->execute([$avis['idavis']]);


?>