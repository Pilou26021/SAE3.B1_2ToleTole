<?php
    session_start();
    if (isset($_POST)){

        include('../SQL/connection_local.php');
        
        
        //categorie
        $cat = $_POST['categorie'];
        $idProPropose = 1; //val par defaut pour test
        $idAdresse = 1; //val par defaut pour test
        $noteMoyenneOffre = 0; //val par defaut pour noteMoyenneOffre

        //commun
        $typeOffre = $_POST['typeOffre'];
        if($typeOffre == 'Standard'){ $typeOffre = 1; } //on assigne la valeur à envoyer à la BDD (int)
        else if ($typeOffre == 'Premium') { $typeOffre = 2; }
        else { $typeOffre = 0; }
        $commBlacklistables = false;
        if ($typeOffre == 2){ $commBlacklistables = true; } //si offre en premium les comms sont blacklistables

        $conditionAccessibilite = $_POST['conditionAccessibilite'];
        $horsLigne = false; //par défaut l'offre est en ligne

        $offerName = $_POST['offerName']; //déjà une String
        $summary = $_POST['summary']; //déjà une String
        $description = $_POST['description']; //déjà une String
        $minPrice = $_POST['minPrice'];

        $adultPrice = $_POST['adultPrice'];
        $childPrice = $_POST['childPrice'];
        $aLaUneOffre = $_POST['aLaUneOffre'];
        $enReliefOffre = $_POST['enReliefOffre'];
        $website = $_POST['website'];
        $adress = $_POST['adress'];

        //existe pas pour toute offre :
        $dateOuverture = $_POST['dateOuverture']; //time
        $dateFermeture = $_POST['dateFermeture'];
        $carteParc  = "carteParc";
        $nbrAttractions = $_POST['nbrAttrations'];

        $visiteGuidee = $_POST['visiteGuidee']; //bool
        $langues = $_POST['langues'];
        $autreLangue = $_POST['autreLangue']; //Arraylist
        $day = $_POST['day'];
        $openTime = $_POST['openTime'];
        $closeTime = $_POST['closeTime'];
        $indicationDuree = $_POST['indicationDuree'];
        $ageMinimum = $_POST['ageMinimum'];
        $prestationIncluse = $_POST['prestationIncluse']; //text

        //restauration
        $carteResto = "menuImage";
        //horairesSemaine
        $lunchOpenTime = $_POST['lunchOpenTime']; //time
        $lunchCloseTime = $_POST['lunchCloseTime']; //time
        $dinnerOpenTime = $_POST['dinnerOpenTime']; //time
        $dinnerCloseTime = $_POST['dinnerCloseTime']; //time
        $horairesSemaine = "{1:$lunchOpenTime, 2:$lunchCloseTime, 3:$dinnerOpenTime, 4:$dinnerCloseTime}";
        $closedDays = $_POST['closedDays'];

        $gammePrix = intval($_POST['averagePrice']);
        if ($gammePrix < 25 ){ $gammePrix = 0; } //changer par la valeur à entrer dans la BDD
        else if ($gammePrix >= 25 || $gammePrix <= 40) { $gammePrix = 1; }
        else if ($gammePrix > 40) { $gammePrix = 2; }
        else { $gammePrix = 2; }

        $tags = $_POST['tags']; //Arraylist

        $sql = "INSERT INTO sae._offre (idProPropose, idAdresse, titreOffre, resumeOffre, descriptionOffre, prixMinOffre, aLaUneOffre, enReliefOffre, typeOffre, siteWebOffre, noteMoyenneOffre, commentaireBlacklistable, dateCreationOffre, conditionAccessibilite, horsLigne)";
        $sql .= " VALUES (:idProPropose, :idAdresse, :offerName, :summary, :description, :prixMinOffre, :aLaUneOffre, :enReliefOffre, :typeOffre, :website, :noteMoyenneOffre, :commBlacklistables, NOW(), :conditionAccessibilite, :horsLigne)";
        try {
            $stmt = $conn->prepare($sql);
            
            // Lier les paramètres
            //commun
            $stmt->bindParam(':idProPropose', $idProPropose);
            $stmt->bindParam(':idAdresse', $idAdresse);
            $stmt->bindParam(':offerName', $offerName);
            $stmt->bindParam(':summary', $summary);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':prixMinOffre', $minPrice);
            $stmt->bindParam(':aLaUneOffre', $aLaUneOffre);
            $stmt->bindParam(':enReliefOffre', $enReliefOffre);
            $stmt->bindParam(':typeOffre', $typeOffre);
            $stmt->bindParam(':website', $website);
            $stmt->bindParam(':noteMoyenneOffre', $noteMoyenneOffre);
            $stmt->bindParam(':commBlacklistables', $commBlacklistables, PDO::PARAM_BOOL);
            //conditions d'accessibilité NOW()
            $stmt->bindParam(':conditionAccessibilite', $conditionAccessibilite);
            $stmt->bindParam(':horsLigne', $horsLigne, PDO::PARAM_BOOL);
        
            // Exécuter la requête
            $stmt->execute();
            echo "Requête bien envoyée";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }

        $idOffre = $conn->lastInsertId();

        uploadImage($carteResto);
        $idCarteResto = $conn->lastInsertId();
        
        if ($cat == 'restauration') {
            try {
                $sql = "INSERT INTO sae._offrerestaurant (idOffre, horaireSemaine, gammePrix, carteResto)";
                $sql .= " VALUES (:idOffre, :horaireSemaine, :gammePrix, :carteResto)";
                
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':idOffre', $idOffre);
                $stmt->bindParam(':horaireSemaine', $horaireSemaine);
                $stmt->bindParam(':gammePrix', $gammePrix);
                $stmt->bindParam(':carteResto', $idCarteResto);

                // Exécuter la requête
                $stmt->execute();
                echo "Requête bien envoyée";
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
            
        }

    }


    function uploadImage($name){
        // Dossier où les images seront stockées
        $targetDir = "./img/uploaded/";
        $targetFile = $targetDir . basename(date("Y-m-d H:i:s"));
        $uploadOk = 1;

        // Vérifie si le fichier est une image réelle
        $check = getimagesize($_FILES["$name"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            echo "Le fichier n'est pas une image.";
            $uploadOk = 0;
        }

        // Si le fichier est valide, essaye de l'uploader
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["$name"]["tmp_name"], $targetFile)) {
                echo "L'image " . basename($_FILES["$name"]["name"]) . " a été uploadée.";

                // Connexion à la base de données et insertion du chemin de l'image
                $sql = "INSERT INTO sae._image (pathImage) VALUES (:pathImage)";

                try {
                    global $conn;
                    $stmt = $conn->prepare($sql); 
                    // Lier le paramètre :pathImage
                    $stmt->bindParam(':pathImage', $targetFile);
                    // Exécuter la requête
                    $stmt->execute();
                    echo "Chemin de l'image enregistré avec succès dans la base de données.";
                } catch (PDOException $e) {
                    echo "Erreur : " . $e->getMessage();
                }

            } else {
                echo "Désolé, une erreur est survenue lors de l'upload de votre image.";
            }
        } else {
            echo "Désolé, votre fichier n'a pas pu être uploadé.";
        }
    }
?>


<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="./style.css">
        <title>Creer Offres</title>
    </head>
    <body>
        
        <script
            src="https://code.jquery.com/jquery-3.3.1.js"
            integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
            crossorigin="anonymous">
        </script>
        <script> 
            $(function(){
            $("#header").load("./header.html"); 
            $("#footer").load("footer.html"); 
            });
        </script> 

        <div id="header"></div>

        <main>
            <?php 
                if ("no error"){ //TODO
                    echo "<h1>OFFRE BIEN AJOUTER A LA BASE DE DONNEE</h1>";
                }
            ?>
        </main>

        <div id="footer"></div>

        <script src="./script.js" ></script>


    </body>

</html>