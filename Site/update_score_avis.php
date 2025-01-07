<?php
    error_reporting(E_ALL ^ E_WARNING);

    //start session
    ob_start();

    //connecteur pour requête
    include "../SQL/connection_local.php";

    $id_avis = intval($_GET['id_avis']);
    $score = $_GET['score'];

    if ($score == "plus") {
        $sql = "UPDATE public._avis SET scorepouce = scorepouce + 1 WHERE idavis = :idAvis"; 
    } else {
        $sql = "UPDATE public._avis SET scorepouce = scorepouce - 1 WHERE idavis = :idAvis"; 
    }
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':idAvis', $id_avis, PDO::PARAM_INT);
    $stmt->execute();

    $_SESSION['thumbed'][$id_avis] = true;


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Changement de score de l'avis</title>
</head>
<body>
    
</body>
</html>