<?php
    error_reporting(E_ALL ^ E_WARNING);
    ob_start();
    session_start();

    if (isset($_SESSION['professionnel'])) {
        $professionel = true;
        $idProPropose = $_SESSION['professionnel'];
    } else {
        ?> <script>window.location.replace('index.php');</script> <?php
    }

    if (isset($_POST)) {
        include('../SQL/connection_local.php');
        
        $idOffre = $_POST['idOffre']; // ID de l'offre à modifier
        $cat = $_POST['categorie'];
        $offerName = $_POST['offerName'];
        $summary = $_POST['summary'];
        $description = $_POST['description'];
        $minPrice = $_POST['minPrice'];
        $dateOffre = $_POST['dateOffre'];
        $typeOffre = $_POST['typeOffre'];
        // Si typeOffre est 'Standard' alors typeOffre = 1, si typeOffre est 'Premium' alors typeOffre = 2
        $typeOffre = $typeOffre == 'Standard' ? 1 : 2;

        // Si typeOffre == 2 alors commentaireBlacklistable
        $commentaireBlacklistable = $typeOffre == 2 ? true : false;

        $conditionAccessibilite = $_POST['conditionAccessibilite'];
        $aLaUneOffre = isset($_POST['aLaUneOffre']) ? true : false;
        $enReliefOffre = isset($_POST['enReliefOffre']) ? true : false;
        $website = $_POST['website'];

        // Adresse
        $adNumRue = $_POST['adNumRue'];
        $adresse = $_POST['adresse'];
        $supAdresse = $_POST['supAdresse'];
        $adCodePostal = $_POST['adCodePostal'];
        $adVille = $_POST['adVille'];
        $adDepartement = $_POST['adDepartement'];
        $adPays = $_POST['adPays'];

        // Vérification des doublons d'adresse
        $sql = "SELECT idAdresse FROM public._adresse WHERE numrue = :adNumRue AND adresse = :adresse AND codepostal = :adCodePostal AND ville = :adVille";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':adNumRue', $adNumRue);
        $stmt->bindParam(':adresse', $adresse);
        $stmt->bindParam(':adCodePostal', $adCodePostal);
        $stmt->bindParam(':adVille', $adVille);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $idAdresse = $result['idAdresse'];
        } else {
            // Insertion de la nouvelle adresse
            $idAdresse = insererAdresse($adNumRue, $supAdresse, $adresse, $adCodePostal, $adVille, $adDepartement, $adPays);
            // On récupère l'ID de l'adresse insérée
            $idAdresse = $conn->lastInsertId();
        }

        // Gestion de l'image
        if (!empty($_FILES['imageOffre']['name'])) {
            $idImageOffre = uploadImage('imageOffre');
        } else {
            // Conserver l'ancienne image si aucune nouvelle image n'est envoyée
            $sql = "SELECT idimage FROM public._afficherimageoffre WHERE idOffre = :idOffre";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':idOffre', $idOffre);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $idImageOffre = $result['idImage'];
        }


        // Mise à jour de l'offre
        $sql = "UPDATE public._offre 
                SET idAdresse = :idAdresse, titreOffre = :offerName, resumeOffre = :summary, descriptionOffre = :description, prixMinOffre = :prixMinOffre, aLaUneOffre = :aLaUneOffre, enReliefOffre = :enReliefOffre, typeOffre = :typeOffre, siteWebOffre = :website, conditionAccessibilite = :conditionAccessibilite
                WHERE idOffre = :idOffre";
        try {
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':idAdresse', $idAdresse);
            $stmt->bindParam(':offerName', $offerName);
            $stmt->bindParam(':summary', $summary);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':prixMinOffre', $minPrice);
            $stmt->bindParam(':aLaUneOffre', $aLaUneOffre, PDO::PARAM_BOOL);
            $stmt->bindParam(':enReliefOffre', $enReliefOffre, PDO::PARAM_BOOL);
            $stmt->bindParam(':typeOffre', $typeOffre);
            $stmt->bindParam(':website', $website);
            $stmt->bindParam(':conditionAccessibilite', $conditionAccessibilite);
            $stmt->bindParam(':idOffre', $idOffre);
            
            $stmt->execute();
            echo "Offre modifiée avec succès";
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }

        // Mise à jour des catégories spécifiques (par exemple, si c'est une offre de restauration)
        if ($cat == 'restauration') {
            $idCarteResto = !empty($_FILES['menuImage']['name']) ? uploadImage('menuImage') : NULL;
            $sql = "UPDATE public._offreRestaurant SET horaireSemaine = :horaireSemaine, gammePrix = :gammePrix, carteResto = COALESCE(:carteResto, carteResto) WHERE idOffre = :idOffre";
            $stmt = $conn->prepare($sql);
            $horaireSemaine = json_encode([
                "lunchOpen" => $_POST['lunchOpenTime'],
                "lunchClose" => $_POST['lunchCloseTime'],
                "dinnerOpen" => $_POST['dinnerOpenTime'],
                "dinnerClose" => $_POST['dinnerCloseTime']
            ]);
            $stmt->bindParam(':horaireSemaine', $horaireSemaine);
            $stmt->bindParam(':gammePrix', $_POST['averagePrice']);
            $stmt->bindParam(':carteResto', $idCarteResto);
            $stmt->bindParam(':idOffre', $idOffre);
            $stmt->execute();
        } else if ($cat == 'activite') {
            $sql = "UPDATE public._offreActivite SET indicationDuree = :indicationDuree, ageMinimum = :ageMinimum, prestationIncluse = :prestationIncluse WHERE idOffre = :idOffre";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':indicationDuree', $_POST['indicationDuree']);
            $stmt->bindParam(':ageMinimum', $_POST['ageMinimum']);
            $stmt->bindParam(':prestationIncluse', $_POST['prestationIncluse']);
            $stmt->bindParam(':idOffre', $idOffre);
            $stmt->execute();
        } else if ($cat == 'parc') {
            $idCarteParc = !empty($_FILES['carteParc']['name']) ? uploadImage('carteParc') : NULL;
            $sql = "UPDATE public._offreParcAttraction SET dateOuverture = :dateOuverture, dateFermeture = :dateFermeture, carteParc = COALESCE(:carteParc, carteParc), nbrAttraction = :nbrAttraction, ageMinimum = :ageMinimum WHERE idOffre = :idOffre";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':dateOuverture', $_POST['dateOuverture']);
            $stmt->bindParam(':dateFermeture', $_POST['dateFermeture']);
            $stmt->bindParam(':carteParc', $idCarteParc);
            $stmt->bindParam(':nbrAttraction', $_POST['nbrAttraction']);
            $stmt->bindParam(':ageMinimum', $_POST['ageMinimum']);
            $stmt->bindParam(':idOffre', $idOffre);
            $stmt->execute();
        } else if ($cat == 'spectacle') {
            $sql = "UPDATE public._offreSpectacle SET dateOffre = :dateOffre, indicationDuree = :indicationDuree, capaciteAcceuil = :capaciteAcceuil WHERE idOffre = :idOffre";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':dateOffre', $_POST['dateOffre']);
            $stmt->bindParam(':indicationDuree', $_POST['indicationDuree']);
            $stmt->bindParam(':capaciteAcceuil', $_POST['capaciteAcceuil']);
            $stmt->bindParam(':idOffre', $idOffre);
            $stmt->execute();
        } else if ($cat == 'visite') {
            $sql = "UPDATE public._offreVisite SET dateOffre = :dateOffre, visiteGuidee = :visiteGuidee, langueProposees = :langueProposees WHERE idOffre = :idOffre";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':dateOffre', $_POST['dateOffre']);
            $stmt->bindParam(':visiteGuidee', $_POST['visiteGuidee']);
            $stmt->bindParam(':langueProposees', $_POST['langues']);
            $stmt->bindParam(':idOffre', $idOffre);
            $stmt->execute();
        }

        // Suppression des anciens tags
        $sql = "DELETE FROM public._theme WHERE idOffre = :idOffre";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idOffre', $idOffre);
        $stmt->execute();

        // Mise à jour des tags
        foreach ($tags as $key => $value) {
            $idTag = getTagIdByValue("$value");
            $sql = "INSERT INTO public._theme (idOffre, idTag) VALUES (:idOffre, :idTag)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':idOffre', $idOffre);
            $stmt->bindParam(':idTag', $idTag);
            $stmt->execute();
        }
    }
    function insererAdresse($numRue, $supplementAdresse, $adresse, $codePostal, $ville, $departement, $pays) {
        global $conn;

        try {
            // Préparer la requête d'insertion
            $sql = "INSERT INTO public._adresse (numRue, supplementAdresse, adresse, codePostal, ville, departement, pays) 
                    VALUES (:numRue, :supplementAdresse, :adresse, :codePostal, :ville, :departement, :pays)";
            
            // Préparation de la requête
            $stmt = $conn->prepare($sql);
            
            // Liaison des paramètres aux valeurs
            $stmt->bindParam(':numRue', $numRue);
            $stmt->bindParam(':supplementAdresse', $supplementAdresse);
            $stmt->bindParam(':adresse', $adresse);
            $stmt->bindParam(':codePostal', $codePostal);
            $stmt->bindParam(':ville', $ville);
            $stmt->bindParam(':departement', $departement);
            $stmt->bindParam(':pays', $pays);
            
            // Exécuter la requête
            $stmt->execute();
            
            // Récupérer l'ID de l'adresse insérée
            $idAdresse = $conn->lastInsertId();
    
            //réussite
            echo "<br>Adresse bien insérer dans la bdd.";

            // Retourner l'ID de l'adresse
            return $idAdresse;
    
        } catch (PDOException $e) {
            // Affichage du message d'erreur
            echo "Erreur lors de l'insertion de l'adresse : " . $e->getMessage();
            return false; // En cas d'erreur, on retourne false
        }
    }    
?>



<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="./style.css">
        <title>Modifier Offre</title>
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
            <?php 
                if ("no error"){ //TODO
                    echo "<h1>VOTRE EST BIEN MODIFIEE !</h1>";
                }
            ?>
        </main>

        <div id="footer"></div>

        <script src="./script.js" ></script>

        <script>
            setTimeout(function() {
                window.location.href = 'index.php'; // Redirection vers la page d'accueil après 3 secondes
            }, 3000000); // 3000 millisecondes = 3 secondes
        </script>



    </body>

</html>