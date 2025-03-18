<?php
error_reporting(E_ALL ^ E_WARNING);
include "../SQL/connection_local.php";
ob_start();
session_start();

// Récupérer les paramètres des filtres
$category = isset($_GET['category']) ? $_GET['category'] : '';
$mavant = isset($_GET['mavant']) ? $_GET['mavant'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$lieux = isset($_GET['lieux']) ? $_GET['lieux'] : '';
$maxPrice = isset($_GET['maxPrice']) ? intval($_GET['maxPrice']) : null;
$minPrice = isset($_GET['minPrice']) ? intval($_GET['minPrice']) : null;
$noteMin = isset($_GET['notemin']) ? intval($_GET['notemin']) : 0;
$noteMax = isset($_GET['notemax']) ? intval($_GET['notemax']) : 5;
$Tprix = isset($_GET['Tprix']) ? $_GET['Tprix'] : '';
$Tnote = isset($_GET['Tnote']) ? $_GET['Tnote'] : '';
$Tdate = isset($_GET['Tdate']) ? $_GET['Tdate'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : '';
$ouvert = isset($_GET['ouvert']) ? $_GET['ouvert'] : '';
if ($ouvert == 1) {
    $ouvert = 'true';
} else {
    $ouvert = 'false';
}


// Initialiser les conditions de la requête
$tableJoin = '';
$whereConditions = [];  // On va stocker les conditions dans un tableau
$bindings = [];

$professionel = false;
$membre = false;

if (isset($_SESSION['membre'])) {
    $membre = true;
    $idmembre = $_SESSION['membre'];
} elseif (isset($_SESSION['professionnel'])) {
    $professionel = true;
    $idpro = $_SESSION['professionnel'];
}

// Ajouter le filtre de catégorie
if (!empty($category)) {
    switch ($category) {
        case 'Restauration':
            $tableJoin = 'INNER JOIN public._offreRestaurant o_r ON o.idoffre = o_r.idoffre';
            $whereConditions[] = 'o_r.idOffre IS NOT NULL';
            break;
        case 'Spectacles':
            $tableJoin = 'INNER JOIN public._offreSpectacle o_s ON o.idoffre = o_s.idoffre';
            $whereConditions[] = 'o_s.idOffre IS NOT NULL';
            break;
        case 'Visites':
            $tableJoin = 'INNER JOIN public._offreVisite o_v ON o.idoffre = o_v.idoffre';
            $whereConditions[] = 'o_v.idOffre IS NOT NULL';
            break;
        case 'Activités':
            $tableJoin = 'INNER JOIN public._offreActivite o_a ON o.idoffre = o_a.idoffre';
            $whereConditions[] = 'o_a.idOffre IS NOT NULL';
            break;
        case 'Parcs':
            $tableJoin = 'INNER JOIN public._offreParcAttraction o_p ON o.idoffre = o_p.idoffre';
            $whereConditions[] = 'o_p.idOffre IS NOT NULL';
            break;
    }
}

// Construire la requête SQL avec les filtres
if ($professionel) {
    // Si professionnel, n'afficher que ses offres
    $sql = "
        SELECT o.idoffre, o.titreoffre, o.resumeoffre, o.prixminoffre, i.pathimage, o.horsligne, o.notemoyenneoffre,o.enreliefoffre,o.alauneoffre,o.typeoffre
        FROM public._offre o
        JOIN (
            SELECT idoffre, MIN(idimage) AS firstImage
            FROM public._afficherimageoffre
            GROUP BY idoffre
        ) a ON o.idoffre = a.idoffre
        JOIN public._image i ON a.firstImage = i.idimage
        $tableJoin
        LEFT JOIN public.offreAdresse oa ON o.idoffre = oa.idOffre
        WHERE o.idProPropose = :idpro AND 1=1
    ";
    
} else {
    // Sinon, afficher toutes les offres pour les visiteurs/membres
    $sql = "
        SELECT o.idoffre, o.titreoffre, o.resumeoffre, o.prixminoffre, i.pathimage, o.horsligne, o.notemoyenneoffre,o.enreliefoffre,o.alauneoffre
        FROM public._offre o
        JOIN (
            SELECT idoffre, MIN(idimage) AS firstImage
            FROM public._afficherimageoffre
            GROUP BY idoffre
        ) a ON o.idoffre = a.idoffre
        JOIN public._image i ON a.firstImage = i.idimage
        $tableJoin
        LEFT JOIN public.offreAdresse oa ON o.idoffre = oa.idOffre
        WHERE 1=1
    ";
}


// Ajouter les conditions supplémentaires (filtrage par ville, prix, note)
if($ouvert == 'true') {

    $heure = $date = new DateTime('now', new DateTimeZone('Europe/Paris'));
    $heure = $date->format('Hi');

    $sql_horaires = "SELECT idoffre, horairesemaine FROM _offrerestaurant";
    $stmt = $conn->prepare($sql_horaires);
    $stmt->execute();
    $horaires = $stmt->fetchAll();
    $results = "";
    $nbr_restos_ouverts = 0;

    foreach($horaires as $horaire){
        $horaire_decoded = json_decode($horaire['horairesemaine'], true);

        // Convertir les heures pour être comparées
        $lunchOpen = str_replace(':', '', $horaire_decoded['lunchOpen']);
        $lunchClose = str_replace(':', '', $horaire_decoded['lunchClose']);
        $dinnerOpen = str_replace(':', '', $horaire_decoded['dinnerOpen']);
        $dinnerClose = str_replace(':', '', $horaire_decoded['dinnerClose']);

        if (($lunchOpen < $heure && $lunchClose > $heure) || ($dinnerOpen < $heure && $dinnerClose > $heure)) {
            $results .= strval($horaire['idoffre']) . ",";
        }
    }

    // Retirer la dernière virgule s'il y en a
    $results = rtrim($results, ',');

    if (!empty($results)) {
        $resultsArray = explode(',', $results);
        $placeholders = implode(',', array_map(function($key) { return ":id_$key"; }, array_keys($resultsArray)));
        $whereConditions[] = "o.idoffre IN ($placeholders)";

        // Lier les paramètres pour chaque id dans le tableau
        foreach ($resultsArray as $key => $value) {
            $bindings[":id_$key"] = intval($value);
        }
    } else {
        $whereConditions[] = "o.idoffre = -1";
    }
}

if (!is_null($minPrice)) {
    $whereConditions[] = "o.prixminoffre >= :minPrice";
    $bindings[':minPrice'] = $minPrice;
}
if (!is_null($maxPrice)) {
    $whereConditions[] = "o.prixminoffre <= :maxPrice";
    $bindings[':maxPrice'] = $maxPrice;
}

if ($noteMin > 0) {
    $whereConditions[] = "o.notemoyenneoffre >= :noteMin";
    $bindings[':noteMin'] = $noteMin;
}
if ($noteMax < 5) {
    $whereConditions[] = "o.notemoyenneoffre <= :noteMax";
    $bindings[':noteMax'] = $noteMax;
}

if (!empty($lieux)) {
    $whereConditions[] = "LOWER(oa.ville) = LOWER(:lieux)";
    $bindings[':lieux'] = $lieux;
}

if(!empty($startDate)) {
    $whereConditions[] = "o.dateCreationOffre >= :startDate";
    $bindings[":startDate"] = $startDate;
}

if (!empty($endDate)) {
    $whereConditions[] = "o.dateCreationOffre <= :endDate";
    $bindings[":endDate"] = $endDate;
}

if (!empty($mavant)) {
    switch ($mavant) {
        case 'Alaune':
            $whereConditions[] = 'o.alauneoffre = TRUE';
            break;
        case 'Relief':
            $whereConditions[] = 'o.enreliefoffre = TRUE ';
            break;
    }
}

if (!empty($search)) {
    $whereConditions[] = "(LOWER(o.titreoffre) LIKE LOWER(:search) 
                         OR LOWER(o.resumeoffre) LIKE LOWER(:search))
                         OR LOWER(oa.ville) = LOWER(:search)";
    $bindings[':search'] = '%' . $search . '%';
}


if (!empty($type)) {
    switch ($type) {
        case 'Standard':
            $whereConditions[] = 'o.typeoffre = 1';
            break;
        case 'Premium':
            $whereConditions[] = 'o.typeoffre = 2 ';
            break;
        case 'Gratuite':
            $whereConditions[] = 'o.typeoffre = 0 ';
            break;
    }
}
// Ajouter les conditions combinées dans la requête
if (count($whereConditions) > 0) {
    $sql .= " AND " . implode(" AND ", $whereConditions);
}

$orderBy = '';
if ($Tprix === 'CroissantP') {
    $orderBy = 'o.prixminoffre ASC';
} elseif ($Tprix === 'DecroissantP') {
    $orderBy = 'o.prixminoffre DESC';
} elseif ($Tprix === 'CroissantN') {
    $orderBy = 'o.notemoyenneoffre ASC';
} elseif ($Tprix === 'DecroissantN') {
    $orderBy = 'o.notemoyenneoffre DESC';
} elseif ($Tprix === 'Recent') {
    $orderBy = 'o.datecreationoffre DESC';
} elseif ($Tprix === 'Ancien') {
    $orderBy = 'o.datecreationoffre ASC';
}

if (!empty($orderBy)) {
    $sql .= " ORDER BY $orderBy";
}



// Préparer la requête
$stmt = $conn->prepare($sql);

if ($professionel) {
    $stmt->bindValue(':idpro', $idpro, PDO::PARAM_INT);  
}

// Lier les paramètres
foreach ($bindings as $key => $value) {
    $stmt->bindValue($key, $value);
}

// Exécuter la requête
$stmt->execute();

// Récupérer les résultats
$offres = $stmt->fetchAll();

// Afficher les offres filtrées
if (count($offres) > 0) {
    foreach ($offres as $offre) {
        if(!$professionel && $offre['horsligne'] == false || $professionel) {
        ?>
        <a style="text-decoration:none; color:#040316; font-family: regular;" href="details_offre.php?idoffre=<?php echo $offre['idoffre'];?>">
            <div class="offre-card" <?php if ($offre["enreliefoffre"]==true) { echo "style = 'border: 3px solid #36D673;' " ;} ?>>
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
                    <p class="offre-prix"><strong>Prix Minimum:</strong> <?= empty($offre['prixminoffre']) || $offre['prixminoffre'] <= 0 ? 'Gratuit' : $offre['prixminoffre'] . ' €' ?></p>

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
                        <a href="modifier_offre.php?idoffre=<?= $offre['idoffre'] ?>" class="bouton-modifier-offre">Modifier</a>
                        <a href="delete_offer.php?idoffre=<?= $offre['idoffre'] ?>" class="bouton-supprimer-offre">Supprimer</a>
                    <?php } ?>

                </div>
                
            </div>
        </a>
        <?php
        }
    }
} else {
    echo "Aucune offre trouvée.";
}
?>
