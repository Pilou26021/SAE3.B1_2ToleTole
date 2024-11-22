<?php 
include "header.php";
ob_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
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
        }

        // Construction de la requête SQL en fonction du type d'utilisateur
        if ($professionel) {
            // Si professionnel, n'afficher que ses offres
            $sql = "
                SELECT o.idOffre, o.titreOffre, o.resumeOffre, o.prixMinOffre, i.pathImage, o.horsligne
                FROM public._offre o
                JOIN (
                    SELECT idOffre, MIN(idImage) AS firstImage
                    FROM public._afficherImageOffre
                    GROUP BY idOffre
                ) a ON o.idOffre = a.idOffre
                JOIN public._image i ON a.firstImage = i.idImage
                WHERE o.idProPropose = :idpro -- Correspond au professionnel
                ORDER BY o.idOffre
            ";
        } else {
            // Sinon, afficher toutes les offres pour les visiteurs/membres
            $sql = "
                SELECT o.idOffre, o.titreOffre, o.resumeOffre, o.prixMinOffre, i.pathImage, o.horsligne
                FROM public._offre o
                JOIN (
                    SELECT idOffre, MIN(idImage) AS firstImage
                    FROM public._afficherImageOffre
                    GROUP BY idOffre
                ) a ON o.idOffre = a.idOffre
                JOIN public._image i ON a.firstImage = i.idImage
                ORDER BY o.idOffre
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
            $stmt->bindValue(':idpro', $idpro, PDO::PARAM_INT);  // Lier l'idPro si l'utilisateur est professionnel
        }

        $stmt->execute();
        $stmtmax->execute();
        $stmtmin->execute();
        $min = $stmtmin->fetchAll();
        $max = $stmtmax->fetchAll();
        $offres = $stmt->fetchAll();
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
            <button class="offer-btn">
                <span class="icon">+</span> Créer une nouvelle offre
            </button> </a>
        <?php endif; ?>

        <div class="recherche">
            <form action="">
                <div class="recherche_top">
                    <img src="img/icons/search.png" alt="Search">
                    <input class="input" placeholder="Votre recherche" type="text">
                    <img src="img/icons/filtre.png" alt="Filtre" id="filterBtn">
                </div>
                <hr>
                <div>
                    <input class="button_1" type="submit" value="Recherche" >
                </div>
            </form>
        </div>
        <div id="filterForm" class="filter-form">
            <h3> Filtres</h3>
            <form action="#">
                <label for="category">Catégorie :</label>
                <select class="choose" id="category" name="category">
                    <option value="">--Choisissez une option--</option>
                    <option value="Restauration">Restauration</option>
                    <option value="Spectacles">Spectacles</option>
                    <option value="Visites">Visites</option>
                    <option value="Activités">Activités</option>
                    <option value="Parcs">Parcs d’attractions</option>
                </select>

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

                <label for="date">Date :</label>
                <div style="display: flex; align-items:center; justify-content: space-around;">
                    <input id="datedeb" class="input-filtre"  style="width: 40%;" type="date" >
                    <input id="datefin" class="input-filtre"  style="width: 40%;" type="date" >
                </div>

                <label for="priceRange">Gamme de prix :</label>
                <div class="slider-container">
                    <div class="price-label">
                        Prix: <span id="price-value-min"><?php echo $min[0]['min']; ?></span>€ a <span id="price-value-max"><?php echo $max[0]['max']; ?></span>€
                    </div>
                    <input data-url="ajax_filtres.php" type="range" id="price-range-min" class="slider" min=<?php echo $min[0]['min']; ?> max=<?php echo $max[0]['max'] ; ?> step="10" value=<?php echo $min[0]['min']; ?>>
                    <input data-url="ajax_filtres.php" type="range" id="price-range-max" class="slider" min=<?php echo $min[0]['min']; ?> max=<?php echo $max[0]['max'] ; ?> step="10" value=<?php echo $max[0]['max']; ?>>

                    <div class="range-values">
                        <span class="range-value"><?php echo $min[0]['min']; ?> €</span>
                        <span class="range-value"><?php echo $max[0]['max']; ?>€</span>
                    </div>
                </div>


                <label for="sort">Notes :</label>
                <div style="display: flex; justify-content: space-around;">
                    <select  class="choose" id="notemin" name="notemin" style="width: 30%; height: 30px;">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                    <p>a</p>
                    <select  class="choose" id="notemax" name="notemax" style="width: 30%; height: 30px;">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </div>
                <h3>Tri</h3>

                <label for="TrieC">Tri par Prix  :</label>
                <select class="choose" id="Tprix" name="Tprix">
                    <option value="">--Choisissez une option--</option>
                    <option value="CroissantP">Tri par ordre Croissant</option>
                    <option value="DecroissantP">Tri par ordre Decroissant</option>
                </select>

                <label for="TrieC">Tri par Notes  :</label>
                <select class="choose" id="Tnote" name="Tnote">
                    <option value="">--Choisissez une option--</option>
                    <option value="CroissantN">Tri par ordre Croissant</option>
                    <option value="DecroissantN">Tri par ordre Decroissant</option>
                </select>

            </form>
        </div>

        <div class="offres-display">
            <?php if (count($offres) > 0): ?>
                <div class="offres-container">
                    <?php foreach ($offres as $offre): ?>
                        <?php if(!$professionel && $offre['horsligne'] == false || $professionel) { ?>
                            <a style="text-decoration:none;" href="details_offre.php?idoffre=<?php echo $offre['idoffre'];?>">
                                <div class="offre-card">
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
                                        <h2 class="offre-titre"><?= !empty($offre['titreoffre']) ? htmlspecialchars($offre['titreoffre']) : 'Titre non disponible' ?></h2>
                                        
                                        <!-- Résumé de l'offre -->
                                        <p class="offre-resume"><strong>Résumé:</strong> <?= !empty($offre['resumeoffre']) ? htmlspecialchars($offre['resumeoffre']) : 'Résumé non disponible' ?></p>
                                        
                                        <!-- Prix minimum de l'offre -->
                                        <p class="offre-prix"><strong>Prix Minimum:</strong> <?= !empty($offre['prixminoffre']) ? htmlspecialchars($offre['prixminoffre']) : 'Prix non disponible' ?> €</p>
                                       
                                       <!-- bouton modifier offre seulement pour le professionel qui détient l'offre -->
                                       <?php if ($professionel) { ?>
                                            <a href="modifier_offre.php?idoffre=<?= $offre['idoffre'] ?>" class="bouton-modifier-offre">Modifier</a>
                                            <a href="supprimer_offre.php?idoffre=<?= $offre['idoffre'] ?>" class="bouton-supprimer-offre">Supprimer</a>
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

    <script src="script.js"></script> 
</body>
</html>
