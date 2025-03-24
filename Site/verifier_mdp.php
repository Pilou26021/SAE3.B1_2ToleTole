<?php
session_start();
include "../SQL/connection_local.php"; // Connexion à la BDD

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $passwordInput = $_POST["pswInput"] ?? '';

    // Récupération du hash du mot de passe de l'utilisateur
    $idcompte = $_SESSION['membre'] ?? $_SESSION['professionnel'] ?? null;

    if (!$idcompte) {
        echo "non";
        exit;
    }

    $sql = "SELECT hashmdpcompte FROM _compte WHERE idcompte = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':id', $idcompte, PDO::PARAM_INT);
    $stmt->execute();
    $resusu = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérification du mot de passe
    if ($resusu && password_verify($passwordInput, $resusu['hashmdpcompte'])) {
        echo "oui";
    } else {
        echo $resusu['hashmdpcompte'];
    }
}
?>
