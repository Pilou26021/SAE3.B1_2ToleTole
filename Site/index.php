<?php 
    error_reporting(E_ALL ^ E_WARNING);
    include "header.php";
    ob_start();


?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
    <link rel="icon" href="./img/icons/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css">

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>

    
<style>
        
        #map_offres { 
            height: 500px; 
            width: 100%; 
            display : none;
            margin : 5%;
            border: black 2px solid;
            border-radius: 8px;
        }
    </style>
<title>Offres</title>
    <?php 
        include "../SQL/connection_local.php";

        $professionel = false;
        $membre = false;

        if (isset($_SESSION['membre'])) {
            $membre = true;
            $idmembre = $_SESSION['membre'];
        } elseif (isset($_SESSION['professionnel'])) {
            $professionel = true;
            $idpro = $_SESSION['professionnel'];
            $idProoffre = $_SESSION['idpro'];
        }

        // Construction de la requête SQL en fonction du type d'utilisateur
        if ($professionel) {
            // Si professionnel, n'afficher que ses offres
            $sql = "
                SELECT o.idOffre, o.titreOffre, o.resumeOffre, o.prixMinOffre, i.pathImage, o.horsligne , o.notemoyenneoffre,o.alauneoffre,o.enreliefoffre
                FROM public._offre o
                JOIN (
                    SELECT idOffre, MIN(idImage) AS firstImage
                    FROM public._afficherImageOffre
                    GROUP BY idOffre
                ) a ON o.idOffre = a.idOffre
                JOIN public._image i ON a.firstImage = i.idImage
                WHERE o.idProPropose = :idpro -- Correspond au professionnel
            ";
        } else {
            // Sinon, afficher toutes les offres pour les visiteurs/membres
            $sql = "
                SELECT o.idOffre, o.titreOffre, o.resumeOffre, o.prixMinOffre, i.pathImage, o.horsligne,o.notemoyenneoffre,o.alauneoffre,o.enreliefoffre
                FROM public._offre o
                JOIN (
                    SELECT idOffre, MIN(idImage) AS firstImage
                    FROM public._afficherImageOffre
                    GROUP BY idOffre
                ) a ON o.idOffre = a.idOffre
                JOIN public._image i ON a.firstImage = i.idImage
            ";
        }

        $sqlprixmin= "
                SELECT MIN(prixMinOffre) FROM public._offre";

        $sqlprixmax= "
                SELECT MAX(prixMinOffre) FROM public._offre";


        // Préparer et exécuter la requête
        $stmt = $conn->prepare($sql);
        $stmtmax = $conn->prepare($sqlprixmax);
        $stmtmin = $conn->prepare($sqlprixmin);

        if ($professionel) {
            $stmt->bindValue(':idpro', $idProoffre, PDO::PARAM_INT);  // Lier l'idPro si l'utilisateur est professionnel
        }

        $stmt->execute();
        $stmtmax->execute();
        $stmtmin->execute();
        $min = $stmtmin->fetchAll();
        $max = $stmtmax->fetchAll();
        $offres = $stmt->fetchAll();
    ?>
    <?php
        $style = "";
        if ($offre['enreliefoffre']==true) {
            if ($professionel) {
                $style = "style = 'border: 3px solid var(--color-accent-pro);'";
            } else {
                $style = "style = 'border: 3px solid #36D673;'";
            }
        }
    ?>
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

    <main>
        <?php if ($professionel): ?>
            <!-- Afficher un bouton de création d'offre pour les professionnels -->
            <a style="text-decoration:none;" href="creer_offre.php"> 
            <button class="offer-btn <?php echo $professionel ? 'professionnel' : ($membre ? 'membre' : 'guest'); ?>">
                <span class="icon <?php echo $professionel ? 'professionnel' : ($membre ? 'membre' : 'guest'); ?>">+</span> Créer une nouvelle offre
            </button> </a>
        <?php endif; ?>

        <div class="recherche">
            <form action="">
                <div class="recherche_top">
                    <img src="img/icons/search.png" alt="Search">
                    <input id="search-query" class="input" placeholder="Votre recherche" type="text" style="margin:auto 10px;">
                    <img src="img/icons/filtre.png" alt="Filtre" id="filterBtn">
                </div>
                <hr>
                <!-- <div>
                    <input class="button_1 <?php echo $professionel ? 'professionnel' : ($membre ? 'membre' : 'guest'); ?>" type="submit" value="Recherche" >
                </div> -->
            </form>
        </div>
        <div id="filterForm" class="filter-form">
            <span class="filter-close">×</span>
            <h3> Filtres</h3>
            <form action="#">
                <div class="container-category-ouvert">
                    <div class="container-category">
                        <label for="category">Catégorie :</label>
                        <select class="choosecat" id="category" name="category" onchange="showDateOuvert()">
                            <option value="">--Choisissez une option--</option>
                            <option value="Restauration">Restauration</option>
                            <option value="Spectacles">Spectacles</option>
                            <option value="Visites">Visites</option>
                            <option value="Activités">Activités</option>
                            <option value="Parcs">Parcs d’attractions</option>
                        </select>
                    </div>

                    <div class="container-ouvert checkbox-member">
                        <label for="ouvert" class="textedateouvert" id="textedateouvert" style="display:none;">Ouvert</label>
                        <label class="switch" id="switch" style="display:none;">
                            <input type="checkbox" id="ouvert" class="switch" name="ouvert" value="0" style="display:none;">
                            <span class="sliderswitch <?php if($professionel){echo "pro";} ?> "></span>
                        </label>
                        <script>
                            document.getElementById('ouvert').addEventListener('change', function() {
                                if (this.checked) {
                                    this.value = 1;
                                } else {
                                    this.value = 0;
                                }
                            });
                        </script>
                    </div>

                </div>

                <label for="Mavant">Mise en avant :</label>
                <select class="choose" id="Mavant" name="category">
                    <option value="">--Choisissez une option--</option>
                    <option value="Alaune">Á la Une</option>
                    <option value="Relief">En relief</option>
                </select>

                <?php 
                    if ($professionel) {
                ?>

                    <label for="type">Mes types d'offre :</label>
                    <select class="choose" id="type" name="category">
                        <option value="">--Choisissez une option--</option>
                        <option value="Standard">Standard</option>
                        <option value="Premium">Premium</option>
                        <option value="Gratuite">Gratuite</option>
                    </select>

                <?php
                    }else{
                ?>
                    <label style="display:none;" for="type">Mes types d'offre :</label>
                    <select style="display:none;" class="choose" id="type" name="category">
                        <option value="">--Choisissez une option--</option>
                    </select>
                <?php
                    } 
                ?>
                

                

                <label for="lieux">Lieux :</label>
                <div style="display: flex; align-items:center; justify-content: space-around;">
                    <input class="input-filtre" id="lieux" style="width: 60%;" type="text" placeholder="Lieux (Rennes) ">
                    <div class="slider-container" style="width: 30%;">
                        <div class="price-label">
                            Rayon: <span id="rayon-value">25</span>km
                        </div>
                        
                        <input type="range" id="rayon-range" class="slider" min="0" max="100" step="5" value="25">
                        
                        <div class="range-values">
                            <span class="range-value">0km</span>
                            <span class="range-value">100km</span>
                        </div>
                    </div>
                </div>

                <label for="datedeb">Date :</label>
                <div style="display: flex; align-items:center; justify-content: space-around;">
                    <input id="datedeb" class="input-filtre"  style="width: 40%;" type="date" >
                    <p>à</p>
                    <input id="datefin" class="input-filtre"  style="width: 40%;" type="date" >
                </div>
                

                <label for="price-range-min">Gamme de prix :</label>
                <div class="slider-container">
                    <div class="price-label">
                        Prix: <span id="price-value-min"><?php echo $min[0]['min']; ?></span>€ à <span id="price-value-max"><?php echo $max[0]['max']; ?></span>€
                    </div>
                    <input data-url="ajax_filtres.php" type="range" id="price-range-min" class="slider" min=<?php echo $min[0]['min']; ?> max=<?php echo $max[0]['max'] ; ?> step="10" value=<?php echo $min[0]['min']; ?>>
                    <input data-url="ajax_filtres.php" type="range" id="price-range-max" class="slider" min=<?php echo $min[0]['min']; ?> max=<?php echo $max[0]['max']+10 ; ?> step="10" value=<?php echo $max[0]['max']+10; ?>>

                    <div class="range-values">
                        <span class="range-value"><?php echo $min[0]['min']; ?> €</span>
                        <span class="range-value"><?php echo $max[0]['max']; ?>€</span>
                    </div>
                </div>


                <label for="notemin">Notes :</label>
                <div style="display: flex; justify-content: space-around;">
                    <select  class="choose" id="notemin" name="notemin" style="width: 30%; height: 30px;" data-url="ajax_filtres.php">
                        <option value="0" selected="selected">0 étoiles</option>
                        <option value="1">1 étoiles</option>
                        <option value="2">2 étoiles</option>
                        <option value="3">3 étoiles</option>
                        <option value="4">4 étoiles</option>
                        <option value="5">5 étoiles</option>
                    </select>
                    <p>à</p>
                    <select  class="choose" id="notemax" name="notemax" style="width: 30%; height: 30px;" data-url="ajax_filtres.php">
                        <option value="0">0 étoiles</option>
                        <option value="1">1 étoiles</option>
                        <option value="2">2 étoiles</option>
                        <option value="3">3 étoiles</option>
                        <option value="4">4 étoiles</option>
                        <option value="5" selected="selected">5 étoiles</option>
                    </select>
                </div>
                <h3>Tris</h3>

                <label for="Tprix">Tris :</label>
                <select class="choose" id="Tprix" name="Tprix">
                    <option value="">--Choisissez une option--</option>
                    <option value="CroissantP">Tri par Prix Croissant</option>
                    <option value="DecroissantP">Tri par Prix Decroissant</option>
                    <option value="CroissantN">Tri par Notes Croissante</option>
                    <option value="DecroissantN">Tri par Notes Decroissante</option>
                    <option value="Recent">Tri par Date la plus Recente</option>
                    <option value="Ancien">Tri par Date la plus Ancienne</option>
                </select>

                <div style="display: flex; justify-content: right; margin-bottom: 10px;">
                    <a href="index.php" class="button_2 <?php echo $professionel ? 'professionnel' : ($membre ? 'membre' : 'guest'); ?>">Reset</a>
                </div>

            </form>
        </div>
        
        <div id="map_offres"></div>

        <div class="offres-display">
            <?php if (count($offres) > 0): ?>
                    <?php 
                        $maxOffresU = 10; // Limite du nombre d'offres à afficher
                        $countU = 0;
                        $countalaUne = 0;
                        foreach ($offres as $offre) {
                            if ($offre['alauneoffre']==True) {
                                $countalaUne++;
                            }
                        }
                        if ($countalaUne != 0) {
                            # code... 
                    ?>
                    <?php if (!$professionel) { ?>    
                        <div style=" display:flex; justify-content:space-between; width:95%; align-items:center; ">
                            <h1>Offre à la Une </h1>
                            <a id="Alaune"  style="color:#040316; cursor: pointer; " > voir plus</a>
                        </div>
                        <div class="carousel-container" >
                            <div class="carousel" >
                                <?php foreach ($offres as $offre): 
                                    if ($countU >= $maxOffresU) {
                                        break; // Arrêter le traitement après 10 offres
                                    }
                                    // Style
                                    $style = "";
                                    if ($offre['enreliefoffre']==true) {
                                        if ($professionel) {
                                            $style = "style = 'border: 3px solid var(--color-accent-pro);'";
                                        } else {
                                            $style = "style = 'border: 3px solid #36D673;'";
                                        }
                                    }

                                    if(!$professionel && $offre['horsligne'] == false && $offre['alauneoffre']==True || $professionel && $offre['alauneoffre']==True ) { ?>
                                        <div class="slide">    
                                            <a style="text-decoration:none; " href="details_offre.php?idoffre=<?php echo $offre['idoffre'];?>">
                                                <div class="offre-card offer-alaune" <?php if ($offre["enreliefoffre"]==true) {echo $style;}?>>
                                                    <div class="offre-image-container" style="position: relative;">
                                                        <!-- Affichage de l'image -->
                                                        <img class="offre-image" src="<?= !empty($offre['pathimage']) ? htmlspecialchars($offre['pathimage']) : 'img/default.jpg' ?>" alt="Image de l'offre">
                                                        <?php if ($professionel && $offre['horsligne']) { ?>
                                                            <!-- Affichage de "Hors ligne" sur l'image si l'offre est hors ligne -->
                                                            <div class="offre-hors-ligne">Hors ligne</div>
                                                        <?php } ?>
                                                    </div>
                                                    <div class="offre-details">
                                                        <!-- Titre de l'offre -->
                                                        <h2 class="offre-titre-index"><?= !empty($offre['titreoffre']) ? htmlspecialchars($offre['titreoffre']) : 'Titre non disponible' ?></h2>
                                                        
                                                        <!-- Résumé de l'offre -->
                                                        <p class="offre-resume"><strong>Résumé:</strong> <?= !empty($offre['resumeoffre']) ? htmlspecialchars($offre['resumeoffre']) : 'Résumé non disponible' ?></p>
                                                        
                                                        <!-- Prix minimum de l'offre -->
                                                        <p class="offre-prix <?php echo $professionel ? 'professionnel' : ($membre ? 'membre' : 'guest'); ?>"><strong>Prix Minimum:</strong> <?= empty($offre['prixminoffre']) || $offre['prixminoffre'] <= 0 ? 'Gratuit' : $offre['prixminoffre'] . ' €' ?></p>

                                                        <div class="titre-moy-index">
                                                            <p class="offre-resume"> <strong> Note : </strong></p>
                                                            <div class="texte_note_etoiles_container">

                                                            <?php if(!empty($offre['notemoyenneoffre'])){
                                                                    $noteMoyenne = $offre['notemoyenneoffre'];

                                                                    // Calcul des étoiles pleines
                                                                    $etoilesCompletes = floor($noteMoyenne);  // on prend la partie entière de la moy
                                                                    if ($noteMoyenne - $etoilesCompletes > 0.705){
                                                                        $etoilesCompletes++;
                                                                    }
                                                                    for ($i = 0; $i < $etoilesCompletes; $i++) {
                                                                        ?> 
                                                                        <img src="./img/icons/star-solid.svg" alt="star checked" width="20" height="20">
                                                                        <?php
                                                                    }

                                                                    // si la partie décimale est supérieure ou égale à 0.3 et inferieure ou égale à 0.7-> une demi étoile
                                                                    if ($noteMoyenne - $etoilesCompletes >= 0.295 && $noteMoyenne - $etoilesCompletes <= 0.705) {
                                                                        ?> 
                                                                        <img src="./img/icons/star-half.svg" alt="half star checked" width="20" height="20"> 
                                                                        <?php
                                                                        $i++; // Compter cette demi-étoile
                                                                    }

                                                                    // Compléter avec les étoiles vides jusqu'à 5
                                                                    for (; $i < 5; $i++) {
                                                                        ?> 
                                                                        <img src="./img/icons/star-regular.svg" alt="star unchecked" width="20" height="20"> 
                                                                        <?php
                                                                    }

                                                                    ?><p class="nombre_note" ><?=$offre['notemoyenneoffre']?>/5</p><?php

                                                                } else {
                                                                    ?> <p>Pas d'évaluations</p><?php
                                                                }

                                                                ?>
                                                            </div>

                                                        </div>

                                                    <!-- bouton modifier offre seulement pour le professionel qui détient l'offre -->
                                                    <?php if ($professionel) { ?>
                                                            <a href="modifier_offre.php?idoffre=<?=$offre['idoffre']?>&origin=index" class="bouton-modifier-offre <?php echo $professionel ? 'professionnel' : ($membre ? 'membre' : 'guest'); ?>">Modifier</a>
                                                            <a href="delete_offer.php?idoffre=<?= $offre['idoffre'] ?>" class="bouton-supprimer-offre">Supprimer</a>
                                                        <?php } ?>

                                                    </div>
                                                </div>
                                            </a>
                                        </div> 
                                    <?php }  $countU++; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
                    <?php
                    if (!$professionel) {
                    ?>
                        <div style=" display:flex; justify-content:space-between; width:95%; align-items:center; ">
                            <h1>Nouveautés</h1>
                            <a id="Nouv"  style="color:#040316; cursor: pointer; " > voir plus</a>
                        </div>
                    <?php
                    } 
                    ?>

                <?php 
                    $maxOffresN = 5; // Limite du nombre d'offres à afficher
                    $countN = 0; 
                    $sqlN = "
                        SELECT o.idOffre, o.titreOffre, o.resumeOffre, o.prixMinOffre, i.pathImage, o.horsligne,o.notemoyenneoffre,o.alauneoffre,o.enreliefoffre,o.datecreationoffre
                        FROM public._offre o
                        JOIN (
                            SELECT idOffre, MIN(idImage) AS firstImage
                            FROM public._afficherImageOffre
                            GROUP BY idOffre
                        ) a ON o.idOffre = a.idOffre
                        JOIN public._image i ON a.firstImage = i.idImage
                        ORDER BY o.datecreationoffre DESC
                    ";
                    $stmtN = $conn->prepare($sqlN);
                    $stmtN->execute();
                    $offresN = $stmtN->fetchAll();
                ?>
                <div class="vertical-carousel" >
                    <?php foreach ($offresN as $offre):
                        if ($countN >= $maxOffresN) {
                            break; // Arrêter le traitement après 10 offres
                        }

                        // Style
                        $style = "";
                        if ($offre['enreliefoffre']==true) {
                            if ($professionel) {
                                $style = "style = 'border: 5px solid var(--color-accent-pro); border-radius: 10px;'";
                            } else {
                                $style = "style = 'border: 5px solid #36D673; border-radius: 15px;'";
                            }
                        }
                        
                        if(!$professionel && $offre['horsligne'] == false ) { ?>
                                <div class="offre-card-1" style=" position: relative; width: 100%; display:flex;  justify-content:center; margin: 40px auto;" >
                                    <a class="a-nouv" href="details_offre.php?idoffre=<?php echo $offre['idoffre'];?>" <?php if ($offre["enreliefoffre"]==true) {echo $style;} ?>>
                                        <img class="offre-image-1" src="<?= !empty($offre['pathimage']) ? htmlspecialchars($offre['pathimage']) : 'img/default.jpg' ?>" alt="Image de l'offre">
                                        <div class="offres-details-1">
                                            <h2 class="offre-titre-index-1"><?= !empty($offre['titreoffre']) ? htmlspecialchars($offre['titreoffre']) : 'Titre non disponible' ?></h2>
                                            <!-- Résumé de l'offre -->
                                            <p class="offre-resume-1"><strong>Résumé:</strong> <?= !empty($offre['resumeoffre']) ? htmlspecialchars($offre['resumeoffre']) : 'Résumé non disponible' ?></p>
                                                        
                                            <!-- Prix minimum de l'offre -->
                                            <p class="offre-prix-1 <?php echo $professionel ? 'professionnel' : ($membre ? 'membre' : 'guest'); ?>"><strong>Prix Minimum:</strong> <?= empty($offre['prixminoffre']) || $offre['prixminoffre'] <= 0 ? 'Gratuit' : $offre['prixminoffre'] . ' €' ?></p>

                                            <div class="titre-moy-index-1">
                                                <p class="offre-resume-1"> <strong> Note : </strong></p>
                                                <div class="texte_note_etoiles_container">

                                                <?php if(!empty($offre['notemoyenneoffre'])){
                                                        $noteMoyenne = $offre['notemoyenneoffre'];

                                                        // Calcul des étoiles pleines
                                                        $etoilesCompletes = floor($noteMoyenne);  // on prend la partie entière de la moy
                                                        if ($noteMoyenne - $etoilesCompletes > 0.705){
                                                            $etoilesCompletes++;
                                                        }
                                                        for ($i = 0; $i < $etoilesCompletes; $i++) {
                                                            ?> 
                                                            <img src="./img/icons/star-solid.svg" alt="star checked" width="40" height="40">
                                                            <?php
                                                        }

                                                        // si la partie décimale est supérieure ou égale à 0.3 et inferieure ou égale à 0.7-> une demi étoile
                                                        if ($noteMoyenne - $etoilesCompletes >= 0.295 && $noteMoyenne - $etoilesCompletes <= 0.705) {
                                                            ?> 
                                                            <img src="./img/icons/star-half.svg" alt="half star checked" width="40" height="40"> 
                                                            <?php
                                                            $i++; // Compter cette demi-étoile
                                                        }

                                                        // Compléter avec les étoiles vides jusqu'à 5
                                                        for (; $i < 5; $i++) {
                                                            ?> 
                                                            <img src="./img/icons/star-regular.svg" alt="star unchecked" width="40" height="40"> 
                                                            <?php
                                                        }

                                                        ?><p class="nombre_note" style="font-size:1.5em;" ><?=$offre['notemoyenneoffre']?>/5</p><?php

                                                    } else {
                                                        ?> <p style="font-size:1.5em;">Pas d'évaluations</p><?php
                                                    }

                                                    ?>
                                                </div>
                                            </div>
                                            <p class="offre-date-1"><strong>Date:</strong> <?= !empty($offre['datecreationoffre']) ? htmlspecialchars($offre['datecreationoffre']) : 'Date non disponible' ?></p>
                                        </div>
                                    </a>
                                </div>
                               
                        <?php } 
                        $countN++;
                        ?>
                    <?php endforeach; ?>
                </div>
                
                <div style="padding: 20px 0;" >
                    <h1>Toutes les Offres</h1>
                </div>
                <div class="offres-container">
                    <?php foreach ($offres as $offre): ?>
                        <?php
                            $style = "";
                            if ($offre['enreliefoffre']==true) {
                                if ($professionel) {
                                    $style = "style = 'border: 3px solid var(--color-accent-pro);'";
                                } else {
                                    $style = "style = 'border: 3px solid #36D673;'";
                                }
                            }
                        ?>
                        <?php if(!$professionel && $offre['horsligne'] == false || $professionel) { ?>
                            <a style="text-decoration:none;" href="details_offre.php?idoffre=<?php echo $offre['idoffre'];?>">
                                <div class="offre-card" <?php if ($offre["enreliefoffre"]==true) {echo $style;} ?>>
                                    <div class="offre-image-container" style="position: relative;">
                                        <!-- Affichage de l'image -->
                                        <img class="offre-image" src="<?= !empty($offre['pathimage']) ? htmlspecialchars($offre['pathimage']) : 'img/default.jpg' ?>" alt="Image de l'offre">
                                        <?php if ($professionel && $offre['horsligne']) { ?>
                                            <!-- Affichage de "Hors ligne" sur l'image si l'offre est hors ligne -->
                                            <div class="offre-hors-ligne">Hors ligne</div>
                                        <?php } ?>
                                    </div>
                                    <div class="offre-details">
                                        <!-- Titre de l'offre -->
                                        <h2 class="offre-titre-index"><?= !empty($offre['titreoffre']) ? htmlspecialchars($offre['titreoffre']) : 'Titre non disponible' ?></h2>
                                        
                                        <!-- Résumé de l'offre -->
                                        <p class="offre-resume"><strong>Résumé:</strong> <?= !empty($offre['resumeoffre']) ? htmlspecialchars($offre['resumeoffre']) : 'Résumé non disponible' ?></p>
                                        
                                        <!-- Prix minimum de l'offre -->
                                        <p class="offre-prix <?php echo $professionel ? 'professionnel' : ($membre ? 'membre' : 'guest'); ?>"><strong>Prix Minimum:</strong> <?= empty($offre['prixminoffre']) || $offre['prixminoffre'] <= 0 ? 'Gratuit' : $offre['prixminoffre'] . ' €' ?></p>

                                        <div class="titre-moy-index">
                                            <p class="offre-resume"> <strong> Note :</strong></p>
                                            <div class="texte_note_etoiles_container">
                                            <?php if(!empty($offre['notemoyenneoffre'])){
                                                    $noteMoyenne = $offre['notemoyenneoffre'];

                                                    // Calcul des étoiles pleines
                                                    $etoilesCompletes = floor($noteMoyenne);  // on prend la partie entière de la moy
                                                    if ($noteMoyenne - $etoilesCompletes > 0.705){
                                                        $etoilesCompletes++;
                                                    }
                                                    for ($i = 0; $i < $etoilesCompletes; $i++) {
                                                        ?> 
                                                        <img src="./img/icons/star-solid.svg" alt="star checked" width="20" height="20">
                                                        <?php
                                                    }

                                                    // si la partie décimale est supérieure ou égale à 0.3 et inferieure ou égale à 0.7-> une demi étoile
                                                    if ($noteMoyenne - $etoilesCompletes >= 0.295 && $noteMoyenne - $etoilesCompletes <= 0.705) {
                                                        ?> 
                                                        <img src="./img/icons/star-half.svg" alt="half star checked" width="20" height="20"> 
                                                        <?php
                                                        $i++; // Compter cette demi-étoile
                                                    }

                                                    // Compléter avec les étoiles vides jusqu'à 5
                                                    for (; $i < 5; $i++) {
                                                        ?> 
                                                        <img src="./img/icons/star-regular.svg" alt="star unchecked" width="20" height="20"> 
                                                        <?php
                                                    }

                                                    ?><p class="nombre_note"><?= $offre['notemoyenneoffre']?>/5</p><?php

                                                } else {
                                                    ?> <p>Pas d'évaluations</p><?php
                                                }

                                                ?>
                                            </div>

                                        </div>

                                       <!-- bouton modifier offre seulement pour le professionel qui détient l'offre -->
                                       <?php if ($professionel) { ?>
                                            <a href="modifier_offre.php?idoffre=<?=$offre['idoffre']?>&origin=index" class="bouton-modifier-offre <?php echo $professionel ? 'professionnel' : ($membre ? 'membre' : 'guest'); ?>">Modifier</a>
                                            <a href="delete_offer.php?idoffre=<?= $offre['idoffre'] ?>" class="bouton-supprimer-offre">Supprimer</a>
                                        <?php } ?>

                                    </div>
                                </div>
                            </a>
                        <?php } ?>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>Aucune offre disponible pour le moment.</p>
            <?php endif; ?>
        </div>
    </main>
    
    <div id="footer"></div>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>
    <script src="script.js"></script> 
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    

</body>
</html>
