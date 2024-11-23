<?php
error_reporting(E_ALL ^ E_WARNING);
include "../SQL/connection_local.php";
ob_start();
session_start();

// Récupérer les paramètres des filtres
$category = isset($_GET['category']) ? $_GET['category'] : '';
$lieux = isset($_GET['lieux']) ? $_GET['lieux'] : '';
$maxPrice = isset($_GET['maxPrice']) ? intval($_GET['maxPrice']) : null;
$minPrice = isset($_GET['minPrice']) ? intval($_GET['minPrice']) : null;
$noteMin = isset($_GET['notemin']) ? intval($_GET['notemin']) : 0;
$noteMax = isset($_GET['notemax']) ? intval($_GET['notemax']) : 5;

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
        SELECT o.idoffre, o.titreoffre, o.resumeoffre, o.prixminoffre, i.pathimage, o.horsligne, o.noteMoyenneOffre
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
        SELECT o.idoffre, o.titreoffre, o.resumeoffre, o.prixminoffre, i.pathimage, o.horsligne, o.noteMoyenneOffre
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
if (!empty($lieux)) {
    $sql .= " AND LOWER(oa.ville) = LOWER(:lieux)";
    $bindings[':lieux'] = htmlspecialchars($lieux, ENT_QUOTES, 'UTF-8');
}

if (!is_null($maxPrice)) {
    $sql .= " AND o.prixminoffre <= :maxPrice";
    $bindings[':maxPrice'] = $maxPrice;
}

if (!is_null($minPrice)) {
    $sql .= " AND o.prixminoffre >= :minPrice";
    $bindings[':minPrice'] = $minPrice;
}

if (!is_null($noteMin)) {
    $sql .= " AND o.noteMoyenneOffre >= :noteMin";
    $bindings[':noteMin'] = $noteMin;
}

if (!is_null($noteMax)) {
    $sql .= " AND o.noteMoyenneOffre <= :noteMax";
    $bindings[':noteMax'] = $noteMax;
}

// Ajouter les conditions combinées dans la requête
if (count($whereConditions) > 0) {
    $sql .= " AND " . implode(" AND ", $whereConditions);
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
        <?php
        }
    }
} else {
    echo "Aucune offre trouvée.";
}
?>
