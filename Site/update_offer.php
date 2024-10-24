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
        include "./SQL/connection_envdev.php";   
        $idOffre = $_POST['idOffre']; // ID de l'offre à modifier
        $cat = $_POST['categorie'];
        $offerName = $_POST['offerName'];
        $summary = $_POST['summary'];
        $description = $_POST['description'];
        $minPrice = $_POST['min_price'];
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

        // Tags
        $tags = $_POST['tags'];

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
        $result = $result['idadresse'];
        

        if ($result) {
            $idAdresse = $result;
        } else {
            // Insertion de la nouvelle adresse
            $idAdresse = insererAdresse($adNumRue, $supAdresse, $adresse, $adCodePostal, $adVille, $adDepartement, $adPays);
            // On récupère l'ID de l'adresse insérée
            $idAdresse = $conn->lastInsertId();
        }

        // Gestion de l'image
        if (!empty($_FILES['imageOffre']['name'])) {
            $idImageOffre = uploadImage('imageOffre');
            // Insérer l'image dans la table _afficherimageoffre
            $sql = "UPDATE public._afficherimageoffre SET idImage = :idImage WHERE idOffre = :idOffre";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':idImage', $idImageOffre);
            $stmt->bindParam(':idOffre', $idOffre);
            $stmt->execute();
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
        $success = true; // Déclare une variable de succès
        } catch (PDOException $e) {
        echo "Erreur lors de la mise à jour de l'offre : " . $e->getMessage();
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

        foreach ($tags as $key => $value) {
            $idTag = getTagIdByValue("$value");

            //lien des tags à l'offre
            $sql = "INSERT INTO public._theme (idOffre, idTag)";
            $sql .= " VALUES (:idOffre, :idTag)";
            try {
                $stmt = $conn->prepare($sql);
                
                // Lier les paramètres
                //commun
                $stmt->bindParam(':idOffre', $idOffre);
                $stmt->bindParam(':idTag', $idTag);
            
                // Exécuter la requête
                $stmt->execute();
                // echo "<br>Requête lien Offre/Tag bien envoyée";
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }

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
    
            // réussite
            // echo "<br>Adresse bien insérée dans la BDD.";
    
            // Retourner l'ID de l'adresse
            return $idAdresse;
    
        } catch (PDOException $e) {
            // Affichage du message d'erreur
            echo "Erreur lors de l'insertion de l'adresse : " . $e->getMessage();
            return false; // En cas d'erreur, on retourne false
        }
    }

    // Fonction pour uploader une image
    function uploadImage($name) {
        global $conn;

        // Obtenir l'ID d'image le plus élevé
        $sql = "SELECT COALESCE(MAX(idImage), 0) FROM public._image";
        $id = $conn->query($sql);
        $maxId = $id->fetchColumn();
        $idImage = $maxId + 1; // Incrémenter l'ID pour la nouvelle image
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
                    // echo "<br>L'image " . basename($_FILES[$name]['name']) . " a été uploadée.";

                    // Insertion du chemin de l'image dans la base de données
                    $sql = "INSERT INTO public._image (pathImage) VALUES (:pathImage)";
                    try {
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':pathImage', $targetFile);
                        $stmt->execute();
                        // echo "<br>Chemin de l'image enregistré avec succès dans la base de données.";
                    } catch (PDOException $e) {
                        echo "<br>Erreur : " . $e->getMessage();
                    }
                } else {
                    echo "<br>Désolé, une erreur est survenue lors de l'upload de votre image.";
                }
            } else {
                echo "<br>Désolé, votre fichier n'a pas pu être uploadé.";
            }
        } else {
            echo "<br>Aucun fichier sélectionné ou le fichier n'a pas été téléchargé correctement.";
        }

        return $idImage;
    }
        
    function getTagIdByValue($value) {
        $sql_get = "SELECT idTag FROM public._tag WHERE typeTag = :typeTag";
        global $conn;

        try {
            // Préparation de la requête
            $stmt = $conn->prepare($sql_get);
            
            // Liaison du paramètre
            $stmt->bindParam(':typeTag', $value, PDO::PARAM_STR);
            
            // Exécution de la requête
            $stmt->execute();
            
            // Vérification si un résultat a été trouvé
            if ($stmt->rowCount() > 0) {
                // Récupération de l'ID du tag
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result['idtag']; // Retourne l'ID
            } else {
                return null; // Aucun tag trouvé
            }
        } catch (PDOException $e) {
            // Gestion d'erreur
            echo "<br>Erreur lors de la récupération de l'ID du tag : " . $e->getMessage();
            return false; // En cas d'erreur, retourne false
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
                    echo "<h1>VOTRE OFFRE EST BIEN MODIFIEE !</h1>";
                }
            ?>
        </main>

        <div id="footer"></div>

        <script src="./script.js" ></script>

        <script>
            setTimeout(function() {
                window.location.href = 'index.php'; // Redirection vers la page d'accueil après 3 secondes
            }, 3000); // 3000 millisecondes = 3 secondes
        </script>



    </body>

</html>