<?php 
    include "header.php";
    ob_start();
    include "./SQL/connection_local.php";

    $professionel = false;
    $membre = false;
    if (isset($_SESSION['membre'])) {
        $membre = true;
        $idmembre = $_SESSION['membre'];
    } elseif (isset($_SESSION['professionnel'])) {
        $professionel = true;
        $idpro = $_SESSION['professionnel'];
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
    <title>Détails de l'Offre</title>
</head>
<body>
    <?php 

        // Vérification de l'ID de l'offre dans l'URL
        if (isset($_GET['idoffre'])) {
            $idoffre = intval($_GET['idoffre']);

            // Requête SQL pour récupérer les détails de l'offre
            $sql = "
                SELECT o.idoffre, o.titreoffre, o.resumeoffre, o.prixminoffre, o.horsligne, i.pathimage
                FROM public._offre o
                JOIN (
                    SELECT idoffre, MIN(idImage) AS firstImage
                    FROM public._afficherImageOffre
                    GROUP BY idoffre
                ) a ON o.idoffre = a.idoffre
                JOIN public._image i ON a.firstImage = i.idImage
                WHERE o.idoffre = :idoffre
            ";

            // Préparer et exécuter la requête
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':idoffre', $idoffre, PDO::PARAM_INT);
            $stmt->execute();

            // Récupérer les détails de l'offre
            $offre = $stmt->fetch();
        } else {
            // Redirection si l'ID de l'offre n'est pas fourni
            header("Location: index.php");
            exit();
        }

        // Traitement pour passer l'offre hors ligne
        if (isset($_POST['horsligne'])) {
            $updateSql = "UPDATE public._offre SET horsligne = true WHERE idoffre = :idoffre";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bindValue(':idoffre', $idoffre, PDO::PARAM_INT);
            $updateStmt->execute();

            // Redirection après la mise à jour
            header("Location: details_offre.php?idoffre=" . $idoffre);
            exit();
        }

        // Traitement pour remettre l'offre en ligne
        if (isset($_POST['remettre_en_ligne'])) {
            $updateSql = "UPDATE public._offre SET horsligne = false WHERE idoffre = :idoffre";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bindValue(':idoffre', $idoffre, PDO::PARAM_INT);
            $updateStmt->execute();

            // Redirection après la mise à jour
            header("Location: details_offre.php?idoffre=" . $idoffre);
            exit();
        }
    ?>
    
    <main>
        <?php if ($offre): ?>
            <div class="offre-detail-container">
                <h1 class="offre-titre"><?= htmlspecialchars($offre['titreoffre']) ?></h1>
                <div class="offre-image-container">
                    <img class="offre-image" src="<?= !empty($offre['pathimage']) ? htmlspecialchars($offre['pathimage']) : 'img/default.jpg' ?>" alt="Image de l'offre">
                </div>
                <p class="offre-resume"><strong>Résumé:</strong> <?= htmlspecialchars($offre['resumeoffre']) ?></p>
                <p class="offre-prix"><strong>Prix Minimum:</strong> <?= htmlspecialchars($offre['prixminoffre']) ?> €</p>

                <?php if ($professionel) { ?>
                    <!-- Bouton pour passer l'offre hors ligne ou remettre en ligne -->
                    <?php if ($offre['horsligne']) { ?>
                        <form style="display:flex;justify-content:center;" method="POST" action="">
                            <input type="hidden" name="remettre_en_ligne" value="true">
                            <button type="submit" class="offer-btn" onclick="return confirm('Êtes-vous sûr de vouloir remettre cette offre en ligne ?');">
                                Remettre l'offre en ligne
                            </button>
                        </form>
                    <?php } else { ?>
                        <form style="display:flex;justify-content:center;" method="POST" action="">
                            <input type="hidden" name="horsligne" value="true">
                            <button type="submit" class="offer-btn" onclick="return confirm('Êtes-vous sûr de vouloir passer cette offre hors ligne ?');">
                                Passer l'offre hors ligne
                            </button>
                        </form>
                    <?php } ?>
                <?php } ?>

                <div style="display:flex;justify-content:center;">
                    <a style="text-decoration:none;" href="index.php"> <button class="offer-btn">Retour aux offres</button></a>
                </div>

            </div>
        <?php else: ?>
            <p>Détails de l'offre non disponibles.</p>
        <?php endif; ?>
    </main>

    <div id="footer"></div>

    <script src="script.js"></script> 
</body>
</html>
