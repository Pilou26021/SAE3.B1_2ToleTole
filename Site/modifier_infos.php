<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include "../SQL/connection_local.php";

if (isset($_SESSION['professionnel'])){
    $userID = $_SESSION['professionnel'];

    $sql2 = "UPDATE _professionnel
            SET denominationpro = :denomination,
                numsirenpro = :numsirenpro
            WHERE _professionnel.idcompte = :userID;";
    
    $stmt2 = $conn->prepare($sql2);

    $stmt2->bindValue(':denomination', $_POST["denominationpro"]);
    $stmt2->bindValue(':numsirenpro', $_POST["numsiren"]);
    $stmt2->bindValue(':userID', $userID);

    $stmt2->execute();
    
} else {
    $userID = $_SESSION['membre'];
}

// Requête UPDATE correctement formée
$sql = "UPDATE _compte
        SET nomcompte = :nomC,
            prenomcompte = :prenomC,
            mailcompte = :mailC,
            numtelcompte = :telC
        WHERE _compte.idcompte = :userID;";

// Préparation
$stmt = $conn->prepare($sql);


// Lier les valeurs
$stmt->bindValue(':nomC', $_POST["nom"], PDO::PARAM_STR);
$stmt->bindValue(':prenomC', $_POST["prenom"], PDO::PARAM_STR);
$stmt->bindValue(':mailC', $_POST["email"], PDO::PARAM_STR);
$stmt->bindValue(':telC', $_POST["telephone"], PDO::PARAM_STR);
$stmt->bindValue(':userID', $userID, PDO::PARAM_INT);


// Exécution
$stmt->execute();



// Redirection après la mise à jour
header("Location: mes_infos.php");

?>
