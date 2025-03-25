<?php
    session_start();

    include "../SQL/connection_local.php";

    // On récupère l'id du compte
    $idcompte = $_SESSION['membre'] ?? $_SESSION['professionnel'] ?? null;        

    // On récupère le mot de passe lié au compte
    $requeteSql = "SELECT hashmdpcompte
                    FROM _compte
                    WHERE idcompte = :id;";

    $executionRequete = $conn->prepare($requeteSql);
    $executionRequete->bindValue(':id', $idcompte, PDO::PARAM_INT);
    $executionRequete->execute();
    $resultat = $executionRequete -> fetch();

    // On vérifie que le mot de passe saisi et le vrai mot de passe correspondent, et on renvoie une réponse en conséquence
    if ($_SERVER["REQUEST_METHOD"] == "POST"){

        $mdpSaisi = htmlspecialchars($_POST['mdp']);

        if (password_verify($mdpSaisi, $resultat[0])){
            echo "MotDePasseValide";
        }
        else{
            echo "MotDePasseNonvalide";
        }
    }
?>


