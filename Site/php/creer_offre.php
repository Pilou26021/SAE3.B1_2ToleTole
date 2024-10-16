<?php
    session_start();
?>


<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="./styles-test.css">
        <title>Détail Offres</title>
    </head>
    <body>
        <header>
            <img src="img/Menu.png" alt="image Menu" title="image menu">
            <img src="img/fond_remove.png" alt="logo site noir" title="logo site noir">
            <img src="img/User.png" alt="image user" title="image user" style="width: 30px; height: 30px;">
        </header>

        <main class="creer-offre-main">

            <h2>Nom de l'offre</h2>
            <input type="text" name="offer_name" placeholder="Cote de granite rose" <?php ?>required> 

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

            <form action="submit_offer.php" method="post" enctype="multipart/form-data">
                <h3 class="type-offre-text">Offre restaurant</h3>
                <h2>Résumé</h2>
                <textarea name="summary" rows="2" placeholder="Résumé de l'offre..." required></textarea>
                <h2>Description détaillée</h2>
                <textarea name="description" rows="4" placeholder="Description détaillée..." required></textarea>
                <h2>Prix</h2>

                <?php if($_GET['categorie'] != 'restauration') { ?>
                <div class="price">
                    <input type="text" name="adult_price" placeholder="Prix adult" value="" required>
                    <input type="text" name="child_price" placeholder="Prix enfant (-18)" value="" required>
                </div>  
                 <?php } ?>

                <h2>Type de L'offre</h2>
                <div class="type-offre">
                <label for="aLaUneOffre">À la une</label>
                <input type="checkbox" name="aLaUneOffre" <?= $offer['aLaUneOffre'] ? 'checked' : '' ?>>
                <label for="enReliefOffre">En relief</label>
                <input type="checkbox" name="enReliefOffre" <?= $offer['enReliefOffre'] ? 'checked' : '' ?>>
                </div>
                <h2>Site web de l'offre</h2>
                <input type="url" name="website" placeholder="https://exemple.com" required>
                
                <?php if($_GET['categorie'] !== 'restauration' && $_GET['categorie'] !== 'parc') { ?>
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
                    <input type="time" name="open_time" id="open_time" value="<?= $open_time ?>" required>
                    <label for="close_time">Horaire de fermeture:</label>
                    <input type="time" name="close_time" id="close_time" value="<?= $close_time ?>" required>
                </div>
                <?php } ?>

                <h2>Adresse/coordonnée</h2>
                <input type="url" name="address" placeholder="https://google.fr/maps/place/..." required>
                <h2>Ajouter une image principale de l'offre</h2>
                <div class="image-upload">
                    <input type="file" name="offer_image" accept="image/*" required>

                <?php if($_GET['categorie'] == 'restauration') { ?>
                </div>
                            <h2>Horaires de la semaine</h2>
                <div class="horaires-semaine">
                    <label for="lunch_open_time">Horaire de déjeuner (ouverture) :</label>
                    <input type="time" name="lunch_open_time" id="lunch_open_time" required>
                    <label for="lunch_close_time">Horaire de déjeuner (fermeture) :</label>
                    <input type="time" name="lunch_close_time" id="lunch_close_time" required>
                    <label for="dinner_open_time">Horaire du dîner (ouverture) :</label>
                    <input type="time" name="dinner_open_time" id="dinner_open_time" required>
                    <label for="dinner_close_time">Horaire du dîner (fermeture) :</label>
                    <input type="time" name="dinner_close_time" id="dinner_close_time" required>
                    <br>
                    <label for="closed_days">Jours de fermeture :</label> 
                    <input type="text" name="closed_days" id="closed_days" placeholder="Ex: Lundi" required>
                </div>
                <h2>Gamme de prix</h2>
                <input type="text" name="average_price" placeholder="Prix moyen par personne" required>
                <h2>Carte du restaurant</h2>
                <div class="image-upload">
                    <input type="file" name="menu_image" accept="image/*" required>
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
                <button type="submit" class="submit-btn">Créer une offre</button>
            </form>

            <?php
                } else if($_GET['categorie'] == 'spectacle') {
            ?>

                <form action="submit_offer.php" method="post" enctype="multipart/form-data">
                    <h3 class="type-offre-text">Offre spectacle</h3>
                    <h2>Résumé</h2>
                    <textarea name="summary" rows="2" placeholder="Résumé de l'offre..." required></textarea>

                    <h2>Description détaillée</h2>
                    <textarea name="description" rows="4" placeholder="Description détaillée..." required></textarea>

                    <h2>Prix</h2>
                    <div class="price">
                        <input type="text" name="adult_price" placeholder="Prix adult" value="" required>
                        <input type="text" name="child_price" placeholder="Prix enfant (-18)" value="" required>
                    </div>  

                    <h2>Type de L'offre</h2>
                    <div class="type-offre">
                    <label for="aLaUneOffre">À la une</label>
                    <input type="checkbox" name="aLaUneOffre" <?= $offer['aLaUneOffre'] ? 'checked' : '' ?>>

                    <label for="enReliefOffre">En relief</label>
                    <input type="checkbox" name="enReliefOffre" <?= $offer['enReliefOffre'] ? 'checked' : '' ?>>
                    </div>

                    <h2>Site web de l'offre</h2>
                    <input type="url" name="website" placeholder="https://exemple.com" required>
                    
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
                        <input type="time" name="open_time" id="open_time" value="<?= $open_time ?>" required>

                        <label for="close_time">Horaire de fermeture:</label>
                        <input type="time" name="close_time" id="close_time" value="<?= $close_time ?>" required>
                    </div>

                    
                    <h2>Durée du spectacle</h2>
                    <div>
                    <input type="text" name="indicationDuree" placeholder="Durée en minutes" value="120min" required>  
                    </div>

                    <div>
                    <h2>Capacité d'accueil</h2>
                    <input type="number" name="capacitéAcceuil" placeholder="Capacité en nombre de personnes" value="<?= $capacitéAcceuil ?>" required>  
                    </div>

                    <h2>Adresse/coordonnée</h2>
                    <div>
                    <input type="url" name="address" placeholder="https://google.fr/maps/place/..." required>
                    </div>

                    <h2>Ajouter une image principale de l'offre</h2>
                    <div class="image-upload">
                        <input type="file" name="offer_image" accept="image/*" required>
                    </div>

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

                    <button type="submit" class="submit-btn">Créer une offre</button>
                </form>

            <?php
                } else if($_GET['categorie'] == 'visite') {
            ?>

                <form action="submit_offer.php" method="post" enctype="multipart/form-data">
                    <h3 class="type-offre-text">Offre visite</h3>
                    <h2>Résumé</h2>
                    <textarea name="summary" rows="2" placeholder="Résumé de l'offre..." required></textarea>

                    <h2>Description détaillée</h2>
                    <textarea name="description" rows="4" placeholder="Description détaillée..." required></textarea>

                    <h2>Prix</h2>
                    <div class="price">
                        <input type="text" name="adult_price" placeholder="Prix adult" value="" required>
                        <input type="text" name="child_price" placeholder="Prix enfant (-18)" value="" required>
                    </div>  

                    <h2>Type de L'offre</h2>
                    <div class="type-offre">
                    <label for="aLaUneOffre">À la une</label>
                    <input type="checkbox" name="aLaUneOffre" <?= $offer['aLaUneOffre'] ? 'checked' : '' ?>>

                    <label for="enReliefOffre">En relief</label>
                    <input type="checkbox" name="enReliefOffre" <?= $offer['enReliefOffre'] ? 'checked' : '' ?>>
                    </div>

                    <h2>Site web de l'offre</h2>
                    <input type="url" name="website" placeholder="https://exemple.com" required>
                    
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
                        <input type="time" name="open_time" id="open_time" value="<?= $open_time ?>" required>

                        <label for="close_time">Horaire de fermeture:</label>
                        <input type="time" name="close_time" id="close_time" value="<?= $close_time ?>" required>
                    </div>

                    
                    <h2>Durée du spectacle</h2>
                    <div>
                    <input type="text" name="indicationDuree" placeholder="Durée en minutes" value="120min" required>  
                    </div>

                    <div>
                    <h2>Capacité d'accueil</h2>
                    <input type="number" name="capacitéAcceuil" placeholder="Capacité en nombre de personnes" value="<?= $capacitéAcceuil ?>" required>  
                    </div>

                    <h2>Adresse/coordonnée</h2>
                    <div>
                    <input type="url" name="address" placeholder="https://google.fr/maps/place/..." required>
                    </div>

                    <h2>Ajouter une image principale de l'offre</h2>
                    <div class="image-upload">
                        <input type="file" name="offer_image" accept="image/*" required>
                    </div>

                    <h2>Durée de la visite</h2>
                    <input type="text" name="indicationDuree" placeholder="Durée de la visite (ex: 1h, 2h)" required>

                    <h2>Visite guidée</h2>
                    <label>
                        <input type="radio" name="visiteGuidee" value="oui" required> Oui
                    </label>
                    <label>
                        <input type="radio" name="visiteGuidee" value="non"> Non
                    </label>

                    <h2>Langues proposées</h2>
                    <div class="langues">
                        <label><input type="checkbox" name="langues[]" value="Français"> Français</label>
                        <label><input type="checkbox" name="langues[]" value="Anglais"> Anglais</label>
                        <label><input type="checkbox" name="langues[]" value="Espagnol"> Espagnol</label>
                        <label><input type="checkbox" name="langues[]" value="Allemand"> Allemand</label>
                        <label><input type="checkbox" name="langues[]" value="Italien"> Italien</label>
                        <label><input type="checkbox" name="langues[]" value="Autre"> Autre</label>
                        <input type="text" name="autreLangue" placeholder="Préciser autre langue" style="display:none;" id="autreLangueInput">
                    </div>

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

                    <button type="submit" class="submit-btn">Créer une offre</button>
                </form>

            <?php
                } else if($_GET['categorie'] == 'activite') {
            ?>

                <form action="submit_offer.php" method="post" enctype="multipart/form-data">
                    <h3 class="type-offre-text">Offre activité</h3>
                    <h2>Résumé</h2>
                    <textarea name="summary" rows="2" placeholder="Résumé de l'offre..." required></textarea>

                    <h2>Description détaillée</h2>
                    <textarea name="description" rows="4" placeholder="Description détaillée..." required></textarea>

                    <h2>Prix</h2>
                    <div class="price">
                        <input type="text" name="adult_price" placeholder="Prix adult" value="" required>
                        <input type="text" name="child_price" placeholder="Prix enfant (-18)" value="" required>
                    </div>  

                    <h2>Type de L'offre</h2>
                    <div class="type-offre">
                    <label for="aLaUneOffre">À la une</label>
                    <input type="checkbox" name="aLaUneOffre" <?= $offer['aLaUneOffre'] ? 'checked' : '' ?>>

                    <label for="enReliefOffre">En relief</label>
                    <input type="checkbox" name="enReliefOffre" <?= $offer['enReliefOffre'] ? 'checked' : '' ?>>
                    </div>

                    <h2>Site web de l'offre</h2>
                    <input type="url" name="website" placeholder="https://exemple.com" required>
                    
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
                        <input type="time" name="open_time" id="open_time" value="<?= $open_time ?>" required>

                        <label for="close_time">Horaire de fermeture:</label>
                        <input type="time" name="close_time" id="close_time" value="<?= $close_time ?>" required>
                    </div>


                    <h2>Adresse/coordonnée</h2>
                    <input type="url" name="address" placeholder="https://google.fr/maps/place/..." required>

                    <h2>Ajouter une image principale de l'offre</h2>
                    <div class="image-upload">
                        <input type="file" name="offer_image" accept="image/*" required>
                    </div>

                    <h2>Date de l'activité</h2>
                    <input type="date" name="dateOffre" value="<?= htmlspecialchars($dateOffre) ?>" required>

                    <h2>Durée de l'activité (en heures)</h2>
                    <input type="number" name="indicationDuree" value="<?= htmlspecialchars($indicationDuree) ?>" required>

                    <h2>Âge requis (min)</h2>
                    <input type="number" name="ageRequis" value="<?= htmlspecialchars($ageRequis) ?>" required>

                    <h2>Prestations incluses</h2>
                    <textarea name="prestationIncluses"  placeholder="Détail des prestations incluses..." required></textarea>

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

                    <button type="submit" class="submit-btn">Créer une offre</button>
                </form>

            <?php
                } else if($_GET['categorie'] == 'parc') {
            ?>

                <form action="submit_offer.php" method="post" enctype="multipart/form-data">*
                    <h3 class="type-offre-text">Offre parc d'attraction</h3>
                    <h2>Résumé</h2>
                    <textarea name="summary" rows="2" placeholder="Résumé de l'offre..." required></textarea>

                    <h2>Description détaillée</h2>
                    <textarea name="description" rows="4" placeholder="Description détaillée..." required></textarea>

                    <h2>Prix</h2>
                    <div class="price">
                        <input type="text" name="adult_price" placeholder="Prix adult" value="" required>
                        <input type="text" name="child_price" placeholder="Prix enfant (-18)" value="" required>
                    </div>  

                    <h2>Type de L'offre</h2>
                    <div class="type-offre">
                    <label for="aLaUneOffre">À la une</label>
                    <input type="checkbox" name="aLaUneOffre" <?= $offer['aLaUneOffre'] ? 'checked' : '' ?>>

                    <label for="enReliefOffre">En relief</label>
                    <input type="checkbox" name="enReliefOffre" <?= $offer['enReliefOffre'] ? 'checked' : '' ?>>
                    </div>

                    <h2>Site web de l'offre</h2>
                    <input type="url" name="website" placeholder="https://exemple.com" required>
                    
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
                        <input type="time" name="open_time" id="open_time" value="<?= $open_time ?>" required>

                        <label for="close_time">Horaire de fermeture:</label>
                        <input type="time" name="close_time" id="close_time" value="<?= $close_time ?>" required>
                    </div>


                    <h2>Adresse/coordonnée</h2>
                    <input type="url" name="address" placeholder="https://google.fr/maps/place/..." required>

                    <h2>Ajouter une image principale de l'offre</h2>
                    <div class="image-upload">
                        <input type="file" name="offer_image" accept="image/*" required>
                    </div>

                    <h2>Date d'ouverture</h2>
                    <input type="date" name="dateOuverture" required>

                    <h2>Date de fermeture</h2>
                    <input type="date" name="dateFermeture" required>

                    <h2>Carte du parc</h2>
                    <div class="image-upload">
                        <input type="file" name="carteParc" accept="image/*" required>
                    </div>

                    <h2>Nombre d'attractions disponibles</h2>
                    <input type="number" name="nbrAttractions" placeholder="Nombre d'attractions" required>

                    <h2>Âge minimum</h2>
                    <input type="number" name="ageMinimum" placeholder="Âge minimum" required>


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

                    <button type="submit" class="submit-btn">Créer une offre</button>
                </form>

            <?php
                }
            ?>

        </main>

        <footer>
            <div class="l_footer">
                <div class="l_footer_1" >
                    <h4>À propos de PACT</h4>
                    <ul class="list-footer" >
                        <li>Conditions d'utilisations</li>
                        <li>Confidentialité et utilisation des cookies</li>
                        <li>Mentions Légales</li>
                        <li>Contactez-nous</li>
                        <li>Ressources et règlements</li>
                    </ul>
                </div>
                <div class="l_footer_2">
                    <h4>Explorez</h4>
                    <ul class="list-footer">
                        <li>Ajouter une offre</li>
                        <li>S’inscrire</li>
                        <li>Nos offres</li>
                        <li>Assistance</li>

                    </ul>
                </div>
            </div>
            <div class="text-footer">
                <p><img src="img/fond_remove.png" alt="PACT" width="35px" height="35px"> propulsé par <img src="img/TripEnarvor.png" alt="TripEnarvor" width="35px" height="35px">© 2024 PACT LLC Tous droits réservés.</p>
                <p>Cette version de notre site internet s'adresse aux personnes parlant Français en France.</p>
            </div>
        </footer>
    </body>

</html>