<?php
include "../SQL/connection_local.php";

// Récupérer les paramètres des filtres
$category = isset($_GET['category']) ? $_GET['category'] : '';
$lieux = isset($_GET['lieux']) ? $_GET['lieux'] : '';

// Initialiser la condition de la table à interroger
$tableJoin = '';
$whereCategory = '';
$whereLieux = '';

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
    LEFT JOIN public.offreadresse oa ON o.idoffre = oa.idoffre
    WHERE 1=1
    $whereCategory
";

// Ajouter le filtre sur la ville si `lieux` est défini
if (!empty($lieux)) {
    $sql .= " AND oa.ville LIKE :lieux";
}

// Préparer la requête
$stmt = $conn->prepare($sql);

// Exécuter la requête
$stmt->execute();


// Récupérer les résultats
$offres = $stmt->fetchAll();

// Afficher les offres filtrées
if (count($offres) > 0) {
    foreach ($offres as $offre) {
        ?>
        <div class="offre-card">
            <img src="<?= !empty($offre['pathimage']) ? htmlspecialchars($offre['pathimage']) : 'img/default.jpg' ?>" alt="Image de l'offre">
            <h2><?= htmlspecialchars($offre['titreoffre']) ?></h2>
            <p><?= htmlspecialchars($offre['resumeoffre']) ?></p>
            <p>Prix: <?= htmlspecialchars($offre['prixminoffre']) ?> €</p>
        </div>
        <?php
    }
} else {
    echo "Aucune offre trouvée.";
}
?>
