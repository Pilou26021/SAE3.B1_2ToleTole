<?php
    ob_start();
    session_start();
    include "../SQL/connection_local.php";
?>

<!DOCTYPE html>
<html lang="fr">
    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
</head>

<?php 

    /////////////////////////////////
    // Récupération de l'ID du membre
    /////////////////////////////////

    if (isset($_SESSION['membre'])) {
        $idCompte = $_SESSION['membre'];

        $requeteIDmembre = "SELECT idmembre
                    FROM _membre
                    WHERE idcompte = :idDuCompte;";

        $executionRequeteIDmembre = $conn->prepare($requeteIDmembre);
        $executionRequeteIDmembre->bindValue(':idDuCompte', $idCompte, PDO::PARAM_INT);
        $executionRequeteIDmembre->execute();
        $resultatRequeteIDmembre = $executionRequeteIDmembre->fetch();
        $idMembre = $resultatRequeteIDmembre[0];
    }




    /////////////
    // Blacklist
    /////////////

    // 1. On cherche tous les avis blacklistés de ce membre

    $requeteSql = "SELECT idavis
                    FROM _avis
                    WHERE idmembre = :idDuMembre AND blacklistavis = 'true';";
   
    $executionRequete = $conn->prepare($requeteSql);
    $executionRequete->bindValue(':idDuMembre', $idMembre, PDO::PARAM_INT);
    $executionRequete->execute();
    $resultatRequete = $executionRequete->fetchAll();

    // 2. Pour chaque avis trouvé, on exécute la fonction de suppression

    // On prépare la requête
    $requeteSql2 = "DELETE
                    FROM _avis
                    WHERE idavis = :idAvis";

    $executionRequete2 = $conn->prepare($requeteSql2);

    foreach ($resultatRequete as $avis){

        // On l'éxécute pour chaque itération
        $executionRequete2->bindParam(':idAvis', $avis[0], PDO::PARAM_INT);
        $executionRequete2->execute();

    }

    /////////////////////
    // Anonymisation
    /////////////////////

    // 1. On cherche tous les commentaires (non-blacklistés) laissés par le membre
    $requeteSql3 = "SELECT idavis
                    FROM _avis
                    WHERE idmembre = :idDuMembre;";

    $executionRequete3 = $conn->prepare($requeteSql3);
    $executionRequete3->bindValue(':idDuMembre', $idMembre, PDO::PARAM_INT);
    $executionRequete3->execute();
    $resultatRequete3 = $executionRequete3->fetchAll();

    // 2. Pour chaque avis trouvé, on change l'attribut ID membre à anonymous
    foreach ($resultatRequete3 as $avisNonBlackliste){
        $requeteSql4 = "UPDATE _avis
                        SET idmembre = 6
                        WHERE idavis = :id;";

        // On l'éxécute pour chaque itération
        $executionRequete4 = $conn->prepare($requeteSql4);
        $executionRequete4->bindParam(':id', $avisNonBlackliste[0], PDO::PARAM_INT);
        $executionRequete4->execute();
    }


    ////////////////////////
    // Suppression du compte
    ////////////////////////

    // Enfin, on supprime le compte du membre.




    // 2. Ensuite, la table _membre
    $requeteSql6 = "DELETE
                    FROM _membre
                    WHERE idcompte = :id;";

    $executionRequete6 = $conn->prepare($requeteSql6);
    $executionRequete6->bindValue(':id', $idCompte, PDO::PARAM_INT);
    $executionRequete6->execute();

    // 3. Finalement, la table _compte
    $requeteSql7 = "DELETE
                    FROM _compte
                    WHERE idcompte = :id;";

    $executionRequete7 = $conn->prepare($requeteSql7);
    $executionRequete7->bindValue(':id', $idCompte, PDO::PARAM_INT);
    $executionRequete7->execute();


    // 4. On indique que le compte à été supprimé, et on redirige l'utilisateur vers l'accueil


    sleep(3);
    session_destroy();
    header("Location: index.php");
    exit();



?>

