<?php
// Création de la session

error_reporting(0);

ob_start();
session_start();

include "../SQL/connection_local.php";   

// On vérifie si l'utilisateur est connecté. Il peut être connecté en tant que membre ou professionnel. Si il n'est pas connecté alors il sera visiteur.

$idcompte = $_SESSION['professionnel'];

// On met les avis en "lu"
$mettreEnLu = "UPDATE _notification
               SET lu = true
               WHERE idcompte = :idPro AND lu = false";

$mettreEnLu = $conn->prepare($mettreEnLu);
$mettreEnLu->bindValue(':idPro', $idcompte, PDO::PARAM_INT);
$mettreEnLu->execute();

header("Location: " . $_SERVER['HTTP_REFERER']);
exit();

?>