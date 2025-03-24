<?php
session_start(); // Démarrer la session au tout début

include "../SQL/connection_local.php";

$idcompte = $_SESSION['membre'] ?? $_SESSION['professionnel'] ?? null;        

//Récupération du mot de passe
$requeteSql7 = "SELECT hashmdpcompte
                FROM _compte
                WHERE idcompte = :id;";

$executionRequete7 = $conn->prepare($requeteSql7);
$executionRequete7->bindValue(':id', $idcompte, PDO::PARAM_INT);
$executionRequete7->execute();
$resusu = $executionRequete7 -> fetch();


if ($_SERVER["REQUEST_METHOD"] == "POST"){

    echo $resusu[0];
    $nom = htmlspecialchars($_POST['texte']);

    if (password_verify($nom, $resusu[0])){

        unset($_POST['pswInput']);

        ?>
        <p style='color: red; font-size: 15px; margin: 0;'> Suppression de votre compte<p>
        <?php

        sleep(1);
        header('Location: suppression_compte.php');
        exit();

    }
    else{

        ?>
        
            <p style='color: red; font-size: 15px; margin: 0;'> Votre mot de passe est incorrect<p>
            
        <?php

        var_dump($idcompte);

    }
}

?>


