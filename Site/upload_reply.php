<?php 

    
    ob_start();
    include "header.php";
    include "../SQL/connection_local.php";

    // On vérifie si l'utilisateur est connecté. Il peut être connecté en tant que membre ou professionnel. Si il n'est pas connecté alors il sera visiteur.
    if (isset($_SESSION['professionnel'])) {
        $professionel = true;
        //récupération de l'id du pro
        $idPro = $_SESSION['professionnel'];
    } else {
        header('Location: index.php');
    }

    $idavis = intval($_POST['idavis']);
    $reply = $_POST['reply'];
    $date = new DateTime('now', new DateTimeZone('Europe/Paris'));
    $date = $date->format('Y-m-d');

    // On insère la réponse dans la base de données
    $sql = "INSERT INTO _reponseavis (idavis, textereponse, datereponse) VALUES (:idavis, :reply, :datereponse)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':idavis', $idavis, PDO::PARAM_INT);
    $stmt->bindParam(':reply', $reply, PDO::PARAM_STR);
    $stmt->bindParam(':datereponse', $date, PDO::PARAM_STR);
    $stmt->execute();

    // On met à jour la table avis pour dire que l'avis a été répondu
    $sql = "UPDATE _avis SET reponsepro = true WHERE idavis = :idavis";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':idavis', $idavis, PDO::PARAM_INT);
    $stmt->execute();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Envoie de la réponse à l'avis</title>
</head>
<body>

    <h1>Votre réponse a bien été envoyée</h1>
    <?php
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    ?>
    
</body>
</html>