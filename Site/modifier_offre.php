<?php
    error_reporting(E_ALL ^ E_WARNING);
    ob_start();
    session_start();

    // Inclusion du fichier de connexion à la base de données
    include('../SQL/connection_local.php');

    // Vérification si l'utilisateur est connecté et que l'offre lui appartient
    if (!isset($_SESSION['professionnel'])) {
        echo "<script>window.location.replace('index.php');</script>";
        exit();
    }

    // Récupération de l'ID de l'offre à modifier depuis l'URL
    if (isset($_GET['idoffre'])) {
        $idOffre = $_GET['idoffre'];

        // Récupération des détails de l'offre
        $stmt = $conn->prepare("SELECT * FROM public._offre WHERE idOffre = :idOffre");
        $stmt->execute([':idOffre' => $idOffre]);
        $offre = $stmt->fetch();

        if (!$offre || $offre['idpropropose'] !== $_SESSION['professionnel']) {
            echo "<script>window.location.replace('index.php');</script>";
            exit();
        }

        // Récupération des détails dans sa catégorie
        // Appelle de la fonction trouver_categorie_offre dans la base de données
        $stmt = $conn->prepare("SELECT public.trouver_categorie_offre(:idOffre)");
        $stmt->execute([':idOffre' => (int)$idOffre]);  // Assurez-vous que $idOffre est un entier
        $categorie = $stmt->fetchColumn();        
        
        // catégorie de l'offre varie entre 1 et 5
        /*
            -- _offreactivite = 1
            -- _offreparcattraction = 2
            -- _offrerestaurant    = 3
            -- _offrespectacle = 4
            -- _offrevisite    = 5
        */
        switch ($categorie) {
            case 1:
                $stmt = $conn->prepare("SELECT * FROM public._offreactivite WHERE idOffre = :idOffre");
                $stmt->execute([':idOffre' => $idOffre]);
                $offreDetails = $stmt->fetch();
                $cat = 'activite';
                break;
            case 2:
                $stmt = $conn->prepare("SELECT * FROM public._offreparcattraction WHERE idOffre = :idOffre");
                $stmt->execute([':idOffre' => $idOffre]);
                $offreDetails = $stmt->fetch();
                $cat = 'parc';
                break;
            case 3:
                $stmt = $conn->prepare("SELECT * FROM public._offrerestaurant WHERE idOffre = :idOffre");
                $stmt->execute([':idOffre' => $idOffre]);
                $offreDetails = $stmt->fetch();
                $cat = 'restauration';
                break;
            case 4:
                $stmt = $conn->prepare("SELECT * FROM public._offrespectacle WHERE idOffre = :idOffre");
                $stmt->execute([':idOffre' => $idOffre]);
                $offreDetails = $stmt->fetch();
                $cat = 'spectacle';
                break;
            case 5:
                $stmt = $conn->prepare("SELECT * FROM public._offrevisite WHERE idOffre = :idOffre");
                $stmt->execute([':idOffre' => $idOffre]);
                $offreDetails = $stmt->fetch();
                $cat = 'visite';
                break;
            default:
                $offreDetails = null;
                echo "<script>window.location.replace('index.php');</script>";
                break;
        }

        $stmt = $conn->prepare("SELECT * FROM public._adresse WHERE idAdresse = :idAdresse");
        $stmt->execute([':idAdresse' => $offre['idadresse']]);
        $adresse = $stmt->fetch();

    } else {
        echo "<script>window.location.replace('index.php');</script>";
        exit();
    }

    // Mise à jour de l'offre
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    }
?>

 <!-- on pré-remplit les champs avec les valeurs actuelles de l'offre -->
            <!-- si offre standard on propose de pouvoir changer vers l'offre premium  mais pas l'inverse -->
            <?php 
                // Parcourir les offres
                echo "<h2>Détails de l'offre</h2>";
                echo "<ul>";
                foreach ($offre as $key => $value) {
                    echo "<li><strong>$key</strong>: $value</li>";
                }
                echo "</ul>";

                // Parcourir les détails de l'offre
                echo "<h2>Détails supplémentaires de l'offre</h2>";
                echo "<ul>";
                foreach ($offreDetails as $key => $value) {
                    echo "<li><strong>$key</strong>: $value</li>";
                }
                echo "</ul>";

                // Parcourir les détails de l'adresse
                echo "<h2>Adresse</h2>";
                echo "<ul>";
                foreach ($adresse as $key => $value) {
                    echo "<li><strong>$key</strong>: $value</li>";
                }
                echo "</ul>";
            ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
    <title>Modifier l'offre</title>
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
    <main class="creer-offre-main">
        <h1>Modifier l'offre</h1>
        <form method="POST">
            <h2>Type d'Offre</h2>
            <?php if ($offre['typeoffre'] === 1): ?>
                <label>
                    <input class="visite" type="radio" name="typeOffre" value="Standard" checked required> Standard (10€)
                </label>
                <label>
                    <input class="visite" type="radio" name="typeOffre" value="Premium" required> Premium (25€)
                </label>
            <?php else: ?>
                <label>
                    <input class="visite" type="radio" name="typeOffre" value="Premium" checked required> Premium (25€)
                </label>
            <?php endif; ?>

            <h2>Nom de l'offre</h2>
            <input class="zone-text" type="text" name="offerName" value="<?= $offre['titreoffre'] ?>" required>

            <h2>Résumé</h2>
            <input type="text" class="textarea-creer_offre" name="summary" rows="2" value="<?= $offre['resumeoffre'] ?>" required>

            <h2>Description détaillée</h2>
            <textarea class="textarea-creer_offre" name="description" rows="4" required><?= $offre['descriptionoffre'] ?></textarea>

            <h2>Prix minimun de l'offre</h2>
            <input id="min_price" class="zone-number" type="number" name="min_price" placeholder="Prix Minimum" value="<?= htmlspecialchars($offre['prixminoffre']) ?>" required oninput="checkNegativeValue(this)" onkeypress="preventInvalidChars(event)">
            <p id="error-min_price" style="color:red; display:none;">Veuillez entrer une valeur positive.</p>

            <h2>Type de L'offre</h2>
            <div class="type-offre">
                <label for="aLaUneOffre">À la une</label>
                <input type="checkbox" id="aLaUneOffre" name="aLaUneOffre" <?= isset($offre['alauneoffre']) && $offre['alauneoffre'] ? 'checked' : '' ?>>
                
                <label for="enReliefOffre">En relief</label>
                <input type="checkbox" id="enReliefOffre" name="enReliefOffre" <?= isset($offre['enreliefoffre']) && $offre['enreliefoffre'] ? 'checked' : '' ?>>
            </div>

            <h2>Condition d'accessibilité</h2>
            <div>
                <textarea class="textarea-creer_offre" name="conditionAccessibilite" rows="4" placeholder="Accessible en fauteuil roulant..." required><?= htmlspecialchars($offre['conditionaccessibilite']) ?></textarea>
            </div>

            <h2>Site web de l'offre</h2>
            <input id="website" class="textarea-creer_offre" type="url" name="website" placeholder="https://exemple.com" value="<?= htmlspecialchars($offre['siteweboffre']) ?>" required oninput="checkValidWebsite(this)">
            <p id="error-website" style="color:red; display:none;">Veuillez entrer une adresse de site web valide.</p>

            <h2>Adresse</h2>
            <input type="text" width="100%" class="textarea-creer_offre" name="adresse" placeholder="Rue Edouard Branly" value="<?= htmlspecialchars($adresse['adresse']) ?>" required>

            <h2>Numéro de la rue</h2>
            <input id="adNumRue" type="number" width="100%" class="textarea-creer_offre" name="adNumRue" placeholder="13" value="<?= htmlspecialchars($adresse['numrue']) ?>" required oninput="checkNegativeValue(this)" onkeypress="preventInvalidChars(event)">
            <p id="error-adNumRue" style="color:red; display:none;">Veuillez entrer une valeur positive.</p>

            <h2>Adresse supplémentaire</h2>
            <input type="text" width="100%" class="textarea-creer_offre" name="supAdresse" placeholder="Bâtiment 4bis, Appartement 105" value="<?= htmlspecialchars($adresse['supplementadresse']) ?>">

            <h2>Code Postal</h2>
            <input id="adCodePostal" type="number" width="100%" class="textarea-creer_offre" name="adCodePostal" placeholder="22300" value="<?= htmlspecialchars($adresse['codepostal']) ?>" required oninput="checkNegativeValue(this)" onkeypress="preventInvalidChars(event); checkCodePostal(this)">
            <p id="error-adCodePostal" style="color:red; display:none;">Un code postal doit être positif et comprendre 5 chiffres</p>

            <h2>Ville</h2>
            <input type="text" width="100%" class="textarea-creer_offre" name="adVille" placeholder="Guingamp" value="<?= htmlspecialchars($adresse['ville']) ?>" required>

            <h2>Département</h2>
            <input type="text" width="100%" class="textarea-creer_offre" name="adDepartement" placeholder="Côtes-d'Armor" value="<?= htmlspecialchars($adresse['departement']) ?>" required>

            <h2>Pays</h2>
            <input type="text" width="100%" class="textarea-creer_offre" name="adPays" placeholder="France" value="<?= htmlspecialchars($adresse['pays']) ?>" required>

            <h2>Ajouter une/des image.s pour l'offre (seulement une pour l'instant)</h2>
            <div class="image-upload">
                <input type="file" name="imageOffre" accept=".png, .jpg, .jpeg" required>
            </div>

            <?php if ($cat === 'parc') : ?>
                <h2>Date d'ouverture</h2>
                <input class="zone-date" type="date" name="dateOuverture" value="<?= htmlspecialchars($offre['dateouverture']) ?>" required>

                <h2>Date de fermeture</h2>
                <input class="zone-date" type="date" name="dateFermeture" value="<?= htmlspecialchars($offre['datefermeture']) ?>" required>

                <br>
                <h2>Carte du parc</h2>
                <div class="image-upload">
                    <input type="file" name="carteParc" accept=".png, .jpg, .jpeg" required>
                </div>

                <h2>Nombre d'attractions disponibles</h2>
                <input id="nbrAttraction" class="zone-number" type="number" name="nbrAttraction" placeholder="Nombre d'attractions" value="<?= htmlspecialchars($offre['nbrattraction']) ?>" required oninput="checkNegativeValue(this)" onkeypress="preventInvalidChars(event)">
                <p id="error-nbrAttraction" style="color:red; display:none;">Veuillez entrer une valeur positive.</p>
            <?php elseif ($cat === 'visite' && $offreDetails['visiteguidee'] === true): ?>
                <h2>Visite guidée</h2>
                <label >
                    <input class="visite" type="radio" name="visiteGuidee" value="oui" checked required> Oui
                </label>
                <label >
                    <input class="visite" type="radio" name="visiteGuidee" value="non" required> Non
                </label>
            <?php elseif ($cat === 'visite' && $offreDetails['visiteguidee'] === false): ?>
                <h2>Visite guidée</h2>
                <label >
                    <input class="visite" type="radio" name="visiteGuidee" value="oui" required> Oui
                </label>
                <label >
                    <input class="visite" type="radio" name="visiteGuidee" value="non" checked required> Non
                </label>
            <?php endif; ?>
            <?php if ($cat === 'visite') : ?>
                <h2>Langues proposées</h2>
                <div class="langues">
                    <label><input type="checkbox" name="langues[]" value="Français"> Français</label>
                    <label><input type="checkbox" name="langues[]" value="Anglais"> Anglais</label>
                    <label><input type="checkbox" name="langues[]" value="Espagnol"> Espagnol</label>
                    <label><input type="checkbox" name="langues[]" value="Allemand"> Allemand</label>
                    <label><input type="checkbox" name="langues[]" value="Italien"> Italien</label>
                    <label><input type="checkbox" name="langues[]" value="Autre" id="autreCheckbox"> Autre</label>
                </div>
                <input type="text" width="100%" class="textarea-creer_offre" name="autreLangue" placeholder="Préciser les autres langues" style="display:none;" id="autreLangueInput">


            <?php endif; ?>
            <?php if ($cat === 'spectacle' || $cat === 'visite' || $cat === 'activite') : ?>
                <h2>Date de l'offre</h2>
                <input type="date" name="dateOffre" value="<?= htmlspecialchars($offreDetails['dateoffre']) ?>" required>
            <?php endif; ?>
            <?php if ($cat === 'spectacle') { ?>

            <?php } if ($cat ===  'spectacle' || $cat === 'activite') { ?>


            <?php } if ($cat != 'spectacle' && $cat != 'visite' && $cat != 'restauration' && $cat != '') {?>


            <?php } if ($cat == 'activite') { ?>


            <?php } if($cat == 'restauration') { ?>


            <?php } if ($cat != 'restauration') : ?>
                

            <?php endif; ?>
            <button type="submit" class="submit-btn">Mettre à jour</button>
        </form>
    </main>
    <div id="footer"></div>
</body>
</html>