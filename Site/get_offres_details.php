<?php
// Connexion à la base de données
include "../SQL/connection_local.php";

// Récupérer les données envoyées en JSON par JavaScript
$data = json_decode(file_get_contents('php://input'), true);
$offerIds = $data['offerIds'] ?? [];

if (empty($offerIds)) {
    echo json_encode([]);
    exit;
}

// Préparer la requête SQL pour récupérer les détails des offres
$placeholders = implode(',', array_fill(0, count($offerIds), '?')); // Créer des placeholders pour les IDs
$sql = "
    SELECT o.idOffre, o.titreOffre, o.resumeOffre, o.prixMinOffre, i.pathImage, o.horsligne, o.notemoyenneoffre, o.alauneoffre, o.enreliefoffre, o.datecreationoffre
    FROM public._offre o
    JOIN (
        SELECT idOffre, MIN(idImage) AS firstImage
        FROM public._afficherImageOffre
        GROUP BY idOffre
    ) a ON o.idOffre = a.idOffre
    JOIN public._image i ON a.firstImage = i.idImage
    WHERE o.idOffre IN ($placeholders)
    ORDER BY array_position(ARRAY[" . implode(',', $offerIds) . "], o.idOffre)
";

// Préparer et exécuter la requête
$stmt = $conn->prepare($sql);
$stmt->execute($offerIds);

// Récupérer les résultats
$offres = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Retourner les offres en JSON
echo json_encode($offres);
?>