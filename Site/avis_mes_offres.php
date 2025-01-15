<?php

    ob_start();

    include "header.php";
    include "../SQL/connection_local.php";

    $idpro = $_SESSION['professionnel'];

    //récupérer les infos du pro
    $sql = "SELECT * from public.professionnel where idpro = :idproOffre";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':idproOffre', $idpro, PDO::PARAM_INT);
    $stmt->execute();
    $infosPro = $stmt->fetch();


?>

<!DOCTYPE html>
<html lang="fr">
    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <link rel="stylesheet" href="./style.css">   
    <title>Mes infos</title>

</head>

    <body>
        <?php
            // 1) Récupération de l'ID utilisateur
            $userID = $_SESSION['professionnel'];

            // 2) On récupère toutes les offres du professionnel
            $sql = "SELECT DISTINCT o.* 
                    FROM _offre o
                    JOIN _professionnel p ON o.idpropropose = p.idcompte
                    JOIN _avis a ON o.idoffre = a.idoffre
                    WHERE o.idpropropose = :idcompte";

            // Préparation
            $stmt = $conn->prepare($sql);

            // Liaison
            $stmt->bindValue(':idcompte', $userID, PDO::PARAM_INT);

            $stmt->execute();
            $liste_offres = $stmt->fetchAll();

        ?>

        <main class="AMO_main">
            <?php foreach($liste_offres as $offre){ ?>
                <?php 
                      $trouver_avis = $conn->prepare("SELECT *
                                                    FROM _avis a
                                                    WHERE a.idoffre = :idoffre");
                      $IDOffre = $offre["idoffre"];
                      $trouver_avis->bindValue(':idoffre', $IDOffre, PDO::PARAM_INT);
                      $trouver_avis->execute();
                      $liste_avis = $trouver_avis->fetchAll(); ?>

                <section class="AMO_offre">
                    <?php echo "<h1>" . "Votre offre: " . $offre["titreoffre"] . "</h1>" ?>

                    <?php

                        // Requête SQL pour récupérer les avis sur l'offre et le profil de l'utilisateur
                        $sql = "SELECT a.idavis, a.commentaireavis, a.noteavis, a.dateavis, a.scorepouce, a.reponsepro, m.nomcompte, m.prenomcompte, i.pathimage
                        FROM public._avis a
                        JOIN public.membre m ON a.idmembre = m.idmembre
                        JOIN public._image i ON m.idimagepdp = i.idimage
                        WHERE a.idoffre = :idoffre
                        ORDER BY a.scorepouce DESC";
                        

                        // Préparer et exécuter la requête de tout les avis
                        $stmt = $conn->prepare($sql);
                        $stmt->bindValue(':idoffre', $IDOffre, PDO::PARAM_INT);
                        $stmt->execute();

                        // Récupérer les avis
                        $avis = $stmt->fetchAll();
                    ?>

                    <div class="AMO_titre-moy">
                        <?php 
                            $noteMoyenne = 0;
                            $nbAvis = count($avis);
                            
                            if ($nbAvis > 0) {
                                foreach ($avis as $avi) {
                                    $noteMoyenne += $avi['noteavis'];
                                }
                                $noteMoyenne = $noteMoyenne/$nbAvis;
                            }

                            // Calcul des étoiles pleines
                            $etoilesCompletes = floor($noteMoyenne);  // on prend la partie entière de la moy
                            if ($noteMoyenne - $etoilesCompletes > 0.705){
                                $etoilesCompletes++;
                            }
                            for ($i = 0; $i < $etoilesCompletes; $i++) {
                                ?> 
                                <img src="./img/icons/star-solid.svg" alt="star checked" width="20" height="20">
                                <?php
                            }

                            // si la partie décimale est supérieure ou égale à 0.3 et inferieure ou égale à 0.7-> une demi étoile
                            if ($noteMoyenne - $etoilesCompletes >= 0.295 && $noteMoyenne - $etoilesCompletes <= 0.705) {
                                ?> 
                                <img src="./img/icons/star-half.svg" alt="half star checked" width="20" height="20"> 
                                <?php
                                $i++; // Compter cette demi-étoile
                            }

                            // Compléter avec les étoiles vides jusqu'à 5
                            for (; $i < 5; $i++) {
                                ?> 
                                <img src="./img/icons/star-regular.svg" alt="star unchecked" width="20" height="20"> 
                                <?php
                            }

                        ?>
                    </div>
                    
                    <p><?= number_format($noteMoyenne, 2) ?> sur 5</p>

                    <div class="AMO_avis-container">

                        <?php 
                           
                            if ($avis) {
                                foreach ($avis as $avis) {

                                    ?>
                                    <hr style="border: 1px solid grey; width: 100%; margin: 20px auto;">
                                    <?php
                                    
                                    $hasReponse = false;
                                    if($avis['reponsepro'] == true){
                                        $hasReponse = true;
                                    }
                                    $avisId = $avis['idavis'];
                                    $scorePouce = $avis['scorepouce'];
                                    if($_SESSION['thumbed'][$avisId]){
                                        $thumbsClicked[$avisId] = true;
                                    } else {
                                        $thumbsClicked[$avisId] = false;
                                    }

                                    $date_formated = date("d/m/Y", strtotime($avis['dateavis']));

                                    //recuperer les infos de la réponse si il y en a une
                                    $sql = "SELECT * from avisreponse where idavis = :idavis";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bindValue(':idavis', $avisId, PDO::PARAM_INT);
                                    $stmt->execute();
                                    $reponse = $stmt->fetch();

                                    if($reponse){
                                        $dateReponse = date("d/m/Y", strtotime($reponse['datereponse']));
                                    } 

                                    ?>
                                    <div class="AMO_avis">
                                        <div class="container_pdp-name-date_options">
                                            <p class="pdp-name-date">
                                                <img class="pdp-avis" src="<?php echo $avis['pathimage'] ?>" alt="image utilisateur">
                                                <strong style="margin-right:3px;"><?= $avis['nomcompte'] . ' ' . $avis['prenomcompte'] ?></strong> - <?= $date_formated ?>
                                            </p>
                                            <a class="avis_options" onclick="openModalAvis(event)">
                                                <img src="./img/icons/report.svg" width="20px" height="20px" alt="report icon">
                                            </a>
                                        </div>

                                        <!-- option avis modale -->
                                        <div id="modalAvis" class="modal_avis">
                                            <div class="modal_avis-content">
                                                <span class="close_avis" onclick="closeModalAvis()">&times;</span>
                                                <form action="report_avis.php" method="POST">
                                                    <input type="hidden" name="idavis" value="<?=$avisId?>">
                                                    <div class="form_avis_signalement">
                                                        <h2>Signaler l'avis aux administrateurs ?</h2><br>
                                                        <select class="dropdown-signalement" name="raison" id="raison">
                                                            <option value="1">Spam</option>
                                                            <option value="2">Contenu inapproprié</option>
                                                            <option value="3">Avis faux ou trompeur</option>
                                                            <option value="4">Non-respect des conditions d'utilisation</option>
                                                            <option value="5">Publicité déguisée</option>
                                                            <option value="6">Harcèlement ou comportement abusif</option>
                                                            <option value="7">Discours haineux ou discriminatoire</option>
                                                            <option value="8">Violence ou incitation à la violence</option>
                                                            <option value="9">Usurpation d'identité</option>
                                                            <option value="10">Contenu haineux ou offensant</option>
                                                            <option value="11">Langage vulgaire ou offensant</option>
                                                            <option value="12">Contenu illégal</option>
                                                        </select>
                                                        <br><br>
                                                        <button type="submit" class="bouton-supprimer-avis">Signaler l'avis</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                        <p><?= $avis['commentaireavis'] ?></p>
                                        <div class="avis_stars_score">
                                            <?php
                                                for ($i = 0; $i < $avis['noteavis']; $i++) {
                                                    ?> <img src="./img/icons/star-solid.svg" alt="star checked" width="20" height="20"> <?php
                                                }
                                                for ($i = $avis['noteavis']; $i < 5; $i++) {
                                                    ?> <img src="./img/icons/star-regular.svg" alt="star checked" width="20" height="20"> <?php
                                                }
                                            ?>
                                            <p>Score de pertinence : <?= $scorePouce ?> </p>
                                        </div>
                                        

                                            <?php if ($professionel && !$hasReponse) { ?>
                                                <div class="container-repondre-avis">
                                                    <!-- afficher une petite flèche à droite de répondre qui change de sens si le form est ouvert ou non -->
                                                    <a id="replyButton-<?= $avisId ?>" href="javascript:void(0);" class="reply-btn bouton-repondre-avis" onclick="openReplyForm(<?= $avisId ?>)">Répondre <img id="arrow-<?= $avisId ?>" src="./img/icons/arrow-down.svg"></a>
                                                    
                                                    <form id="replyForm-<?= $avisId ?>" class="reply-form" style="display:none;" action="upload_reply.php" method="POST">
                                                        <input type="hidden" name="idavis" value="<?= $avisId ?>">
                                                        <textarea name="reply" placeholder="Votre réponse à l'avis" cols="29" rows="5" required></textarea>
                                                        <button class="bouton-envoyer-reponse" type="submit">Envoyer</button>
                                                    </form>
                                                </div>
                                            <?php } elseif ($hasReponse) { ?>

                                                <div class="container-reponse">

                                                    <p class="title-reponse"><strong>Réponse du professionnel :</strong></p>    
                                                    <div class="container_pdp-name-date_options">
                                                        <p class="pdp-name-date-pro">
                                                            <img class="pdp-avis" src="<?php echo $infosPro['pathimage'] ?>" alt="image utilisateur">
                                                            <strong style="margin-right:3px;"><?= $infosPro['nomcompte'] . ' ' . $infosPro['prenomcompte'] ?></strong> - <?= $dateReponse ?>
                                                        </p>
                                                    </div>
                                                    <div class="text-reponse-pro">
                                                        <img src="./img/icons/arrow-enter-right.svg" alt="arrow enter right">
                                                        <p><?= $reponse['textereponse'] ?></p>
                                                    </div>

                                                </div>
                                                <?php
                                            } ?>
                                        </div>      

                                    <?php
                                }
                            } else {
                                    ?>
                                    <div class="AMO_titre-moy">
                                        <p>Aucun avis pour cette offre.</p>
                                    </div>


                                    <?php
                            }


                        ?>

                        <!-- FIN -->

                </section>
            <?php } ?>
        </main>

        
        <div id="footer"></div>
        <!-- Script pour header et footer -->
        <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
        <script>
            $(function() {
                $("#footer").load("./footer.html");
            });
        </script>
        
        <script src="./script.js" ></script>
    </body>
</html>
