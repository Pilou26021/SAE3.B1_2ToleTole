<?php
    error_reporting(E_ALL ^ E_WARNING);
    ob_start();
    session_start();
    
    //include du header
    include "header.php";

    // Inclusion du fichier de connexion à la base de données
    include "./SQL/connection_envdev.php";   

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

        $stmt = $conn->prepare("SELECT typetag FROM public.offreTag WHERE idOffre = :idOffre");
        $stmt->execute([':idOffre' => $idOffre]);
        $tags = $stmt->fetchAll();
        $existingTags = array_column($tags, 'typetag'); // Récupère uniquement les valeurs des tags

    } else {
        echo "<script>window.location.replace('index.php');</script>";
        exit();
    }
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
            $("#footer").load("footer.html"); 
        });
    </script> 

    <div id="header"></div>
    <main class="creer-offre-main">
        <h1>Modifier l'offre</h1>
        <form method="post" action="update_offer.php" enctype="multipart/form-data">
            <?php 
                // Envoie de l'ID de l'offre dans le post
                echo '<input type="hidden" name="idOffre" value="' . $idOffre . '">';
                // envoie de categorie dans le post
                echo '<input type="hidden" name="categorie" value="' . $cat . '">';
            ?>
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
                <input type="file" name="imageOffre" accept=".png, .jpg, .jpeg">
            </div>

            <?php if ($cat === 'parc') : ?>
                <h2>Date d'ouverture</h2>
                <input class="zone-date" type="date" name="dateOuverture" value="<?= htmlspecialchars($offre['dateouverture']) ?>" required>

                <h2>Date de fermeture</h2>
                <input class="zone-date" type="date" name="dateFermeture" value="<?= htmlspecialchars($offre['datefermeture']) ?>" required>

                <br>
                <h2>Carte du parc</h2>
                <div class="image-upload">
                    <input type="file" name="carteParc" accept=".png, .jpg, .jpeg">
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
                <?php
                    $languestring = $offreDetails['langueproposees'];

                    // Séparer les langues standards
                    $languestableau = explode(',', $languestring);

                    // Créer un tableau pour les langues standard
                    $languesStandard = ['Français', 'Anglais', 'Espagnol', 'Allemand', 'Italien'];
                    $offreLangues = [];

                    // Remplir le tableau d'offres avec les langues standards
                    foreach ($languestableau as $langue) {
                        $langue = trim($langue); // Nettoyer l'espace autour de chaque langue
                        if (in_array($langue, $languesStandard)) {
                            $offreLangues[] = $langue; // Ajouter au tableau d'offres si c'est une langue standard
                        }
                    }

                    // Vérifier s'il y a des autres langues
                    $languesautres = [];
                    foreach ($languestableau as $langue) {
                        if (strpos($langue, 'Autres langues :')) {
                            $languesautres[] = trim(substr($langue, strpos($langue, ':') + 1)); // Extraire les autres langues
                        }
                    }
                ?>

                <h2>Langues proposées</h2>
                <div class="langues">
                    <label><input type="checkbox" name="langues[]" value="Français" <?= in_array('Français', $offreLangues) ? 'checked' : ''; ?>> Français</label>
                    <label><input type="checkbox" name="langues[]" value="Anglais" <?= in_array('Anglais', $offreLangues) ? 'checked' : ''; ?>> Anglais</label>
                    <label><input type="checkbox" name="langues[]" value="Espagnol" <?= in_array('Espagnol', $offreLangues) ? 'checked' : ''; ?>> Espagnol</label>
                    <label><input type="checkbox" name="langues[]" value="Allemand" <?= in_array('Allemand', $offreLangues) ? 'checked' : ''; ?>> Allemand</label>
                    <label><input type="checkbox" name="langues[]" value="Italien" <?= in_array('Italien', $offreLangues) ? 'checked' : ''; ?>> Italien</label>
                    <label><input type="checkbox" name="langues[]" value="Autre" id="autreCheckbox" <?= !empty($languesautres) ? 'checked' : ''; ?>> Autre</label>
                </div>
                <input type="text" width="100%" class="textarea-creer_offre" name="autreLangue" placeholder="Préciser les autres langues" style="display: <?= !empty($languesautres) ? 'block' : 'none'; ?>;" id="autreLangueInput" value="<?= htmlspecialchars(implode(', ', $languesautres)); ?>">

                <script>
                    // Vérifiez si "Autre" est coché pour afficher le champ de texte correspondant
                    document.getElementById('autreCheckbox').addEventListener('change', function() {
                        var inputAutreLangue = document.getElementById('autreLangueInput');
                        if (this.checked) {
                            inputAutreLangue.style.display = 'block'; // Affiche l'input texte
                        } else {
                            inputAutreLangue.style.display = 'none';  // Masque l'input texte
                        }
                    });

                    // Affichez le champ de texte si "Autre" était déjà coché
                    if (document.getElementById('autreCheckbox').checked) {
                        document.getElementById('autreLangueInput').style.display = 'block';
                    }
                </script>

            <?php endif; ?>
            <?php if ($cat === 'spectacle' || $cat === 'visite' || $cat === 'activite') : ?>
                <h2>Date de l'offre</h2>
                <input type="date" name="dateOffre" value="<?= htmlspecialchars($offreDetails['dateoffre']) ?>" required>
            <?php endif; ?>
            <?php if ($cat === 'spectacle') { ?>

                <h2>Capacité d'acceuil</h2>
                <input id="cap_acceuil" class="zone-number" type="number" name="capaciteAcceuil" placeholder="ex : 300 personnes" required oninput="checkNegativeValue(this)" onkeypress="preventInvalidChars(event)" value="<?= htmlspecialchars($offreDetails['capaciteacceuil']) ?>">
                <p id="error-cap_acceuil" style="color:red; display:none;">Veuillez entrer une valeur positive.</p>

            <?php } if ($cat ===  'spectacle' || $cat === 'activite') { ?>

                <h2>Durée de l'activité (en heures)</h2>
                <input id="zone_duree_act" class="zone-number" type="number" name="indicationDuree" placeholder="ex : 2 heures" required oninput="checkNegativeValue(this)" onkeypress="preventInvalidChars(event)" value="<?= htmlspecialchars($offreDetails['indicationduree']) ?>">
                <p id="error-zone_duree_act" style="color:red; display:none;">Veuillez entrer une valeur positive.</p>
                <br>  

            <?php } if ($cat != 'spectacle' && $cat != 'visite' && $cat != 'restauration' && $cat != '') {?>

                <h2>Âge minimum</h2>
                <input id="ageMinimum" class="zone-number" type="number" name="ageMinimum" placeholder="Âge minimum" required oninput="checkNegativeValue(this)" onkeypress="preventInvalidChars(event)" value="<?= htmlspecialchars($offreDetails['ageminimum']) ?>">
                <p id="error-ageMinimum" style="color:red; display:none;">Veuillez entrer une valeur positive.</p>
                <br>    

            <?php } if ($cat == 'activite') { ?>

                <h2>Prestations incluses</h2>
                <textarea class="textarea-creer_offre" name="prestationIncluse"  placeholder="Détail des prestations incluses..." required></textarea>

            <?php } if($cat == 'restauration') { ?>
                <?php 
                    $offreDetails['horairesemaine'] = json_decode($offreDetails['horairesemaine'], true);
                    // On obtient : Array ( [lunchOpen] => 11:30 [lunchClose] => 14:00 [dinnerOpen] => 22:00 [dinnerClose] => 00:00 ) 
                ?>

                <h2>Horaires de la semaine</h2>
                <div class="horaires-semaine">
                    <label for="lunch_open_time">Horaire de déjeuner (ouverture) :</label>
                    <input type="time" name="lunchOpenTime" id="lunch_open_time" value="<?= isset($offreDetails['horairesemaine']['lunchOpen']) ? htmlspecialchars($offreDetails['horairesemaine']['lunchOpen']) : ''; ?>" required>
                        
                    <label for="lunch_close_time">Horaire de déjeuner (fermeture) :</label>
                    <input type="time" name="lunchCloseTime" id="lunch_close_time" value="<?= isset($offreDetails['horairesemaine']['lunchClose']) ? htmlspecialchars($offreDetails['horairesemaine']['lunchClose']) : ''; ?>" required>
                        
                    <label for="dinner_open_time">Horaire du dîner (ouverture) :</label> 
                    <input type="time" name="dinnerOpenTime" id="dinner_open_time" value="<?= isset($offreDetails['horairesemaine']['dinnerOpen']) ? htmlspecialchars($offreDetails['horairesemaine']['dinnerOpen']) : ''; ?>" required>
                        
                    <br>
                    <label for="dinner_close_time">Horaire du dîner (fermeture) :</label>
                    <input type="time" name="dinnerCloseTime" id="dinner_close_time" value="<?= isset($offreDetails['horairesemaine']['dinnerClose']) ? htmlspecialchars($offreDetails['horairesemaine']['dinnerClose']) : ''; ?>" required>
                    <br>
                        
                    <!-- <label for="closed_days">Jours de fermeture :</label> 
                    <input class="zone-text" type="text" name="closedDays" id="closed_days" placeholder="Ex: Lundi" value="<?= isset($offreDetails['closedDays']) ? htmlspecialchars($offreDetails['closedDays']) : ''; ?>"> -->
                </div>

                <h2>Gamme de prix</h2>
                <input id="gamme_prix" class="zone-number" type="number" name="averagePrice" placeholder="Prix moyen par personne" required oninput="checkNegativeValue(this)" onkeypress="preventInvalidChars(event)" value="<?= htmlspecialchars($offreDetails['gammeprix']) ?>">
                <p id="error-gamme_prix" style="color:red; display:none;">Veuillez entrer une valeur positive.</p>
                <h2>Carte du restaurant</h2>
                <div class="image-upload">
                    <input type="file" name="menuImage" accept=".png, .jpg, .jpeg">
                </div>

                <h2>Tags de l'offre</h2>
                <div class="tags">
                    <label><input type="checkbox" name="tags[]" value="Française" <?= in_array('Française', $existingTags) ? 'checked' : ''; ?>> Française</label>
                    <label><input type="checkbox" name="tags[]" value="Fruit de mer" <?= in_array('Fruit de mer', $existingTags) ? 'checked' : ''; ?>> Fruit de mer</label>
                    <label><input type="checkbox" name="tags[]" value="Asiatique" <?= in_array('Asiatique', $existingTags) ? 'checked' : ''; ?>> Asiatique</label>
                    <label><input type="checkbox" name="tags[]" value="Indienne" <?= in_array('Indienne', $existingTags) ? 'checked' : ''; ?>> Indienne</label>
                    <label><input type="checkbox" name="tags[]" value="Italienne" <?= in_array('Italienne', $existingTags) ? 'checked' : ''; ?>> Italienne</label>
                    <label><input type="checkbox" name="tags[]" value="Gastronomique" <?= in_array('Gastronomique', $existingTags) ? 'checked' : ''; ?>> Gastronomique</label>
                    <label><input type="checkbox" name="tags[]" value="Restauration rapide" <?= in_array('Restauration rapide', $existingTags) ? 'checked' : ''; ?>> Restauration rapide</label>
                    <label><input type="checkbox" name="tags[]" value="Crêperie" <?= in_array('Crêperie', $existingTags) ? 'checked' : ''; ?>> Crêperie</label>
                </div>
                <br>

            <?php } if ($cat != 'restauration') : ?>
                <h2>Tags de l'offre</h2>
                <div class="tags">
                    <label><input type="checkbox" name="tags[]" value="Classique" <?= in_array('Classique', $existingTags) ? 'checked' : ''; ?>> Classique</label>
                    <label><input type="checkbox" name="tags[]" value="Culturel" <?= in_array('Culturel', $existingTags) ? 'checked' : ''; ?>> Culturel</label>
                    <label><input type="checkbox" name="tags[]" value="Patrimoine" <?= in_array('Patrimoine', $existingTags) ? 'checked' : ''; ?>> Patrimoine</label>
                    <label><input type="checkbox" name="tags[]" value="Histoire" <?= in_array('Histoire', $existingTags) ? 'checked' : ''; ?>> Histoire</label>
                    <label><input type="checkbox" name="tags[]" value="Urbain" <?= in_array('Urbain', $existingTags) ? 'checked' : ''; ?>> Urbain</label>
                    <label><input type="checkbox" name="tags[]" value="Nature" <?= in_array('Nature', $existingTags) ? 'checked' : ''; ?>> Nature</label>
                    <label><input type="checkbox" name="tags[]" value="Plein air" <?= in_array('Plein air', $existingTags) ? 'checked' : ''; ?>> Plein air</label>
                    <label><input type="checkbox" name="tags[]" value="Sport" <?= in_array('Sport', $existingTags) ? 'checked' : ''; ?>> Sport</label>
                    <label><input type="checkbox" name="tags[]" value="Nautique" <?= in_array('Nautique', $existingTags) ? 'checked' : ''; ?>> Nautique</label>
                    <label><input type="checkbox" name="tags[]" value="Gastronomie" <?= in_array('Gastronomie', $existingTags) ? 'checked' : ''; ?>> Gastronomie</label>
                    <label><input type="checkbox" name="tags[]" value="Musée" <?= in_array('Musée', $existingTags) ? 'checked' : ''; ?>> Musée</label>
                    <label><input type="checkbox" name="tags[]" value="Atelier" <?= in_array('Atelier', $existingTags) ? 'checked' : ''; ?>> Atelier</label>
                    <label><input type="checkbox" name="tags[]" value="Musique" <?= in_array('Musique', $existingTags) ? 'checked' : ''; ?>> Musique</label>
                    <label><input type="checkbox" name="tags[]" value="Famille" <?= in_array('Famille', $existingTags) ? 'checked' : ''; ?>> Famille</label>
                    <label><input type="checkbox" name="tags[]" value="Cinéma" <?= in_array('Cinéma', $existingTags) ? 'checked' : ''; ?>> Cinéma</label>
                    <label><input type="checkbox" name="tags[]" value="Cirque" <?= in_array('Cirque', $existingTags) ? 'checked' : ''; ?>> Cirque</label>
                    <label><input type="checkbox" name="tags[]" value="Son et Lumière" <?= in_array('Son et Lumière', $existingTags) ? 'checked' : ''; ?>> Son et Lumière</label>
                    <label><input type="checkbox" name="tags[]" value="Humour" <?= in_array('Humour', $existingTags) ? 'checked' : ''; ?>> Humour</label>
                </div>
                <br>

            <?php endif; ?>
            <button type="submit" class="submit-btn">Mettre à jour</button>
        </form>
    </main>
    <div id="footer"></div>

    <script src="script.js"></script> 

</body>
</html>