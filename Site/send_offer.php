<?php
    error_reporting(E_ALL ^ E_WARNING);
    
    session_start();
    
    if (isset($_POST)){

        include('../SQL/connection_local.php');       
        
        // Catégorie
        $cat = $_POST['categorie'];
        // $idProPropose = $_SESSION['professionnel']; //Récupération de l'id du Pro dans la sessions
        $idProPropose = 1; //défaut
        $noteMoyenneOffre = 0; // Valeur par défaut pour la note moyenne de l'offre
        $imageOffre = "imageOffre"; // Nom de l'image de l'offre

        // Informations communes
        $offerName = $_POST['offerName'];
        $summary = $_POST['summary'];
        $description = $_POST['description'];
        $minPrice = $_POST['minPrice'];
        $adultPrice = $_POST['adultPrice'];
        $childPrice = $_POST['childPrice'];
        $dateOffre = $_POST['dateOffre'];
        $typeOffre = $_POST['typeOffre'];
        $conditionAccessibilite = $_POST['conditionAccessibilite'];

        //par défaut les commentaires ne sont pas blacklistables
        $commBlacklistables = false;
        // Détermination du type d'offre (1: Standard, 2: Premium, 0: Normal)
        switch ($typeOffre) {
            case 'Standard':
                $typeOffre = 1;
                break;
            case 'Premium':
                $typeOffre = 2;
                $commBlacklistables = true; // Les commentaires sont blacklistables en Premium
                break;
            default:
                $typeOffre = 0;
        }

        // Accessibilité et statut
        $horsLigne = false; // Par défaut, l'offre est en ligne

        // Options supplémentaires
        $aLaUneOffre = isset($_POST['aLaUneOffre']) ? true : false;
        $enReliefOffre = isset($_POST['enReliefOffre']) ? true : false;

        // Site web
        $website = $_POST['website'];

        //Adresse
        $adNumRue = $_POST['adNumRue'];
        $adresse = $_POST['adresse'];
        $supAdresse = $_POST['supAdresse'];
        $adCodePostal = $_POST['adCodePostal'];
        $adVille = $_POST['adVille'];
        $adDepartement = $_POST['adDepartement'];
        $adPays = $_POST['adPays'];

        // Informations parcs
        $dateOuverture = $_POST['dateOuverture'];
        $dateFermeture = $_POST['dateFermeture'];
        $nbrAttraction = $_POST['nbrAttraction'];
        $carteParc = "carteParc";

        // Visite
        $visiteGuidee = ($_POST['visiteGuidee'] == "oui") ? true : false;

        // Traitement des langues (ArrayList)
        $langues = $_POST['langues'];
        $autreLangue = $_POST['autreLangue'];
        $string_langues = "";

        if (!empty($langues)) {
            $string_langues = implode(", ", $langues);
            
            // Gestion de l'option "Autre"
            if (in_array("Autre", $langues)) {
                $string_langues = str_replace(", Autre", "", $string_langues);
                $string_langues .= ", Autres langues : " . $autreLangue;
            }
        }

        // Informations sur les horaires
        $day = $_POST['day'];
        $openTime = $_POST['openTime'];
        $closeTime = $_POST['closeTime'];
        $indicationDuree = $_POST['indicationDuree'];
        $ageMinimum = $_POST['ageMinimum'];
        $prestationIncluse = $_POST['prestationIncluse'];

        // Images supplémentaires
        $carteResto = "menuImage";

        // Horaires de la semaine
        $lunchOpenTime = $_POST['lunchOpenTime'];
        $lunchCloseTime = $_POST['lunchCloseTime'];
        $dinnerOpenTime = $_POST['dinnerOpenTime'];
        $dinnerCloseTime = $_POST['dinnerCloseTime'];
        $horaireSemaine = json_encode([
            "lunchOpen" => $lunchOpenTime,
            "lunchClose" => $lunchCloseTime,
            "dinnerOpen" => $dinnerOpenTime,
            "dinnerClose" => $dinnerCloseTime
        ]);

        $closedDays = $_POST['closedDays'];

        // Détermination de la gamme de prix (0: <25, 1: 25-40, 2: >40)
        $gammePrix = intval($_POST['averagePrice']);

        if ($gammePrix < 25) {
            $gammePrix = 0;
        } elseif ($gammePrix <= 40) {
            $gammePrix = 1;
        } else {
            $gammePrix = 2;
        }

        // Spectacle
        $capaciteAcceuil = intval($_POST['capaciteAcceuil']);

        // Tags (ArrayList)
        $tags = $_POST['tags'];

        $idAdresse = insererAdresse($adNumRue, $supAdresse, $adresse, $adCodePostal, $adVille, $adDepartement, $adPays);

        $sql = "INSERT INTO public._offre (idProPropose, idAdresse, titreOffre, resumeOffre, descriptionOffre, prixMinOffre, aLaUneOffre, enReliefOffre, typeOffre, siteWebOffre, noteMoyenneOffre, commentaireBlacklistable, dateCreationOffre, conditionAccessibilite, horsLigne)";
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
            $stmt->bindParam(':aLaUneOffre', $aLaUneOffre, PDO::PARAM_BOOL);
            $stmt->bindParam(':enReliefOffre', $enReliefOffre, PDO::PARAM_BOOL);
            $stmt->bindParam(':typeOffre', $typeOffre);
            $stmt->bindParam(':website', $website);
            $stmt->bindParam(':noteMoyenneOffre', $noteMoyenneOffre);
            $stmt->bindParam(':commBlacklistables', $commBlacklistables, PDO::PARAM_BOOL);
            //conditions d'accessibilité NOW()
            $stmt->bindParam(':conditionAccessibilite', $conditionAccessibilite);
            $stmt->bindParam(':horsLigne', $horsLigne, PDO::PARAM_BOOL);
        
            // Exécuter la requête
            $stmt->execute();
            echo "<br>Requête Offre bien envoyée";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }

        $idOffre = $conn->lastInsertId();
        $idImageOffre = uploadimage($imageOffre);

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
                echo "<br>Requête lien Offre/Tag bien envoyée";
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }

        }   
        
        if ($cat == 'restauration') { //requête si l'offre est de restauration

            $idCarteResto = uploadImage($carteResto);

            try {
                $sql = "INSERT INTO public._offreRestaurant (idOffre, horaireSemaine, gammePrix, carteResto)";
                $sql .= " VALUES (:idOffre, :horaireSemaine, :gammePrix, :carteResto)";
                
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':idOffre', $idOffre);
                $stmt->bindParam(':horaireSemaine', $horaireSemaine);
                $stmt->bindParam(':gammePrix', $gammePrix);
                $stmt->bindParam(':carteResto', $idCarteResto);

                // Exécuter la requête
                $stmt->execute();
                echo "<br>Requête restauration bien envoyée";
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
            
        } else if ($cat == 'spectacle'){ //requête si l'offre est un spectacle
            try {
                $sql = "INSERT INTO public._offreSpectacle (idOffre, dateOffre, indicationDuree, capaciteAcceuil)";
                $sql .= " VALUES (:idOffre, :dateOffre, :indicationDuree, :capaciteAcceuil)";
                
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':idOffre', $idOffre);
                $stmt->bindParam(':dateOffre', $dateOffre);
                $stmt->bindParam(':indicationDuree', $indicationDuree);
                $stmt->bindParam(':capaciteAcceuil', $capaciteAcceuil);

                // Exécuter la requête
                $stmt->execute();
                echo "<br>Requête spectacle bien envoyée";
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }

        } else if ($cat == 'visite'){ //requête si l'offre est une visite
            try {
                $sql = "INSERT INTO public._offreVisite (idOffre, dateOffre, visiteGuidee, langueProposees)";
                $sql .= " VALUES (:idOffre, :dateOffre, :visiteGuidee, :langueProposees)";
                
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':idOffre', $idOffre);
                $stmt->bindParam(':dateOffre', $dateOffre);
                $stmt->bindParam(':visiteGuidee', $visiteGuidee);
                $stmt->bindParam(':langueProposees', $string_langues);

                // Exécuter la requête
                $stmt->execute();
                echo "<br>Requête visite bien envoyée";
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }

        } else if ($cat == 'activite'){
            try {
                $sql = "INSERT INTO public._offreActivite (idOffre, indicationDuree, ageMinimum, prestationIncluse)";
                $sql .= " VALUES (:idOffre, :indicationDuree, :ageMinimum, :prestationIncluse)";
                
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':idOffre', $idOffre);
                $stmt->bindParam(':indicationDuree', $indicationDuree);
                $stmt->bindParam(':ageMinimum', $ageMinimum);
                $stmt->bindParam(':prestationIncluse', $prestationIncluse);

                // Exécuter la requête
                $stmt->execute();
                echo "<br>Requête visite bien envoyée";
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }

        } else if ($cat == 'parc'){

            $idCarteParc = uploadImage($carteParc);

            try {
                $sql = "INSERT INTO public._offreParcAttraction (idOffre, dateOuverture, dateFermeture, carteParc, nbrAttraction, ageMinimum)";
                $sql .= " VALUES (:idOffre, :dateOuverture, :dateFermeture, :carteParc, :nbrAttraction, :ageMinimum)";
                
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':idOffre', $idOffre);
                $stmt->bindParam(':dateOuverture', $dateOuverture);
                $stmt->bindParam(':dateFermeture', $dateFermeture);
                $stmt->bindParam(':carteParc', $idCarteParc);
                $stmt->bindParam(':nbrAttraction', $nbrAttraction);
                $stmt->bindParam(':ageMinimum', $ageMinimum);

                // Exécuter la requête
                $stmt->execute();
                echo "<br>Requête visite bien envoyée";
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }

        }

        //lien entre l'id de l'image de l'offre et l'id de l'offre
        try {
            $sql = "INSERT INTO public._afficherImageOffre (idImage, idOffre) VALUES (:idImage, :idOffre)";
            
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':idImage', $idImageOffre);
            $stmt->bindParam(':idOffre', $idOffre);
            // Exécuter la requête
            $stmt->execute();
            echo "<br>Requête lien Image Offre bien envoyée";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
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

    // Fonction pour uploader une image
    function uploadImage($name) {
        global $conn;

        // Obtenir le prochain ID d'image en fonction du nombre d'images dans la base de données
        $sql = "SELECT COUNT(*) FROM public._image";
        $id = $conn->query($sql);
        $count = $id->fetchColumn() + 1; // Incrémenter le count pour générer un nouvel ID
        $idImage = $count;
        $nom_image = "image" . strval($count);

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
                    echo "<br>L'image " . basename($_FILES[$name]['name']) . " a été uploadée.";

                    // Insertion du chemin de l'image dans la base de données
                    $sql = "INSERT INTO public._image (pathImage) VALUES (:pathImage)";
                    try {
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':pathImage', $targetFile);
                        $stmt->execute();
                        echo "<br>Chemin de l'image enregistré avec succès dans la base de données.";
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
            $("#header").load("./header.php"); 
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