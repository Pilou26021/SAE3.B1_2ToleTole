<?php
    session_start();
    if (isset($_GET)){
        $cat = $_GET['categorie'];
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

        <main class="creer-offre-main">
            
            <form action="creer_offre.php" method="get" enctype="multipart/form-data"> 
                <h2>Catégorie de l'offre</h2> 
                <div class="categories">
                    <input type="radio" name="categorie" value="restauration" id="cat-restauration" required>
                    <label class="category" for="cat-restauration">Restauration</label>
                    <input type="radio" name="categorie" value="spectacle" id="cat-spectacles" required>
                    <label class="category" for="cat-spectacles">Spectacles</label>  
                    <input type="radio" name="categorie" value="visite" id="cat-visites" required>
                    <label class="category" for="cat-visites">Visites</label>
                    <input type="radio" name="categorie" value="activite" id="cat-activites" required>
                    <label class="category" for="cat-activites">Activités</label>
                    <input type="radio" name="categorie" value="parc" id="cat-parcs" required>
                    <label class="category" for="cat-parcs">Parcs d’attractions</label>
                    <input class="submit-btn" type="submit" value="Choisir cette catégorie">
                </div>
            </form>

            <form action="send_offer.php" method="post" enctype="multipart/form-data">

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
                        case '':
                            break;
                    }
                ?>

                <?php if($cat != '')  { ?>

                <h2>Type d'Offre</h2>
                <label >
                    <input class="visite" type="radio" name="typeOffre" value="Standard" required> Standard (10€)
                </label>
                <label >
                    <input class="visite" type="radio" name="typeOffre" value="Premium" required> Premium (25€)
                </label>

                <h2>Nom de l'offre</h2>
                <input class="zone-text" type="text" name="offerName" placeholder="Cote de granite rose" required> 

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
                ?></h3>

                <h2>Résumé</h2>
                <textarea class="textarea-creer_offre" name="summary" rows="2" placeholder="Résumé de l'offre..." required></textarea>
                <h2>Description détaillée</h2>
                <textarea class="textarea-creer_offre" name="description" rows="4" placeholder="Description détaillée..." required></textarea>

                <?php } else {
                        ?> <h2>Veuillez choisir une catégorie pour votre offre.</h2> <?php
                    } if($cat != '' && $cat != 'restauration') { ?>
                <h2>Prix</h2>
                <div class="price">
                    <input id="adult_price" class="zone-number" type="number" name="adultPrice" placeholder="Prix adult" value="" required oninput="checkNegativeValue(this)" onkeypress="preventInvalidChars(event)">
                    <input id="child_price" class="zone-number" type="number" name="childPrice" placeholder="Prix enfant (-18)" value="" required oninput="checkNegativeValue(this)" onkeypress="preventInvalidChars(event)">
                </div>  
                <p id="error-adult_price" style="color:red; display:none;">Veuillez entrer une valeur positive.</p>
                <p id="error-child_price" style="color:red; display:none;">Veuillez entrer une valeur positive.</p>
                
                <?php } if($cat != '' ) { ?>

                    <h2>Type de L'offre</h2>
                    <div class="type-offre">
                        <label for="aLaUneOffre">À la une</label>
                        <input type="checkbox" name="aLaUneOffre" <?= $offer['aLaUneOffre'] ? 'checked' : '' ?>>
                        <label for="enReliefOffre">En relief</label>
                        <input type="checkbox" name="enReliefOffre" <?= $offer['enReliefOffre'] ? 'checked' : '' ?>>
                    </div>
                        <h2>Site web de l'offre</h2>
                        <input id="website" class="zone-text" type="url" name="website" placeholder="https://exemple.com" required oninput="checkValidWebsite(this)">
                        <p id="error-website" style="color:red" >Veuillez entrez une adresse de site web valide.</p>
                        <h2>Adresse/coordonnée</h2>
                        <input class="zone-text" type="url" name="address" placeholder="https://google.fr/maps/place/..." required>
                        <h2>Ajouter une image principale de l'offre</h2>
                    <div class="image-upload">
                    <input type="file" name="offer_image" accept="image/*" required>
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
                        <input type="file" name="carteParc" accept="image/*" required>
                    </div>
                    <h2>Nombre d'attractions disponibles</h2>
                    <input id="nbrAttractions" class="zone-number" type="number" name="nbrAttractions" placeholder="Nombre d'attractions" required oninput="checkNegativeValue(this)" onkeypress="preventInvalidChars(event)">
                    <p id="error-nbrAttractions" style="color:red; display:none;">Veuillez entrer une valeur positive.</p>

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
                        <label><input type="checkbox" name="langues" value="Français"> Français</label>
                        <label><input type="checkbox" name="langues" value="Anglais"> Anglais</label>
                        <label><input type="checkbox" name="langues" value="Espagnol"> Espagnol</label>
                        <label><input type="checkbox" name="langues" value="Allemand"> Allemand</label>
                        <label><input type="checkbox" name="langues" value="Italien"> Italien</label>
                        <label><input type="checkbox" name="langues" value="Autre"> Autre</label>
                        <input type="text" name="autreLangue" placeholder="Préciser autre langue" style="display:none;" id="autreLangueInput">
                    </div>
                
                <?php } if( $cat != '' && $cat == 'activite' || $cat == 'spectacle' || $cat == 'visite') { ?>
                    <h2>Périodes d'ouverture</h2>
                    <label for="day">Jour d'ouverture</label>
                    <select name="day" id="day" required>
                        <option value="Lundi" <?= $selected_day == 'Lundi' ? 'selected' : '' ?>>Lundi</option>
                        <option value="Mardi" <?= $selected_day == 'Mardi' ? 'selected' : '' ?>>Mardi</option>
                        <option value="Mercredi" <?= $selected_day == 'Mercredi' ? 'selected' : '' ?>>Mercredi</option>
                        <option value="Jeudi" <?= $selected_day == 'Jeudi' ? 'selected' : '' ?>>Jeudi</option>
                        <option value="Vendredi" <?= $selected_day == 'Vendredi' ? 'selected' : '' ?>>Vendredi</option>
                        <option value="Samedi" <?= $selected_day == 'Samedi' ? 'selected' : '' ?>>Samedi</option>
                        <option value="Dimanche" <?= $selected_day == 'Dimanche' ? 'selected' : '' ?>>Dimanche</option>
                    </select>
                    <div class="hours">
                        <label for="open_time">Horaire d'ouverture:</label>
                        <input type="time" name="openTime" id="open_time" value="<?= $open_time ?>" required>
                        <label for="close_time">Horaire de fermeture:</label>
                        <input type="time" name="closeTime" id="close_time" value="<?= $close_time ?>" required>
                    </div>

                <?php } if ($cat != 'parc' && $cat != 'restauration' && $cat != '') { ?>
                        

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
                    <textarea class="textarea-creer_offre" name="prestationIncluses"  placeholder="Détail des prestations incluses..." required></textarea>

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
                    <label for="closed_days">Jours de fermeture :</label> 
                    <input class="zone-text" type="text" name="closedDays" id="closed_days" placeholder="Ex: Lundi" required>
                </div>
                <h2>Gamme de prix</h2>
                <input id="gamme_prix" class="zone-number" type="number" name="averagePrice" placeholder="Prix moyen par personne" required oninput="checkNegativeValue(this)" onkeypress="preventInvalidChars(event)">
                <p id="error-gamme_prix" style="color:red; display:none;">Veuillez entrer une valeur positive.</p>
                <h2>Carte du restaurant</h2>
                <div class="image-upload">
                    <input type="file" name="menuImage" accept="image/*" required>
                </div>
                <h2>Tags de l'offre</h2>
                <div class="tags">
                    <label><input type="checkbox" name="tags" value="Française"> Française</label>
                    <label><input type="checkbox" name="tags" value="Fruit de mer"> Fruit de mer</label>
                    <label><input type="checkbox" name="tags" value="Asiatique"> Asiatique</label>
                    <label><input type="checkbox" name="tags" value="Indienne"> Indienne</label>
                    <label><input type="checkbox" name="tags" value="Italienne"> Italienne</label>
                    <label><input type="checkbox" name="tags" value="Gastronomique"> Gastronomique</label>
                    <label><input type="checkbox" name="tags" value="Restauration rapide"> Restauration rapide</label>
                    <label><input type="checkbox" name="tags" value="Crêperie"> Crêperie</label>
                </div>
                <br>

                <?php } if ($cat != 'restauration' && $cat != '') {?>
                    <h2>Tags de l'offre</h2>
                    <div class="tags">
                        <label><input type="checkbox" name="tags" value="Classique"> Classique</label>
                        <label><input type="checkbox" name="tags" value="Culturel"> Culturel</label>
                        <label><input type="checkbox" name="tags" value="Patrimoine"> Patrimoine</label>
                        <label><input type="checkbox" name="tags" value="Histoire"> Histoire</label>
                        <label><input type="checkbox" name="tags" value="Urbain"> Urbain</label>
                        <label><input type="checkbox" name="tags" value="Nature"> Nature</label>
                        <label><input type="checkbox" name="tags" value="Plein air"> Plein air</label>
                        <label><input type="checkbox" name="tags" value="Sport"> Sport</label>
                        <label><input type="checkbox" name="tags" value="Nautique"> Nautique</label>
                        <label><input type="checkbox" name="tags" value="Gastronomie"> Gastronomie</label>
                        <label><input type="checkbox" name="tags" value="Musée"> Musée</label>
                        <label><input type="checkbox" name="tags" value="Atelier"> Atelier</label>
                        <label><input type="checkbox" name="tags" value="Musique"> Musique</label>
                        <label><input type="checkbox" name="tags" value="Famille"> Famille</label>
                        <label><input type="checkbox" name="tags" value="Cinéma"> Cinéma</label>
                        <label><input type="checkbox" name="tags" value="Cirque"> Cirque</label>
                        <label><input type="checkbox" name="tags" value="Son et Lumière"> Son et Lumière</label>
                        <label><input type="checkbox" name="tags" value="Humour"> Humour</label>
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