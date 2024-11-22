<?php
    error_reporting(E_ALL ^ E_WARNING);

    //start session
    ob_start();
    session_start();

    //connecteur pour requête
    include "../SQL/connection_local.php";   

    // On vérifie si l'utilisateur est connecté. Il peut être connecté en tant que membre ou professionnel. Si il n'est pas connecté alors il sera visiteur.
    if (isset($_SESSION['professionnel'])) {
        $professionel = true;
        $idProPropose = $_SESSION['professionnel'];
    } else {
        ?> <script>window.location.replace('index.php');</script> <!-- Redirection en quittant la page actuelle --> <?php
    }

    //récupération de la catégorie dans le GET
    if (isset($_GET)){
        $cat = $_GET['categorie'];
    }

    function isCatValid($cat){
        switch ($cat) {
            case 'restauration':
                return true;
                break;
            case 'spectacle':
                return true;
                break;
            case 'visite':
                return true;
                break;
            case 'activite':
                return true;
                break;
            case 'parc':
                return true;
                break;
            default :
                return false;
                break;
        }
    }

    function getTextFromCat($cat){
        switch ($cat) {
            case 'restauration':
                return "Restauration";
                break;
            case 'spectacle':
                return "Spectacle";
                break;
            case 'visite':
                return "Visite";
                break;
            case 'activite':
                return "Activité";
                break;
            case 'parc':
                return "Parc d'attractions";
                break;
            default :
                return "---";
                break;
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

        <main class="creer-offre-main">

            <!-- Dropdown -->
            <label for="categorie">Choisissez une catégorie :</label>
            <br><br>
            <div class="dropdown" onmouseover="showDropdown()" onmouseout="hideDropdown()">
                <button class="dropdown-btn" id="dropdown-btn"><?php if(isCatValid($cat)) { echo getTextFromCat($cat); } else {echo "Choisir une catégorie";} ?></button>
                
                <div class="dropdown-content" id="dropdown-content">
                    <a href="creer_offre.php?categorie=restauration" onmouseover="changeButtonText(this)" onmouseout="resetButtonText(<?php $cat ?>)" >Restauration</a>
                    <a href="creer_offre.php?categorie=spectacle" onmouseover="changeButtonText(this)" onmouseout="resetButtonText(<?php $cat ?>)">Spectacle</a>
                    <a href="creer_offre.php?categorie=visite" onmouseover="changeButtonText(this)" onmouseout="resetButtonText(<?php $cat ?>)">Visite</a>
                    <a href="creer_offre.php?categorie=activite" onmouseover="changeButtonText(this)" onmouseout="resetButtonText(<?php $cat ?>)">Activité</a>
                    <a href="creer_offre.php?categorie=parc" onmouseover="changeButtonText(this)" onmouseout="resetButtonText(<?php $cat ?>)">Parc d’attractions</a>
                </div>
            </div>

            <form action="upload_offer.php" method="post" enctype="multipart/form-data" onsubmit="return validImages(document.querySelectorAll('.image-input-fn'))">            
            <br>
                <?php 
                    switch($cat){
                        case 'restauration':
                            ?><input value="restauration" type="text" name="categorie" style="display:none"><?php
                            break;
                        case 'spectacle':
                            ?><input value="spectacle" type="text" name="categorie" style="display:none"><?php
                            break;
                        case 'visite':
                            ?><input value="visite" type="text" name="categorie" style="display:none"><?php
                            break;
                        case 'activite':
                            ?><input value="activite"  type="text" name="categorie" style="display:none"><?php
                            break;
                        case 'parc':
                            ?><input value="parc" type="text" name="categorie" style="display:none"><?php
                            break;
                        default :
                            $cat = ''; // si la catégorie n'est pas valide on met une valeur ""
                            break;
                    }
                ?>

                <?php if($cat != '')  { ?>

                <h3 class="type-offre-text">Offre <?php 
                switch($cat){
                    case 'restauration':
                        echo 'de restaurant';
                        break;
                    case 'spectacle':
                        echo 'de spectacle';
                        break;
                    case 'visite':
                        echo 'de visite';
                        break;
                    case 'activite':
                        echo 'd\'activité';
                        break;
                    case 'parc':
                        echo 'de parc d\'attraction';
                        break;
                    default:
                        echo 'erreur';
                        break;
                }
                ?></h3><br>

                <h2>Type d'Offre</h2>
                <label >
                    <input class="visite" type="radio" name="typeOffre" value="Standard" required> Standard (10€)
                </label>
                <label >
                    <input class="visite" type="radio" name="typeOffre" value="Premium" required> Premium (25€)
                </label>

                <h2>Nom de l'offre</h2>
                <input class="zone-text" type="text" name="offerName" placeholder="Cote de granite rose" required> 

                <h2>Résumé</h2>
                <textarea class="textarea-creer_offre" name="summary" rows="2" placeholder="Résumé de l'offre..." required></textarea>
                <h2>Description détaillée</h2>
                <textarea class="textarea-creer_offre" name="description" rows="4" placeholder="Description détaillée..." required></textarea>

                <?php } else {
                        ?> <h2>Veuillez choisir une catégorie pour votre offre.</h2> <?php
                } if($cat != '' ) { ?>

                    <h2>Prix minimum de l'offre</h2>
                    <div class ="price">
                        <input id="min_price" class="zone-number" type="number" name="minPrice" placeholder="Prix Minimum" value="" required oninput="checkNegativeValue(this)" onkeypress="preventInvalidChars(event)">
                    </div>
                    <p id="error-min_price" style="color:red; display:none;">Veuillez entrer une valeur positive.</p>

                    <h2>Type de L'offre</h2>
                    <div class="type-offre">
                        <label for="aLaUneOffre">À la une</label>
                        <input type="checkbox" id="aLaUneOffre" name="aLaUneOffre">
                        
                        <label for="enReliefOffre">En relief</label>
                        <input type="checkbox" id="enReliefOffre" name="enReliefOffre">
                    </div>

                    <h2>Condition d'accessibilité</h2>
                    <textarea class="textarea-creer_offre" name="conditionAccessibilite" rows="4" placeholder="Accessible en fauteuil roulant..." required></textarea>

                    <h2>Site web de l'offre</h2>
                    <input id="website" class="textarea-creer_offre" type="url" name="website" placeholder="https://exemple.com" required oninput="checkValidWebsite(this)">
                    <p id="error-website" style="color:red; display:none;" >Veuillez entrez une adresse de site web valide.</p>


                    <h2>Adresse</h2>
                    <input type="text" width="100%" class="textarea-creer_offre" name="adresse" placeholder="Rue Edouard Branly" required>

                    <h2>Numéro de la rue</h2>
                    <input id="adNumRue" type="number" width="100%" class="textarea-creer_offre" name="adNumRue" placeholder="13" required oninput="checkNegativeValue(this)" onkeypress="preventInvalidChars(event)">
                    <p id="error-adNumRue" style="color:red; display:none;">Veuillez entrer une valeur positive.</p>

                    <h2>Adresse supplémentaire</h2>
                    <input type="text" width="100%" class="textarea-creer_offre" name="supAdresse" placeholder="Bâtiment 4bis, Appartemment 105">

                    <h2>Code Postal</h2>
                    <input id="adCodePostal" type="number" width="100%" class="textarea-creer_offre" name="adCodePostal" placeholder="22300" required oninput="checkNegativeValue(this)" onkeypress="preventInvalidChars(event); checkCodePostal(this)">
                    <p id="error-adCodePostal" style="color:red; display:none;">Un code postal doit être positif et comprendre 5 chiffres</p>

                    <h2>Ville</h2>
                    <input type="text" width="100%" class="textarea-creer_offre" name="adVille" placeholder="Guimgamp" required>

                    <h2>Département</h2>
                    <input type="text" width="100%" class="textarea-creer_offre" name="adDepartement" placeholder="Côtes-d'Armor" required>

                    <h2>Pays</h2>
                    <input type="text" width="100%" class="textarea-creer_offre" name="adPays" placeholder="France" required>

                    <h2>Ajouter une/des image.s pour l'offre (seulement une pour l'instant)</h2>
                    <div class="image-upload">
                        <input class="image-input-fn" type="file" name="imageOffre" accept=".png, .jpg, .jpeg" required>
                    </div>

                <?php } if ($cat == 'parc') {?>
                    <br>
                    <h2>Date d'ouverture</h2>
                    <input class="zone-date" type="date" name="dateOuverture" required>
                    <h2>Date de fermeture</h2>
                    <input class="zone-date" type="date" name="dateFermeture" required>
                    <br>
                    <h2>Carte du parc</h2>
                    <div class="image-upload">
                        <input class="image-input-fn" type="file" name="carteParc" accept=".png, .jpg, .jpeg" required>
                    </div>
                    <h2>Nombre d'attractions disponibles</h2>
                    <input id="nbrAttraction" class="zone-number" type="number" name="nbrAttraction" placeholder="Nombre d'attractions" required oninput="checkNegativeValue(this)" onkeypress="preventInvalidChars(event)">
                    <p id="error-nbrAttraction" style="color:red; display:none;">Veuillez entrer une valeur positive.</p>

                    <?php } if ($cat == 'visite') { ?>

                    <h2>Visite guidée</h2>
                    <label >
                        <input class="visite" type="radio" name="visiteGuidee" value="oui" required> Oui
                    </label>
                    <label >
                        <input class="visite" type="radio" name="visiteGuidee" value="non" required> Non
                    </label>

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

                    <script>
                        //check si autreLangueInput est coché si oui affiché le bloc de texte supplémentaire
                        document.getElementById('autreCheckbox').addEventListener('change', function() {
                            var inputAutreLangue = document.getElementById('autreLangueInput');
                            if (this.checked) {
                                inputAutreLangue.style.display = 'block'; // Affiche l'input texte
                            } else {
                                inputAutreLangue.style.display = 'none';  // Masque l'input texte
                            }
                        });
                    </script>

                <?php } if ($cat == "spectacle" || $cat == "visite" || $cat == "activite") { ?>
                    <h2>Date de l'offre</h2>
                    <input type="date" name="dateOffre">

                <?php } if ($cat == "spectacle") { ?>
                
                    <h2>Capacité d'acceuil</h2>
                    <input id="cap_acceuil" class="zone-number" type="number" name="capaciteAcceuil" placeholder="ex : 300 personnes" required oninput="checkNegativeValue(this)" onkeypress="preventInvalidChars(event)">
                    <p id="error-cap_acceuil" style="color:red; display:none;">Veuillez entrer une valeur positive.</p>

                <?php } if ($cat == 'spectacle' || $cat == 'activite') { ?>
                        

                    <h2>Durée de l'activité (en heures)</h2>
                    <input id="zone_duree_act" class="zone-number" type="number" name="indicationDuree" placeholder="ex : 2 heures" required oninput="checkNegativeValue(this)" onkeypress="preventInvalidChars(event)">
                    <p id="error-zone_duree_act" style="color:red; display:none;">Veuillez entrer une valeur positive.</p>
                    <br>                

                <?php } if ($cat != 'spectacle' && $cat != 'visite' && $cat != 'restauration' && $cat != ''){?>
                    <h2>Âge minimum</h2>
                    <input id="ageMinimum" class="zone-number" type="number" name="ageMinimum" placeholder="Âge minimum" required oninput="checkNegativeValue(this)" onkeypress="preventInvalidChars(event)">
                    <p id="error-ageMinimum" style="color:red; display:none;">Veuillez entrer une valeur positive.</p>
                    <br>    

                <?php } if ($cat == 'activite') { ?>

                    <h2>Prestations incluses</h2>
                    <textarea class="textarea-creer_offre" name="prestationIncluse"  placeholder="Détail des prestations incluses..." required></textarea>

                <?php } if($cat == 'restauration') { ?>

                <h2>Horaires de la semaine</h2>
                <div class="horaires-semaine">
                    <label for="lunch_open_time">Horaire de déjeuner (ouverture) :</label>
                    <input type="time" name="lunchOpenTime" id="lunch_open_time" required>
                    <label for="lunch_close_time">Horaire de déjeuner (fermeture) :</label>
                    <input type="time" name="lunchCloseTime" id="lunch_close_time" required>
                    <label for="dinner_open_time">Horaire du dîner (ouverture) :</label> 
                    <input type="time" name="dinnerOpenTime" id="dinner_open_time" required> <br>
                    <label for="dinner_close_time">Horaire du dîner (fermeture) :</label>
                    <input type="time" name="dinnerCloseTime" id="dinner_close_time" required>
                    <br>
                    <!-- <label for="closed_days">Jours de fermeture :</label> 
                    <input class="zone-text" type="text" name="closedDays" id="closed_days" placeholder="Ex: Lundi"> -->
                </div>
                <h2>Gamme de prix</h2>
                <input id="gamme_prix" class="zone-number" type="number" name="averagePrice" placeholder="Prix moyen par personne" required oninput="checkNegativeValue(this)" onkeypress="preventInvalidChars(event)">
                <p id="error-gamme_prix" style="color:red; display:none;">Veuillez entrer une valeur positive.</p>
                <h2>Carte du restaurant</h2>
                <div class="image-upload">
                    <input class="image-input-fn" type="file" name="menuImage" accept=".png, .jpg, .jpeg" required>
                </div>
                <h2>Tags de l'offre</h2>
                <div class="tags">
                    <label><input type="checkbox" name="tags[]" value="Française"> Française</label>
                    <label><input type="checkbox" name="tags[]" value="Fruit de mer"> Fruit de mer</label>
                    <label><input type="checkbox" name="tags[]" value="Asiatique"> Asiatique</label>
                    <label><input type="checkbox" name="tags[]" value="Indienne"> Indienne</label>
                    <label><input type="checkbox" name="tags[]" value="Italienne"> Italienne</label>
                    <label><input type="checkbox" name="tags[]" value="Gastronomique"> Gastronomique</label>
                    <label><input type="checkbox" name="tags[]" value="Restauration rapide"> Restauration rapide</label>
                    <label><input type="checkbox" name="tags[]" value="Crêperie"> Crêperie</label>
                </div>
                <br>

                <?php } if ($cat != 'restauration' && $cat != '') { ?>
                <h2>Tags de l'offre</h2>
                <div class="tags">
                    <label><input type="checkbox" name="tags[]" value="Classique"> Classique</label>
                    <label><input type="checkbox" name="tags[]" value="Culturel"> Culturel</label>
                    <label><input type="checkbox" name="tags[]" value="Patrimoine"> Patrimoine</label>
                    <label><input type="checkbox" name="tags[]" value="Histoire"> Histoire</label>
                    <label><input type="checkbox" name="tags[]" value="Urbain"> Urbain</label>
                    <label><input type="checkbox" name="tags[]" value="Nature"> Nature</label>
                    <label><input type="checkbox" name="tags[]" value="Plein air"> Plein air</label>
                    <label><input type="checkbox" name="tags[]" value="Sport"> Sport</label>
                    <label><input type="checkbox" name="tags[]" value="Nautique"> Nautique</label>
                    <label><input type="checkbox" name="tags[]" value="Gastronomie"> Gastronomie</label>
                    <label><input type="checkbox" name="tags[]" value="Musée"> Musée</label>
                    <label><input type="checkbox" name="tags[]" value="Atelier"> Atelier</label>
                    <label><input type="checkbox" name="tags[]" value="Musique"> Musique</label>
                    <label><input type="checkbox" name="tags[]" value="Famille"> Famille</label>
                    <label><input type="checkbox" name="tags[]" value="Cinéma"> Cinéma</label>
                    <label><input type="checkbox" name="tags[]" value="Cirque"> Cirque</label>
                    <label><input type="checkbox" name="tags[]" value="Son et Lumière"> Son et Lumière</label>
                    <label><input type="checkbox" name="tags[]" value="Humour"> Humour</label>
                </div>
                <br>

                <?php } if ($cat != '' ){ ?>
                <button type="submit" class="submit-btn">Créer une offre</button>
                <?php } ?>

            </form>
        </main>
        <div id="footer"></div>

        <script src="./script.js" ></script>

    </body>
</html>