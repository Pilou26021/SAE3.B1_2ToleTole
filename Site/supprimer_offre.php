<?php
    error_reporting(E_ALL ^ E_WARNING);
    ob_start();
    session_start();

    // connecteur pour requête
    include "./SQL/connection_envdev.php";   

    // On vérifie si l'utilisateur est connecté. Il peut être connecté en tant que membre ou professionnel. Si il n'est pas connecté alors il sera visiteur.
    if (isset($_SESSION['professionnel'])) {
        $professionel = true;
        //récupération de l'id du pro
        $idProPropose = $_SESSION['professionnel'];
    } else {
        ?> <script>window.location.replace('index.php');</script> <!-- Redirection en quittant la page actuelle --> <?php
    }

    if (isset($_GET)){

        $idOffre = $_GET['idoffre']; //récupérer l'id de l'offre en paramètres

        $sql = "SELECT EXISTS(SELECT 1 FROM public._offre WHERE idoffre = :idoffre AND idpropropose = :idpropropose) AS exists";
        try {
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':idoffre', $idOffre);
            $stmt->bindParam(':idpropropose', $idProPropose);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $bonPro = $result['exists'];
        } catch (PDOException $e) {
            //echo "<br>Error: " . $e->getMessage();
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
        <?php

        if($bonPro){

            $sql = "SELECT public.trouver_categorie_offre(:idoffre) AS categorie";
            try {
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':idoffre', $idOffre);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $categorie = $result['categorie'];
            } catch (PDOException $e) {
                //echo "<br>Error: " . $e->getMessage();
            }

            switch ($categorie) {
                case 1:
                    $categorie = 'activite';
                    break;
                case 2:
                    $categorie = 'parc';
                    break;
                case 3:
                    $categorie = 'restauration';
                    break;
                case 4:
                    $categorie = 'spectacle';
                    break;
                case 5:
                    $categorie = 'visite';
                    break;
                default:
                    $categorie = 'erreur';
                    break;
            }

            //récupérer l'array d'id de tag lié à l'offre
            $sql = "SELECT idtag FROM public._theme WHERE idoffre = :idoffre";
            try {
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':idoffre', $idOffre);
                $stmt->execute();
                $idTags = $stmt->fetchAll(PDO::FETCH_COLUMN);
                print_r($idTags);
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }

            $sql = "DELETE FROM public._theme WHERE idTag = :idTag";
            try {
                $stmt = $conn->prepare($sql);
                foreach ($idTags as $idTag) {
                    $stmt->bindParam(':idTag', $idTag);
                    $stmt->execute();
                }
                //echo "<br>Tags supprimés avec succès.";
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }


            //récupérer l'id de l'image pour la supprimer
            $sql = "SELECT idimage FROM public._afficherImageOffre WHERE idoffre = :idoffre";
            try {
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':idoffre', $idOffre);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $idImage = $result['idimage'];
                //echo "<br>ID de l'image: " . $idImage;
            } catch (PDOException $e) {
                //echo "<br>Error: " . $e->getMessage();
            }

            unlink("./img/uploaded/image" . strval($idImage) . ".png");

            //delete de _afficherimageoffre
            $sql = "DELETE FROM public._afficherimageoffre WHERE idoffre = :idoffre";
            try {
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':idoffre', $idOffre);
                $stmt->execute();
                //echo "<br>Offre supprimée avec succès de _afficherimageoffre";
            } catch (PDOException $e) {
                //echo "<br>Error: " . $e->getMessage();
            }

            //delete de _image
            $sql = "DELETE FROM public._image WHERE idimage = :idimage";
            try {
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':idimage', $idImage);
                $stmt->execute();
                //echo "<br>Lien de l'image supprimée de _image";
            } catch (PDOException $e) {
                //echo "<br>Error: " . $e->getMessage();
            }

            if ($categorie == 'activite') { //delete de _offreactivite
                $sql = "DELETE FROM public._offreactivite WHERE idoffre = :idoffre";
                try {
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':idoffre', $idOffre);
                    $stmt->execute();
                    //echo "<br>Offre supprimée avec succès de _offreactivite";
                } catch (PDOException $e) {
                    //echo "<br>Error: " . $e->getMessage();
                }
            } else if ($categorie == 'parc') { //delete de _offreparcattraction
                $sql = "DELETE FROM public._offreparcattraction WHERE idoffre = :idoffre";
                try {
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':idoffre', $idOffre);
                    $stmt->execute();
                    //echo "<br>Offre supprimée avec succès de _offreparcattraction";
                } catch (PDOException $e) {
                    //echo "<br>Error: " . $e->getMessage();
                }
            } else if ($categorie == 'restauration') { //delete de _offrerestaaurant
                $sql = "DELETE FROM public._offrerestaaurant WHERE idoffre = :idoffre";
                try {
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':idoffre', $idOffre);
                    $stmt->execute();
                    //echo "<br>Offre supprimée avec succès de _offrerestaaurant";
                } catch (PDOException $e) {
                    //echo "<br>Error: " . $e->getMessage();
                }
            } else if ($categorie == 'spectacle') { //delete de _offrespectacle
                $sql = "DELETE FROM public._offrespectacle WHERE idoffre = :idoffre";
                try {
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':idoffre', $idOffre);
                    $stmt->execute();
                    //echo "<br>Offre supprimée avec succès de _offrespectacle";
                } catch (PDOException $e) {
                    //echo "<br>Error: " . $e->getMessage();
                }
            } else if ($categorie == 'visite') { //delete de _offrevisite
                $sql = "DELETE FROM public._offrevisite WHERE idoffre = :idoffre";
                try {
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':idoffre', $idOffre);
                    $stmt->execute();
                    //echo "<br>Offre supprimée avec succès de _offrevisite";
                } catch (PDOException $e) {
                    //echo "<br>Error: " . $e->getMessage();
                }

            }

            //delete de _offre
            $sql = "DELETE FROM public._offre WHERE idoffre = :idoffre";
            try {
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':idoffre', $idOffre);
                $stmt->execute();
                //echo "<br>Offre supprimée avec succès de _offre";
            } catch (PDOException $e) {
                //echo "<br>Error: " . $e->getMessage();
            }

        }
        
    ?>

            <main>
                <?php 
                    if ("no error"){ //TODO
                        echo "<h1>OFFRE BIEN SUPPRIMER DE LA BASE DE DONNEE</h1>";
                    }
                ?>
            </main>
    <?php } else { ?>
        <main>
                <?php 
                    if ("no error"){ //TODO
                        echo "<h1>CETTE OFFRE NE VOUS APPARTIENT PAS, VOUS ALLEZ ETRE REDIRIGE</h1>";
                    }
                ?>
        </main>
    <?php } ?>

        <div id="footer"></div>

        <script src="./script.js" ></script>
        <script>
            setTimeout(function() {
                window.location.href = 'index.php'; // Redirection vers la page d'accueil après 3 secondes
            }, 3000); // 3000 millisecondes = 3 secondes
        </script>

    </body>

</html>