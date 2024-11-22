<?php
include "../SQL/connection_local.php";

// Récupérer les paramètres des filtres
$category = isset($_GET['category']) ? $_GET['category'] : '';
$lieux = isset($_GET['lieux']) ? $_GET['lieux'] : '';
$maxPrice = isset($_GET['maxPrice']) ? intval($_GET['maxPrice']) : null;
$minPrice = isset($_GET['minPrice']) ? intval($_GET['minPrice']) : null;
$noteMin = isset($_GET['notemin']) ? intval($_GET['notemin']) : 0;
$noteMax = isset($_GET['notemax']) ? intval($_GET['notemax']) : 5;

// Initialiser la condition de la table à interroger
$tableJoin = '';
$whereCategory = '';


switch ($category) {
    case 'Restauration':
        $tableJoin = 'INNER JOIN public._offreRestaurant o_r ON o.idoffre = o_r.idoffre';
        $whereCategory = ' AND o_r.idOffre IS NOT NULL';  // Cela s'assure que la catégorie correspond à une offre dans _offreRestaurant
        break;
    case 'Spectacles':
        $tableJoin = 'INNER JOIN public._offreSpectacle o_s ON o.idoffre = o_s.idoffre';
        $whereCategory = ' AND o_s.idOffre IS NOT NULL';
        break;
    case 'Visites':
        $tableJoin = 'INNER JOIN public._offreVisite o_v ON o.idoffre = o_v.idoffre';
        $whereCategory = ' AND o_v.idOffre IS NOT NULL';
        break;
    case 'Activités':
        $tableJoin = 'INNER JOIN public._offreActivite o_a ON o.idoffre = o_a.idoffre';
        $whereCategory = ' AND o_a.idOffre IS NOT NULL';
        break;
    case 'Parcs':
        $tableJoin = 'INNER JOIN public._offreParcAttraction o_p ON o.idoffre = o_p.idoffre';
        $whereCategory = ' AND o_p.idOffre IS NOT NULL';
        break;
    default:
        // Si aucune catégorie, on ne joint pas de table spécifique
        $tableJoin = '';
        $whereCategory = '';
}


// Construire la requête SQL avec les filtres
$sql = "
    SELECT o.idoffre, o.titreoffre, o.resumeoffre, o.prixminoffre, i.pathimage, o.horsligne
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
    $whereCategory
    AND o.noteMoyenneOffre BETWEEN :notemin AND :notemax
";


// Ajouter le filtre sur la ville si `lieux` est défini
if (!empty($lieux)) {
    $sql .= " AND LOWER(oa.ville) = LOWER(:lieux)";
    // OR LOWER(oa.departement) = LOWER(:lieux) OR LOWER(oa.pays) = LOWER(:lieux))
}

if (!is_null($maxPrice)) {
    $sql .= " AND o.prixminoffre <= :maxPrice";
}
if (!is_null($minPrice)) {
    $sql .= " AND o.prixminoffre >= :minPrice";
}

// Préparer la requête
$stmt = $conn->prepare($sql);

$stmt->bindValue(":notemin", $noteMin, PDO::PARAM_INT);
$stmt->bindValue(":notemax", $noteMax, PDO::PARAM_INT);

if (!is_null($maxPrice)) {
    $stmt->bindValue(":maxPrice", $maxPrice, PDO::PARAM_INT);
}
if (!is_null($minPrice)) {
    $stmt->bindValue(":minPrice", $minPrice, PDO::PARAM_INT);
}

if (!empty($lieux)) {
    // Assurez-vous que la variable $lieux est correctement échappée et liée
    $lieux = htmlspecialchars($lieux, ENT_QUOTES, 'UTF-8'); // Protéger la valeur pour éviter les injections SQL
    $stmt->bindValue(':lieux', $lieux, PDO::PARAM_STR); // Lier la variable `lieux`
}

// Exécuter la requête
$stmt->execute();


// Récupérer les résultats
$offres = $stmt->fetchAll();

// Afficher les offres filtrées
if (count($offres) > 0) {
    foreach ($offres as $offre) {
        ?>
        <a style="text-decoration:none; color:#040316; font-family: regular;" href="details_offre.php?idoffre=<?php echo $offre['idoffre'];?>">
            <div class="offre-card">
                <img src="<?= !empty($offre['pathimage']) ? htmlspecialchars($offre['pathimage']) : 'img/default.jpg' ?>" alt="Image de l'offre">
                <h2><?= htmlspecialchars($offre['titreoffre']) ?></h2>
                <p><?= htmlspecialchars($offre['resumeoffre']) ?></p>
                <p>Prix: <?= htmlspecialchars($offre['prixminoffre']) ?> €</p>
            </div>
        </a>
        <?php
    }
} else {
    echo "Aucune offre trouvée.";
}
?>
