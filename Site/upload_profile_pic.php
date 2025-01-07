<?php

    if(empty($_FILES['newProfileImage']['name'])){
        ?><script>window.location.replace('index.php');</script><?php
    }

    shell_exec('chown -R www-data:www-data ./Site/img/uploaded');
    shell_exec ('chmod 755 -R ./Site/img');
    ob_start();
    include "header.php";
    include "../SQL/connection_local.php";

    $professionel = false;
    $membre = false;
    if (isset($_SESSION['membre'])) {
        $membre = true;
        $idcompte = $_SESSION['membre'];
    } elseif (isset($_SESSION['professionnel'])) {
        $professionel = true;
        $idcompte = $_SESSION['professionnel'];
    }

    //Requête pour récuperer l'image de base de l'utilisateur et la supprimer
    $sql = "SELECT idimagepdp
            FROM public._compte 
            WHERE idcompte = :idcompte";

    // Préparer et exécuter la requête
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':idcompte', $idcompte, PDO::PARAM_INT);
    $stmt->execute();

    // Récupérer les détails de l'offre
    $res = $stmt->fetch();

    $idimagepdp = $res['idimagepdp'];

    // upload de l'image
    $idnewimage = uploadImage('newProfileImage');
    var_dump($idnewimage);

    //Requête pour update la pdp dans la bdd
    $sql = "UPDATE _compte SET idimagepdp = :idnewimage WHERE idcompte = :idcompte";
    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idnewimage', $idnewimage);
        $stmt->bindParam(':idcompte', $idcompte);
        $stmt->execute();
        echo "<br>Image mise à jour pour l'utilisateur";
    } catch (PDOException $e) {
        echo "<br>Error: " . $e->getMessage();
    }

    //Requête pour supprimer l'ancienne image dans la bdd
    $sql = "DELETE FROM public._image WHERE idimage = :idimage";
    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idimage', $idimagepdp);
        $stmt->execute();
        echo "<br>Ancienne image supprimée de la bdd";
    } catch (PDOException $e) {
        echo "<br>Error: " . $e->getMessage();
    }
    
    unlink("./img/uploaded/image" . strval($idimagepdp) . ".png");

?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="./style.css">
        <title>Envoie de la photo de profil</title>
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

        <main>

            <h1>Photo de profil mise à jour</h1>
            <p>Redirection vers la page d'accueil...</p>
            <?php header("Location: mon_compte.php"); ?>

        </main>

        <div id="footer"></div>
        <script src="script.js"></script> 
    </body>
</html>

<?php 
    // Fonction pour uploader une image
    function uploadImage($name) {
        global $conn;

        // Obtenir l'ID d'image le plus élevé grâce à la séquence d'incrémentation
        try {

            $sql = "SELECT last_value FROM public._image_idimage_seq";
            // Récupération de la dernière valeur de la séquence
            $stmt = $conn->query($sql);
            $last_value = $stmt->fetchColumn();
            
        } catch (PDOException $e) {
            // Gestion d'erreur
            //echo "<brErreur lors de la récupération de l'incrément : " . $e->getMessage();
        }
        
        $idImage = $last_value + 1; // Incrémenter l'ID pour la nouvelle image
        $nom_image = "image" . strval($idImage);

        // Dossier où les images seront stockées
        $targetDir = "./img/uploaded/";
        $targetFile = $targetDir . basename($nom_image)  . ".png";
        $uploadOk = 1;

        // Vérification de l'existence du fichier
        if (isset($_FILES[$name]) && $_FILES[$name]['tmp_name'] !== '') {
            // Vérifie si le fichier est une image réelle
            $check = getimagesize($_FILES[$name]['tmp_name']);
            if ($check !== false) {
                $uploadOk = 1;
            } else {
                echo "Le fichier n'est pas une image.";
                $uploadOk = 0;
            }

            // Si le fichier est valide, essaye de l'uploader
            if ($uploadOk == 1) {

                if (move_uploaded_file($_FILES[$name]['tmp_name'], $targetFile)) {
                    //echo "<brL'image " . basename($_FILES[$name]['name']) . " a été uploadée.";

                    // Insertion du chemin de l'image dans la base de données
                    $sql = "INSERT INTO public._image (pathImage) VALUES (:pathImage)";
                    try {
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':pathImage', $targetFile);
                        $stmt->execute();
                        //echo "<brChemin de l'image enregistré avec succès dans la base de données.";
                    } catch (PDOException $e) {
                        //echo "<brErreur : " . $e->getMessage();
                    }
                } else {
                    //echo "<brDésolé, une erreur est survenue lors de l'upload de votre image.";
                }
            } else {
                //echo "<brDésolé, votre fichier n'a pas pu être uploadé.";
            }
        } else {
            //echo "<brAucun fichier sélectionné ou le fichier n'a pas été téléchargé correctement.";
        }

        return $idImage;
    }
?>