<?php
    error_reporting(E_ALL ^ E_WARNING);
    include "header.php";

    //start session
    ob_start();

    //connecteur pour requête
    include "../SQL/connection_local.php";   

    // On vérifie si l'utilisateur est connecté. Il peut être connecté en tant que membre ou professionnel. Si il n'est pas connecté alors il sera visiteur.
    if (isset($_SESSION['membre'])) {
        $membre = true;
        $idmembre = $_SESSION['membre'];
    } else {
        ?> <script>window.location.replace('index.php');</script> <!-- Redirection en quittant la page actuelle --> <?php
    }

    //Récupérer les données de l'utilisateur
    $sql = "SELECT c.idcompte, c.nomcompte, c.prenomcompte, c.idimagepdp, i.pathimage
            FROM _compte c JOIN _image i
            ON c.idimagepdp = i.idimage
            WHERE c.idcompte = :idmembre
    "

    // Préparer et exécuter la requête de tout les avis
    $stmt = $conn->prepare($sql);
    if ($membre){
        $stmt->bindValue(':conn_membre', $idmembre, PDO::PARAM_INT);
    }
    $stmt->bindValue(':idoffre', $idoffre, PDO::PARAM_INT);
    $stmt->execute();

    // Récupérer les avis
    $avis = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="./style.css">
        <title>Détails de l'Offre</title>
    </head>
    <body>
        <script
            src="https://code.jquery.com/jquery-3.3.1.js"
            integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
            crossorigin="anonymous">
        </script>
        <script> 
            $(function(){
                $("#footer").load("footer.html"); 
            });
        </script> 
        <?php 
            // Vérification de l'ID de l'offre dans l'URL
            if (isset($_GET['idoffre'])) {
                $idoffre = intval($_GET['idoffre']);
            } else {
                ?> <script>window.location.replace('index.php');</script> <!-- Redirection en quittant la page actuelle --> <?php
            }
        ?>

        <main>

        <form action="upload_avis.php" method="post">

            <label for=""></label>

        </form>



        </main>
        
        <div id="footer"></div>

        <script src="script.js"></script> 

    </body>

</html>