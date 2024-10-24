<?php
    error_reporting(E_ALL ^ E_WARNING);
    ob_start();
    session_start();

    // Inclusion du fichier de connexion à la base de données
    include('../SQL/connection_local.php');

    // Vérification si l'utilisateur est connecté et que l'offre lui appartient
    if (!isset($_SESSION['professionnel'])) {
        echo "<script>window.location.replace('index.php');</script>";
        exit();
    }

    // Récupération de l'ID de l'offre à modifier depuis l'URL
    if (isset($_GET['idoffre'])) {
        $idOffre = $_GET['idoffre'];

        // Récupération des détails de l'offre
        $stmt = $conn->prepare("SELECT * FROM public._offre WHERE idOffre = :idOffre");
        $stmt->execute([':idOffre' => $idOffre]);
        $offre = $stmt->fetch();

        // Récupération des détails dans sa catégorie
        $themeoffre = 0;

        if (!$offre) {
            echo "<script>window.location.replace('index.php');</script>";
            exit();
        }
    } else {
        echo "<script>window.location.replace('index.php');</script>";
        exit();
    }

    // Mise à jour de l'offre
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $titreoffre = $_POST['titreoffre'];
        $resumeoffre = $_POST['resumeoffre'];
        $descriptionoffre = $_POST['descriptionoffre'];
        $prixminoffre = $_POST['prixminoffre'];
        $alauneoffre = $_POST['alauneoffre'];
        $enreliefoffre = $_POST['enreliefoffre'];
        $typeoffre = $_POST['typeoffre'];
        $siteweboffre = $_POST['siteweboffre'];
        $conditionAccessibilite = $_POST['conditionAccessibilite'];
        $horsligne = $_POST['horsLigne'];
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
    <title>Modifier l'offre</title>
</head>
<body>
    <div id="header"></div>
    <main>
        <h1>Modifier l'offre</h1>
        <form method="POST">
            <label for="offerName">Nom de l'offre :</label>
            <input type="text" name="offerName" value="<?= htmlspecialchars($offre['offerName']) ?>" required><br>

            <label for="summary">Résumé :</label>
            <input type="text" name="summary" value="<?= htmlspecialchars($offre['summary']) ?>" required><br>

            <label for="description">Description :</label>
            <textarea name="description" required><?= htmlspecialchars($offre['description']) ?></textarea><br>

            <label for="minPrice">Prix minimum :</label>
            <input type="number" name="minPrice" value="<?= htmlspecialchars($offre['minPrice']) ?>" required><br>

            <label for="adultPrice">Prix adulte :</label>
            <input type="number" name="adultPrice" value="<?= htmlspecialchars($offre['adultPrice']) ?>" required><br>

            <label for="childPrice">Prix enfant :</label>
            <input type="number" name="childPrice" value="<?= htmlspecialchars($offre['childPrice']) ?>" required><br>

            <label for="dateOffre">Date :</label>
            <input type="date" name="dateOffre" value="<?= htmlspecialchars($offre['dateOffre']) ?>" required><br>

            <label for="typeOffre">Type d'offre :</label>
            <input type="text" name="typeOffre" value="<?= htmlspecialchars($offre['typeOffre']) ?>" required><br>

            <label for="conditionAccessibilite">Condition d'accessibilité :</label>
            <textarea name="conditionAccessibilite" required><?= htmlspecialchars($offre['conditionAccessibilite']) ?></textarea><br>

            <button type="submit">Mettre à jour</button>
        </form>
    </main>
    <div id="footer"></div>
</body>
</html>
