<?php
    session_start();
    if (isset($_POST)){

        //categorie
        $cat = $_POST['categorie'];
        $idProPropose = 1; //val par defaut pour test

        //commun
        $typeOffre = $_POST['typeOffre'];
        if($typeOffre == 'Standard'){ $typeOffre = 1; } //on assigne la valeur à envoyer à la BDD (int)
        else if ($typeOffre == 'Premium') { $typeOffre = 2; }
        else { $typeOffre = 0; }
        $commBlacklistables = false;
        if ($typeOffre == 2){ $commBlacklistables = true; } //si offre en premium les comms sont blacklistables
        $horsLigne = false; //par défaut l'offre est en ligne

        $offerName = $_POST['offerName']; //déjà une String
        $summary = $_POST['summary']; //déjà une String
        $description = $_POST['description']; //déjà une String

        $adultPrice = $_POST['adultPrice'];
        $childPrice = $_POST['childPrice'];
        $aLaUneOffre = $_POST['aLaUneOffre'];
        $enReliefOffre = $_POST['enReliefOffre'];
        $website = $_POST['website'];
        $adress = $_POST['adress'];

        //existe pas pour toute offre :
        $dateOuverture = $_POST['dateOuverture']; //time
        $dateFermeture = $_POST['dateFermeture'];
        $carteParc  = $_POST['carteParc'];
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
        //horairesSemaine
        $lunchOpenTime = $_POST['lunchOpenTime']; //time
        $lunchCloseTime = $_POST['lunchCloseTime']; //time
        $dinnerOpenTime = $_POST['dinnerOpenTime']; //time
        $dinnerCloseTime = $_POST['dinnerCloseTime']; //time
        $horairesSemaine = "{1:$lunchOpenTime, 2:$lunchCloseTime, 3:$dinnerOpenTime, 4:$dinnerCloseTime}";

        $closedDays = $_POST['closedDays'];
        $averagePrice = $_POST['averagePrice'];
        $menuImage = $_POST['menuImage'];

        $tags = $_POST['tags']; //Arraylist

        // include '../SQL/connection_local.php';

        if ($cat == 'restauration') {
            $sql = "INSERT INTO restaurant (idProPropose, idAdresse, titreOffre, resumeOffre, descriptionOffre, prixMinOffre, aLaUneOffre, enReliefOffre, typeOffre, noteMoyenneOffre, commentairesBlacklistable, siteWebOffre, dateCreationOffre, conditionAccessibilite, horsLigne, horairesSemaine, joursFermeture, prixMoyen, imageMenu, tags) ";
            $sql .= "VALUES (:idProPropose, :idAdresse, :offerName, :summary, :description, :adultPrice, :aLaUneOffre, :enReliefOffre, :typeOffre, :noteMoyenneOffre, :commBlacklistables, :website, NOW(), :conditionAccessibilite, :horsLigne, :horairesSemaine, :closedDays, :averagePrice, :menuImage, :tags)";
            
            try {
                $stmt = $conn->prepare($sql);
                
                // Lier les paramètres
                $stmt->bindParam(':idProPropose', $idProPropose);
                $stmt->bindParam(':idAdresse', $idAdresse); // Assurez-vous de définir $idAdresse
                $stmt->bindParam(':offerName', $offerName);
                $stmt->bindParam(':summary', $summary);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':adultPrice', $adultPrice);
                $stmt->bindParam(':aLaUneOffre', $aLaUneOffre);
                $stmt->bindParam(':enReliefOffre', $enReliefOffre);
                $stmt->bindParam(':typeOffre', $typeOffre);
                $stmt->bindParam(':noteMoyenneOffre', $noteMoyenneOffre); // Assurez-vous de définir $noteMoyenneOffre
                $stmt->bindParam(':commBlacklistables', $commBlacklistables, PDO::PARAM_BOOL);
                $stmt->bindParam(':website', $website);
                $stmt->bindParam(':conditionAccessibilite', $conditionAccessibilite); // Assurez-vous de définir $conditionAccessibilite
                $stmt->bindParam(':horsLigne', $horsLigne, PDO::PARAM_BOOL);
                $stmt->bindParam(':horairesSemaine', $horairesSemaine);
                $stmt->bindParam(':closedDays', $closedDays);
                $stmt->bindParam(':averagePrice', $averagePrice);
                $stmt->bindParam(':menuImage', $menuImage);
                $stmt->bindParam(':tags', $tags);
            
                // Exécuter la requête
                $stmt->execute();
                echo "New record created successfully";
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        
        }

        if ($cat == 'visite'){

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