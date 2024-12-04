<?php

    ob_start();
    session_start();
    include "header.php";
    include "../SQL/connection_local.php";

    // 1) Définition de l'identifiant utilisateur
    if (isset($_SESSION['professionnel'])){
        // Si c'est un pro
        $userID = $_SESSION['professionnel'];
    }else{
        // Si c'est un membre
        $userID = $_SESSION['membre'];
    }

    // 2) On vérifie sur quelle page se trouve l'utilisateur, on test donc un champ

    // Si l'utilisateur est sur la page mes_infos.php:
    if (isset($_POST['nom'])){

        // Si c'est un professionnel, il peut changer sa dénomination et son n° de siren si c'est un pro privé:
        if (isset($_SESSION['professionnel'])){    

            if ($_SESSION['typePro'] == 'prive'){

                $sqlProPriv = "UPDATE _professionnel
                       SET denominationpro = :denomination,
                           numsirenpro = :numsirenpro
                       WHERE _professionnel.idcompte = :userID;";
        
                // Préparation
                $stmtProPriv = $conn->prepare($sqlProPriv);

                // Liaisons
                $stmtProPriv->bindValue(':denomination', $_POST["denominationpro"]);
                $stmtProPriv->bindValue(':numsirenpro', $_POST["numsiren"]);
                $stmtProPriv->bindValue(':userID', $userID);

                // Exécution
                $stmtProPriv->execute();
                header("Location: mes_infos_bancaires.php");
            }
        
            // Si c'est un professionnel publique, il ne dispose pas de numéro de siren, ainsi on lui assigne un numéro par défaut non visible
            else if ($_SESSION['typePro'] == 'publique'){

                $sqlProPub = "UPDATE _professionnel
                              SET denominationpro = :denomination,
                                  numsirenpro = :numsirenpro
                              WHERE _professionnel.idcompte = :userID;";
            
                // Préparation
                $stmtProPub = $conn->prepare($sqlProPub);

                // Liaisons
                $sirenDefaut = "000000000";

                $stmtProPub->bindValue(':denomination', $_POST["denominationpro"]);
                $stmtProPub->bindValue(':numsirenpro', $sirenDefaut);
                $stmtProPub->bindValue(':userID', $userID);

                // Exécution
                $stmtProPub->execute();

            }

    } else {
        
        // Sinon, c'est un membre, et lui peut modifier son pseudonyme
        $sqlMembre = "UPDATE _membre
                      SET pseudonyme = :pseudo
                      WHERE idcompte = :userID;";
        
        // Préparation
        $stmtMembre = $conn->prepare($sqlMembre);

        // Liaisons
        $stmtMembre->bindValue(':pseudo', $_POST["pseudo"]);
        $stmtMembre->bindValue(':userID', $userID);

        // Execution
        $stmtMembre->execute();

    }

    // 3) On met à jour toutes les informations communes
    $sqlInfos = "UPDATE _compte
                 SET nomcompte = :nomC,
                     prenomcompte = :prenomC,
                     mailcompte = :mailC,
                     numtelcompte = :telC
                 WHERE _compte.idcompte = :userID;";

    // Préparation
    $stmtInfos = $conn->prepare($sqlInfos);

    // Liaisons
    $stmtInfos->bindValue(':nomC', $_POST["nom"]);
    $stmtInfos->bindValue(':prenomC', $_POST["prenom"]);
    $stmtInfos->bindValue(':mailC', $_POST["email"]);
    $stmtInfos->bindValue(':telC', $_POST["telephone"]);
    $stmtInfos->bindValue(':userID', $userID);

    // Exécution
    $stmtInfos->execute();

    // On cherche idadresse pour l'utiliser plus tard
    $trouver_id_adresse = "SELECT idadresse
                           FROM _compte
                           WHERE _compte.idcompte = :userID;";

    // Préparation
    $preparerAdresse = $conn->prepare($trouver_id_adresse);

    // Liaison
    $preparerAdresse->bindValue(':userID', $userID);

    // Exécution
    $preparerAdresse->execute();

    $adresse = $preparerAdresse->fetch();

    // Grace à idadresse, on peut mettre à jour l'adresse de l'utilisateur
    $sqlAdresse = "UPDATE _adresse
                   SET numrue = :numC,
                       supplementadresse = :supAdresseC,
                       adresse = :adresseC,
                       codepostal = :cpC,
                       ville = :villeC
                   WHERE _adresse.idadresse = :adresse;";

    // Préparation
    $stmtAdresse = $conn->prepare($sqlAdresse);

    // Liaisons
    $stmtAdresse->bindValue(':numC', $_POST["rue"]);
    $stmtAdresse->bindValue(':supAdresseC', $_POST["supAdresse"]);
    $stmtAdresse->bindValue(':adresseC', $_POST["adresse"]);
    $stmtAdresse->bindValue(':cpC', $_POST["cp"]);
    $stmtAdresse->bindValue(':villeC', $_POST["ville"]);
    $stmtAdresse->bindValue(':adresse', $adresse['idadresse']);

    // Exécution
    $stmtAdresse->execute();

    // Redirection vers la page précédente
    header("Location: mes_infos.php");
    exit;
}



/**=========================
Page coordonnées bancaires
==========================*/

//On regarde si l'IBAN à bien été envoyé et qu'on est donc bien sur cette page
if (isset($_POST['iban'])){

    $sqlCoBanq = "UPDATE _professionnelprive p
                  SET coordbancairesiban = :CoIban,
                      coordbancairesbic = :CoBic
                  WHERE p.idpro = :userID";
    
    // Préparation
    $stmtCoBanq = $conn->prepare($sqlCoBanq);

    // Liaisons
    $stmtCoBanq->bindValue(':CoIban', $_POST["iban"]);
    $stmtCoBanq->bindValue(':CoBic', $_POST["bic"]);
    $stmtCoBanq->bindValue(':userID', $userID);

    // Exécution
    $stmtCoBanq->execute();

    // Redirection vers la page précédente
    header("Location: mes_infos_bancaires.php");
    exit;
}

?>
