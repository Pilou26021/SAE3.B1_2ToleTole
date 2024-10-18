<?php
    session_start();
    if (isset($_POST)){
        //commun
        $typeOffre = $_POST['typeOffre'];

        $offerName = $_POST['offerName'];
        $summary = $_POST['summary'];
        $description = $_POST['description'];
        $adultPrice = $_POST['adultPrice'];
        $childPrice = $_POST['childPrice'];
        $aLaUneOffre = $_POST['aLaUneOffre'];
        $enReliefOffre = $_POST['enReliefOffre'];
        $website = $_POST['website'];
        $adress = $_POST['adress'];

        //existe pas pour toute offre :
        $dateOuverture = $_POST['dateOuverture'];
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

        $lunchOpenTime = $_POST['lunchOpenTime']; //time
        $lunchCloseTime = $_POST['lunchCloseTime']; //time
        $dinnerOpenTime = $_POST['dinnerOpenTime']; //time
        $dinnerCloseTime = $_POST['dinnerCloseTime']; //time

        $closedDays = $_POST['closedDays'];
        $averagePrice = $_POST['averagePrice'];
        $menuImage = $_POST['menuImage'];
        $tags = $_POST['tags']; //Arraylist

        print_r($_POST['categorie']);

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