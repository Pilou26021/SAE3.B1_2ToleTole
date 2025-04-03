<?php 

    ob_start();
    session_start();
    include "../SQL/connection_local.php";

    //récupération du post
    $idavis = $_POST['idavis'];
    $idoffre = $_POST['idoffre'];

    //recuperation de la config
    $config = parse_ini_file('./.config');
    $nbr_blacklist = $config['nbr_blacklist'];  // ex "12"
    $unit_blacklist = $config['unit_blacklist'];  // ex "months"

     // Requête SQL pour récupérer les détails de l'offre
    $sql = "
        SELECT o.idoffre, o.nbrjetonblacklistagerestant 
        FROM public._offre o
        WHERE o.idoffre = :idoffre
    ";
    // Préparer et exécuter la requête
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':idoffre', $idoffre, PDO::PARAM_INT);
    $stmt->execute();
    // Récupérer les détails de l'offre
    $offre = $stmt->fetch();

    // Requête SQL pour récupérer les détails de l'avis
    $sql = "
        SELECT a.idavis, a.idoffre, a.noteavis, a.commentaireavis, a.idmembre, a.dateavis, a.datevisiteavis, a.reponsepro, a.scorepouce, a.blacklistavis, a.blacklistenddate
        FROM public._avis a
        WHERE a.idavis = :idavis
    ";
    // Pré
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':idavis', $idavis, PDO::PARAM_INT);
    $stmt->execute();
    // Récupérer les détails de l'avis
    $avis = $stmt->fetch();

    if($offre['nbrjetonblacklistagerestant'] > 0){
        // Requête SQL pour ajouter un avis à la blacklist
        $sql = "
            UPDATE public._avis
            SET blacklistavis = true, blacklistenddate = current_timestamp + interval '".$nbr_blacklist." ".$unit_blacklist."'
            WHERE idavis = :idavis
        ";
        //
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':idavis', $idavis, PDO::PARAM_INT);
        $stmt->execute();

        // Requête SQL pour décrémenter le nombre de jetons de blacklistage restants
        $sql = "
            UPDATE public._offre
            SET nbrjetonblacklistagerestant = nbrjetonblacklistagerestant - 1
            WHERE idoffre = :idoffre
        ";
        //
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':idoffre', $idoffre, PDO::PARAM_INT);
        $stmt->execute();

    }else{
        echo "Vous n'avez plus de jetons de blacklistage";
    }

    // Rediriger l'utilisateur vers la page précédente
    echo "<script>window.history.back();</script>";
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();

?>