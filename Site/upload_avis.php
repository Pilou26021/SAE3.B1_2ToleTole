<?php

    ob_start();
    include "header.php";
    include "../SQL/connection_local.php";

    $professionel = false;
    $membre = false;
    if (isset($_SESSION['membre'])) {
        $membre = true;
        $idmembre = $_SESSION['membre'];
    } elseif (isset($_SESSION['professionnel'])) {
        header('Location: index.php');
    } else {
        header('Location: index.php');
    }

    $idoffre = intval($_POST['idoffre']);
    $note = intval($_POST['note']);
    $commentaire = $_POST['commentaire'];
    //idmembre
    $dateavis = date("d-m-Y");
    $datevisiteavis = $_POST['datevisite'];
    $blacklistavis = false;
    $reponsepro = false;


?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
        <title>Upload avis</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <!-- Chargement du header et du footer -->
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

        <div id="header"></div> 

        <main>

            <?php
                //récupérer les avis du membre
                $sql = "SELECT * FROM _avis WHERE idmembre = :idmembre";
                // Lier les paramètres
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':idmembre', $idmembre, PDO::PARAM_INT);
                $stmt->execute();

                //si il à déjà un avis sur cette offre on ne peut pas en ajouter un autre
                if ($avis['idoffre'] == $idoffre) { ?> 
                    <h1>ERREUR: VOUS AVEZ DÉJÀ LAISSÉ UN AVIS POUR CETTE OFFRE.</h1>
                    <?php 
                    //retourner à la page de l'offre après 3 secondes
                    header("refresh:3;url=offre.php?id=$idoffre");
                }

                if ($note <= 0 || $note > 5) { ?>
                    <h1>ERREUR: LA NOTE DOIT ÊTRE COMPRIS ENTRE 1 ET 5.</h1>
                    <?php
                } else {
                    $sql = "INSERT INTO _avis (idoffre, noteavis, commentaireavis, idmembre, dateavis, datevisiteavis, blacklistavis, reponsepro)
                            VALUES (:idoffre, :note, :commentaire, :idmembre, :dateavis, :datevisiteavis, :blacklistavis, :reponsepro)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':idoffre', $idoffre, PDO::PARAM_INT);
                    $stmt->bindParam(':note', $note, PDO::PARAM_INT);
                    $stmt->bindParam(':commentaire', $commentaire, PDO::PARAM_STR);
                    $stmt->bindParam(':idmembre', $idmembre, PDO::PARAM_INT);
                    $stmt->bindParam(':dateavis', $dateavis, PDO::PARAM_STR);
                    $stmt->bindParam(':datevisiteavis', $datevisiteavis, PDO::PARAM_STR);
                    $stmt->bindParam(':blacklistavis', $blacklistavis, PDO::PARAM_BOOL);
                    $stmt->bindParam(':reponsepro', $reponsepro, PDO::PARAM_BOOL);
                    $stmt->execute();
                    
                    if ($result) { ?>
                        <h1>L'AVIS A ÉTÉ AJOUTÉ AVEC SUCCÈS.</h1>
                        <?php header("refresh:3;url=details_offre.php?idoffre=$idoffre");?>
                        <?php
                    } else { ?>
                        <h1>ERREUR: L'AVIS N'A PAS PU ÊTRE AJOUTÉ.</h1>
                        <?php
                    }
                }

            ?>

        </main>

        <div id="footer"></div>
        
    </body>


</html>