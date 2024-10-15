<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION['day'] = $_POST['day'];
    $_SESSION['open_time'] = $_POST['open_time'];
    $_SESSION['close_time'] = $_POST['close_time'];
}

$selected_day = isset($_SESSION['day']) ? $_SESSION['day'] : '';
$open_time = isset($_SESSION['open_time']) ? $_SESSION['open_time'] : '06:00';
$close_time = isset($_SESSION['close_time']) ? $_SESSION['close_time'] : '12:00';
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles-test.css">
    <title>Détail Offres</title>
</head>
<body>
    <header>
        <img src="img/Menu.png" alt="image Menu" title="image menu">
        <img src="img/fond_remove.png" alt="logo site noir" title="logo site noir">
        <img src="img/User.png" alt="image user" title="image user" style="width: 30px; height: 30px;">
    </header>
    <main class="creer-offre-main">
    <form action="submit_offer.php" method="post" enctype="multipart/form-data">
            <h2>Nom de l'offre</h2>
            <input type="text" name="offer_name" placeholder="Cote de granite rose" required>

            <h2>Catégorie de l'offre</h2>
            <div class="categories">
                <button type="button" class="category">Restauration</button>
                <button type="button" class="category">Spectacles</button>
                <button type="button" class="category">Visites</button>
                <button type="button" class="category">Activités</button>
                <button type="button" class="category">Parcs d’attractions</button>
            </div>

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