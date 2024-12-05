<?php


    error_reporting(E_ALL ^ E_WARNING);
    ob_start();
    session_start();

    // connecteur pour requête
    include "../SQL/connection_local.php";   

    $idoffre = $_GET['idoffre'];
    $idmembre = $_GET['idmembre'];
    $idavis = $_GET['idavis'];
    $idcompte = $_SESSION['membre'];

    // On vérifie si l'utilisateur est connecté
    if (isset($_GET['membre'])) {
        $idcompte = $_GET['membre'];
    } else {
        //rediriger vers l'offre 
        header("Location: details_offre.php?idoffre=$idoffre");
    }

    // On vérifie que le bon utilisateur est connecté
    var_dump($idcompte, $_SESSION['membre']);
    if ($idcompte == $_SESSION['membre']) {
        $bonmembre = true;
    } else {
        //rediriger vers l'offre 
        header("Location: details_offre.php?idoffre=$idoffre");
    }

    // On vérifie que l'avis existe
    $sql = "SELECT * FROM _avis WHERE idavis = :idavis";

    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':idavis', $idavis, PDO::PARAM_INT);
    $stmt->execute();
    // Récupérer l'avis
    $avis_membre = $stmt->fetch();

    if ($avis_membre) {
        $avis_existe = true;
    } else {
        //rediriger vers l'offre 
        header("Location: details_offre.php?idoffre=$idoffre");
    }

    // On vérifie que l'avis appartient bien au membre
    if ($avis_membre['idmembre'] == $idmembre) {
        $avis_appartient = true;
    } else {
        //rediriger vers l'offre 
        header("Location: details_offre.php?idoffre=$idoffre");
    }

    // On vérifie que l'avis est bien lié à cette offre 
    if ($avis_membre['idoffre'] == $idoffre) {
        $avis_offre = true;
    } else {
        //rediriger vers l'offre 
        header("Location: details_offre.php?idoffre=$idoffre");
    }

    var_dump($bonmembre, $avis_existe, $avis_appartient, $avis_offre);

    // On supprime l'avis
    if ($bonmembre && $avis_existe && $avis_appartient && $avis_offre) {
        $sql = "DELETE FROM _avis WHERE idavis = :idavis";

        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':idavis', $idavis, PDO::PARAM_INT);
        $stmt->execute();
    }

?>


<!DOCTYPE html>
    <html lang="fr">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="stylesheet" href="./style.css">
            <title>Supprimer une offre</title>
        </head>

        <body>

            <script
                src="https://code.jquery.com/jquery-3.3.1.js"
                integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
                crossorigin="anonymous">
            </script>
            <script> 
                $(function(){
                $("#header").load("./header.php"); 
                $("#footer").load("footer.html"); 
                });
            </script> 

            <div id="header"></div>

            <main>

                <h1>VOTRE AVIS A BIEN ÉTÉ SUPPRIMÉ.</h1>
                <?php //rediriger vers l'offre après 3 secondes
                    header("refresh:3;url=details_offre.php?idoffre=$idoffre");
                ?>

                <!-- Bouton de retour à l'accueil-->
                <a style="text-decoration:none;" href="index.php"> <button class="offer-btn">Retour à la page d'Accueil</button></a>

            </main>

            <div id="footer"></div>
            <script src="./script.js" ></script>

        </body>
    </html>
