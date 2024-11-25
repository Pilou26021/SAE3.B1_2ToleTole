<?php
error_reporting(E_ALL ^ E_WARNING);

include "../SQL/connection_local.php";
ob_start();
session_start();

try {
    $category = isset($_GET['category']) ? trim($_GET['category']) : '';
    $lieux = isset($_GET['lieux']) ? trim($_GET['lieux']) : '';
    $minPrice = isset($_GET['minPrice']) ? intval($_GET['minPrice']) : null;
    $maxPrice = isset($_GET['maxPrice']) ? intval($_GET['maxPrice']) : null;
    $noteMin = isset($_GET['notemin']) ? intval($_GET['notemin']) : 0;
    $noteMax = isset($_GET['notemax']) ? intval($_GET['notemax']) : 5;
    $Tprix = isset($_GET['Tprix']) ? $_GET['Tprix'] : '';
    $Tnote = isset($_GET['Tnote']) ? $_GET['Tnote'] : '';

    $sql = "
        SELECT o.idoffre, o.titreoffre, o.resumeoffre, o.prixminoffre, i.pathimage, o.horsligne, o.noteMoyenneOffre
        FROM public._offre o
        JOIN (
            SELECT idoffre, MIN(idimage) AS firstImage
            FROM public._afficherimageoffre
            GROUP BY idoffre
        ) a ON o.idoffre = a.idoffre
        JOIN public._image i ON a.firstImage = i.idimage
    ";

    $whereConditions = [];
    $bindings = [];

    if (!empty($category)) {
        $categoryMapping = [
            'Restauration' => '_offreRestaurant',
            'Spectacles' => '_offreSpectacle',
            'Visites' => '_offreVisite',
            'Activités' => '_offreActivite',
            'Parcs' => '_offreParcAttraction'
        ];

        if (array_key_exists($category, $categoryMapping)) {
            $sql .= " INNER JOIN public." . $categoryMapping[$category] . " o_cat ON o.idoffre = o_cat.idoffre";
        } else {
            throw new Exception('Invalid category');
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
        $whereConditions[] = "o.noteMoyenneOffre >= :noteMin";
        $bindings[':noteMin'] = $noteMin;
    }
    if ($noteMax < 5) {
        $whereConditions[] = "o.noteMoyenneOffre <= :noteMax";
        $bindings[':noteMax'] = $noteMax;
    }

    if (!empty($lieux)) {
        $sql .= " LEFT JOIN public.offreAdresse oa ON o.idoffre = oa.idOffre";
        $whereConditions[] = "LOWER(oa.ville) = LOWER(:lieux)";
        $bindings[':lieux'] = $lieux;
    }

    if (!empty($whereConditions)) {
        $sql .= " WHERE " . implode(" AND ", $whereConditions);
    }

    $orderBy = '';
    if ($Tprix === 'CroissantP') {
        $orderBy = 'o.prixminoffre ASC';
    } elseif ($Tprix === 'DecroissantP') {
        $orderBy = 'o.prixminoffre DESC';
    } elseif ($Tnote === 'CroissantN') {
        $orderBy = 'o.noteMoyenneOffre ASC';
    } elseif ($Tnote === 'DecroissantN') {
        $orderBy = 'o.noteMoyenneOffre DESC';
    }

    if (!empty($orderBy)) {
        $sql .= " ORDER BY $orderBy";
    }

    $stmt = $conn->prepare($sql);
    foreach ($bindings as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $offres = $stmt->fetchAll(PDO::FETCH_ASSOC);

    ob_end_clean();
    echo json_encode(['status' => 'success', 'data' => $offres]);

} catch (Exception $e) {
    ob_end_clean();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
