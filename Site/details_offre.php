<?php 
    ob_start();
    session_start();
    include "header.php";
    include "../SQL/connection_local.php";

    /*
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    */

    $professionel = false;
    $bonProfessionnel = false;
    $membre = false;

    if (isset($_SESSION['membre'])) {
        $membre = true;
        $idcompte = $_SESSION['membre'];
    } elseif (isset($_SESSION['professionnel'])) {
        $professionel = true;
        $idcompte = $_SESSION['professionnel'];
    }

    //récupérer l'id du membre
    if($membre){
        $sql = "SELECT idmembre FROM _membre WHERE idcompte = :idcompte";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':idcompte', $idcompte, PDO::PARAM_INT);
        $stmt->execute();
        $idmembre = $stmt->fetchColumn();
    }

    // Vérification que c'est bien le professionel connecté qui a créé l'offre
    if ($professionel) {

        //récupérer l'id du professionnel
        $sql = "SELECT idpro FROM public.professionnel WHERE idcompte = :idcompte";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':idcompte', $idcompte, PDO::PARAM_INT);
        $stmt->execute();
        $idpro = $stmt->fetchColumn();

        
        $sql = "SELECT idpropropose FROM public._offre WHERE idoffre = :idoffre";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':idoffre', $_GET['idoffre'], PDO::PARAM_INT);
        $stmt->execute();
        $idproOffre = $stmt->fetchColumn();
        if ($idproOffre == $idpro) {
            $bonProfessionnel = true; // Utiliser cette variable pour vérifier que c'est le professionel qui a créé l'offre
        }
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
        <script
            src="https://code.jquery.com/jquery-3.3.1.js"
            integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
            crossorigin="anonymous">
        </script>
        <script> 
            $(function(){
                $("#footer").load("footer.html"); 
            });
        </script>
        <?php 
            // Vérification de l'ID de l'offre dans l'URL
            if (isset($_GET['idoffre'])) {
                $idoffre = intval($_GET['idoffre']);

                // Requête SQL pour récupérer les détails de l'offre
                $sql = "
                    SELECT o.idoffre, o.titreoffre, o.resumeoffre, o.descriptionoffre, o.prixminoffre, o.horsligne, i.pathimage, o.siteweboffre, o.alauneoffre, o.conditionAccessibilite
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
                // Passer l'offre hors ligne
                if (isset($_POST['horsligne'])) {
                    $idoffre = $_POST['idoffre']; // Récupérer l'ID de l'offre
                    $dateActuelle = new DateTime('now', new DateTimeZone('Europe/Paris'));
                    $dateActuelle = $dateActuelle->format('Y-m-d');
                
                    // Insérer une nouvelle entrée dans la table _dateStatusOffre
                    $insertSql = "INSERT INTO public._dateStatusOffre (idOffre, dateStatusChange, statusOffre, estActive)
                                  VALUES (:idOffre, :dateStatusChange, 0, true)";
                    $insertStmt = $conn->prepare($insertSql);
                    $insertStmt->bindValue(':idOffre', $idoffre, PDO::PARAM_INT);
                    $insertStmt->bindValue(':dateStatusChange', $dateActuelle, PDO::PARAM_STR);
                    $insertStmt->execute();
                
                    // Mettre à jour le statut dans la table _offre
                    $updateSql = "UPDATE public._offre 
                                  SET horsLigne = true 
                                  WHERE idoffre = :idoffre";
                    $updateStmt = $conn->prepare($updateSql);
                    $updateStmt->bindValue(':idoffre', $idoffre, PDO::PARAM_INT);
                    $updateStmt->execute();
                
                    // Redirection après la mise à jour
                    header("Location: details_offre.php?idoffre=" . $idoffre);
                    exit();
                }

                // Remettre l'offre en ligne
                if (isset($_POST['remettre_en_ligne'])) {
                    $idoffre = $_POST['idoffre']; // Récupérer l'ID de l'offre
                    $dateActuelle = new DateTime('now', new DateTimeZone('Europe/Paris'));
                    $dateActuelle = $dateActuelle->format('Y-m-d');
                
                    // Insérer une nouvelle entrée dans la table _dateStatusOffre
                    $insertSql = "INSERT INTO public._dateStatusOffre (idOffre, dateStatusChange, statusOffre, estActive)
                                  VALUES (:idOffre, :dateStatusChange, 0, false)";
                    $insertStmt = $conn->prepare($insertSql);
                    $insertStmt->bindValue(':idOffre', $idoffre, PDO::PARAM_INT);
                    $insertStmt->bindValue(':dateStatusChange', $dateActuelle, PDO::PARAM_STR);
                    $insertStmt->execute();
                
                    // Mettre à jour le statut dans la table _offre
                    $updateSql = "UPDATE public._offre 
                                  SET horsLigne = false 
                                  WHERE idoffre = :idoffre";
                    $updateStmt = $conn->prepare($updateSql);
                    $updateStmt->bindValue(':idoffre', $idoffre, PDO::PARAM_INT);
                    $updateStmt->execute();
                
                    // Redirection après la mise à jour
                    header("Location: details_offre.php?idoffre=" . $idoffre);
                    exit();
                }
                

?>

        <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

        <?php
        //Collecte des informations sur l'emplacement de l'offre
        $recherche_adresse = $conn -> prepare("SELECT numrue, supplementadresse, adresse, codepostal, ville, departement, pays
                            FROM public.offreadresse o
                            WHERE o.idoffre = :idoffre");
        
        $recherche_adresse->bindValue(':idoffre', $idoffre, PDO::PARAM_INT);
        $recherche_adresse->execute();
        $adresse_offre = $recherche_adresse->fetch();


        $rue = $adresse_offre["numrue"]; // Numéro de la rue
        $code_postal = $adresse_offre["codepostal"]; // Code postal
        $adresserue = $adresse_offre["adresse"]; // Adresse
        $ville = ucfirst($adresse_offre["ville"]); // Ville + passage en majuscule
        $departement = ucfirst($adresse_offre["departement"]); // Département (ex : Bretagne) + passage en majuscule
        $pays = ucfirst($adresse_offre["pays"]); // Pays + passage en majuscule
        $supplement = $adresse_offre["supplementadresse"];
        
        // Adresse pour l'affichage sur la carte
        $adresse = trim("$rue $adresserue, $code_postal $ville, $departement, $pays");
        // Adresse plus complète pour l'utilisateur
        if (trim($supplement) == '') { // si il n'y a pas de supplément on ne l'ajoute pas à la string final
            $adresseComplete = trim("$rue $adresserue, $code_postal $ville, $departement, $pays");
        } else {
            $adresseComplete = trim("$rue $adresserue, $supplement, $code_postal $ville, $departement, $pays");
        }

        ?>
        
        <main>
            <div style=" position:sticky; top:20px; left:20px; width: 100%;">
                <a style="text-decoration: none; font-size: 30px; color: #040316; cursor: pointer;" href="./index.php">&#8617;</a>
                <!-- onclick="history.back(); -->
            </div>

            <div class="container-details-offre" >
                <div class="details-offre" >

                    <?php if ($bonProfessionnel) { ?>
                    
                        <div style="display:flex; align-items:center; justify-content:center;">
                            <br><a href="modifier_offre.php?idoffre=<?=$offre['idoffre']?>&origin=details_offre" class="bouton-modifier-offre">Modifier mon offre</a><br><br>
                        </div>

                    <?php } ?>

                    <!-- ************************************ -->
                    <!-- ******* DEBUT DETAILS OFFRE ******** -->
                    <!-- ************************************ -->

                    <?php if ($offre): ?>
                        <div class="offre-detail-container">
                            <h1 class="offre-titre"><?= $offre['titreoffre'] ?></h1>
                            <?php
                                if ($offre["alauneoffre"]==true) {
                            ?>
                                <p style="color:#36D673;" class="offre-resume-detail" ><strong>Cette offre est à la Une</strong></p>
                            <?php 
                                }
                            ?>
                            <div class="offre-image-container" style="text-align:center;">
                                <img class="offre-image" src="<?= !empty($offre['pathimage']) ? $offre['pathimage'] : 'img/default.jpg' ?>" alt="Image de l'offre">
                            </div>
                            <p class="offre-resume-detail"><strong>Résumé:</strong> <?= $offre['resumeoffre'] ?></p>
                            <p class="offre-resume-detail"><strong>Description:</strong> <?= $offre['descriptionoffre'] ?></p>
                            <p class="offre-resume-detail"><strong>Accessibilité :</strong> <?= $offre['conditionaccessibilite'] ?></p>

                            <p class="adresse-detail">Localisation de l'offre</p>
                            <div id="map" style="display:flex;align-items:center;justify-content:center;">
                                <h2 id="text-chargement" >Chargement de la carte</h2>
                            </div>
                            <p class="adresse-detail"><?php echo $adresseComplete ?><p>

                            <section class="details_offre_mobile_tarifs">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Tarif minimum</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><?php echo htmlspecialchars($offre['prixminoffre']) . "€"; ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </section>

                            <div class="detail-btn-container">
                                <a href="<?php echo $offre['siteweboffre']; ?>"  target="_blank" class="detail-offre-btn">
                                    <button class="detail-offer-btn">Site web de l'offre</button>
                                </a>
                            </div>

                            <?php
                        
                                // 1) On cherche d'abord dans quelle table se trouve l'ID Offre
                                $stmt = $conn->prepare("SELECT public.trouver_categorie_offre(:idoffre)");
                                $stmt->execute([':idoffre' => (int)$idoffre]);  // Assurez-vous que $idoffre est un entier
                                $categorie = $stmt->fetchColumn();

                                // On récupère les détails de l'offre en fonction de la catégorie
                                switch ($categorie) {
                                    case 1:
                                        $stmt = $conn->prepare("SELECT * FROM public._offreactivite WHERE idoffre = :idoffre");
                                        $stmt->execute([':idoffre' => $idoffre]);
                                        $offreDetails = $stmt->fetch();
                                        $cat = 'activite';
                                        break;
                                    case 2:
                                        $stmt = $conn->prepare("SELECT * FROM public._offreparcattraction WHERE idoffre = :idoffre");
                                        $stmt->execute([':idoffre' => $idoffre]);
                                        $offreDetails = $stmt->fetch();
                                        $cat = 'parc';
                                        break;
                                    case 3:
                                        $stmt = $conn->prepare("SELECT * FROM public._offrerestaurant WHERE idoffre = :idoffre");
                                        $stmt->execute([':idoffre' => $idoffre]);
                                        $offreDetails = $stmt->fetch();
                                        $cat = 'restauration';
                                        break;
                                    case 4:
                                        $stmt = $conn->prepare("SELECT * FROM public._offrespectacle WHERE idoffre = :idoffre");
                                        $stmt->execute([':idoffre' => $idoffre]);
                                        $offreDetails = $stmt->fetch();
                                        $cat = 'spectacle';
                                        break;
                                    case 5:
                                        $stmt = $conn->prepare("SELECT * FROM public._offrevisite WHERE idoffre = :idoffre");
                                        $stmt->execute([':idoffre' => $idoffre]);
                                        $offreDetails = $stmt->fetch();
                                        $cat = 'visite';
                                        break;
                                    default:
                                        $offreDetails = null;
                                        break;

                                }
                            ?>

                            <?php
                            if ($offreDetails) { switch ($cat) {
                                    case 'activite':
                                    ?>
                                        <section class="details_offre_mobile">
                                            <table>
                                                <thead>
                                                    <tr>
                                                        <th>Indication de durée</th>
                                                        <th>Age minimum</th>
                                                        <th>Prestation incluse</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($offreDetails['indicationduree']); ?> heures</td>
                                                        <td><?php echo htmlspecialchars($offreDetails['ageminimum']); ?> ans</td>
                                                        <td><?php echo htmlspecialchars($offreDetails['prestationincluse']); ?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </section>
                                <?php 
                                    break;
                                    case 'parc':
                                ?>
                                        <section class="details_offre_mobile">
                                            <!-- On affiche la carte du parc -->
                                            <?php
                                            $stmt = $conn->prepare("SELECT pathimage FROM public._image WHERE idimage = :idimage");
                                            $stmt->execute([':idimage' => $offreDetails['carteparc']]);
                                            $imagecarteresto = $stmt->fetchColumn();
                                            ?>
                                            <h2>Carte du parc</h2>
                                            <img src="<?php echo $imagecarteresto; ?>" alt="Carte du parc" style="width:100%;max-width:500px; margin-bottom: 20px;">
                                            
                                            <table>
                                                <thead>
                                                    <tr>
                                                        <th>Date d'ouverture</th>
                                                        <th>Date de fermeture</th>
                                                        <th>Nombre d'attractions</th>
                                                        <th>Age minimum</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($offreDetails['dateouverture']); ?></td>
                                                        <td><?php echo htmlspecialchars($offreDetails['datefermeture']); ?></td>
                                                        <td><?php echo htmlspecialchars($offreDetails['nbrattraction']); ?></td>
                                                        <td><?php echo htmlspecialchars($offreDetails['ageminimum']); ?> ans</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </section>
                                <?php
                                    break;
                                    case 'restauration':
                                        $horaires = json_decode($offreDetails['horairesemaine'], true);
                                ?>
                                        <section class="details_offre_mobile">
                                            <!-- On affiche la carte du restaurant -->
                                            <?php
                                            $stmt = $conn->prepare("SELECT pathimage FROM public._image WHERE idimage = :idimage");
                                            $stmt->execute([':idimage' => $offreDetails['carteresto']]);
                                            $imagecarteresto = $stmt->fetchColumn();
                                            ?>
                                            <h2>Carte du restaurant</h2>
                                            <img src="<?php echo $imagecarteresto; ?>" alt="Carte du restaurant" style="width:100%;max-width:500px; margin-bottom: 20px;">

                                            <table>
                                                <thead>
                                                    <tr>
                                                        <th>Horaire semaine</th>
                                                        <th>Gamme de prix</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><?php echo "Déjeuner : " . $horaires['lunchOpen'] . " - " . $horaires['lunchClose'] . "<br>Dîner : " . $horaires['dinnerOpen'] . " - " . $horaires['dinnerClose']; ?></td>
                                                        <?php
                                                        // si 1 alors on remplace par €, si 2 alors on remplace par €€, si 3 alors on remplace par €€€
                                                        $gammePrix = str_repeat('€', $offreDetails['gammeprix']);
                                                        ?>
                                                        <td><?php echo htmlspecialchars($gammePrix); ?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </section>
                                <?php
                                    break;
                                    case 'spectacle':
                                ?>
                                        <section class="details_offre_mobile">
                                            <table>
                                                <thead>
                                                    <tr>
                                                        <th>Date de l'offre</th>
                                                        <th>Indication de durée</th>
                                                        <th>Capacité d'accueil</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($offreDetails['dateoffre']); ?></td>
                                                        <td><?php echo htmlspecialchars($offreDetails['indicationduree']); ?> heures</td>
                                                        <td><?php echo htmlspecialchars($offreDetails['capaciteacceuil']); ?> personnes</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </section>
                                <?php
                                    break;
                                    case 'visite':
                                ?>
                                        <section class="details_offre_mobile">
                                            <table>
                                                <thead>
                                                    <tr>
                                                        <th>Date de l'offre</th>
                                                        <th>Visite guidée</th>
                                                        <th>Langues proposées</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($offreDetails['dateoffre']); ?></td>
                                                        <td><?php echo htmlspecialchars($offreDetails['visiteguidee']); ?></td>
                                                        <td><?php echo htmlspecialchars($offreDetails['langueproposees']); ?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </section>
                                <?php
                                    break;
                                }
                            }
                            ?>

                            <div class="boutons-bas-offre">

                                <?php if ($bonProfessionnel) { ?>

                                    <!-- Bouton pour passer l'offre hors ligne ou remettre en ligne -->
                                    <?php if ($offre['horsligne']) { ?>
                                        <form style="display:flex;justify-content:center;" method="POST" action="">
                                            <input type="hidden" name="remettre_en_ligne" value="true">
                                            <input type="hidden" name="idoffre" value="<?= $offre['idoffre'] ?>">
                                            <button type="submit" class="offer-btn" onclick="return confirm('Êtes-vous sûr de vouloir remettre cette offre en ligne ?');">
                                                Remettre l'offre en ligne
                                            </button>
                                        </form>
                                    <?php } else { ?>
                                        <form style="display:flex;justify-content:center;" method="POST" action="">
                                            <input type="hidden" name="horsligne" value="true">
                                            <input type="hidden" name="idoffre" value="<?= $offre['idoffre'] ?>">
                                            <button type="submit" class="offer-btn" onclick="return confirm('Êtes-vous sûr de vouloir passer cette offre hors ligne ?');">
                                                Passer l'offre hors ligne
                                            </button>
                                        </form>
                                    <?php } ?>
                                <?php } ?>

                                <a href="index.php" class="offer-btn">Retour aux offres</a>

                            </div>

                        </div>
                    <?php else: ?>
                        <p>Détails de l'offre non disponibles.</p>
                    <?php endif; ?>
                    <!-- ************************************ -->
                    <!-- ******* FIN DETAILS OFFRE ********** -->
                    <!-- ************************************ -->
                
                </div>

                <div class="avis-offre" >

                    <!-- ************************************ -->
                    <!-- ******* DEBUT AVIS OFFRE ********** -->
                    <!-- ************************************ -->

                    <?php
                        if($membre){
                            // Requête SQL pour récupérer les avis sur l'offre et le profil de l'utilisateur sauf pour l'utilisateur connecté
                            $sql = "SELECT a.idavis, a.commentaireavis, a.noteavis, a.dateavis, a.scorepouce, m.nomcompte, m.prenomcompte, i.pathimage
                            FROM public._avis a
                            JOIN public.membre m ON a.idmembre = m.idmembre
                            JOIN public._image i ON m.idimagepdp = i.idimage
                            WHERE a.idoffre = :idoffre AND m.idmembre <> :conn_membre
                            ORDER BY a.scorepouce DESC";

                            $sql_only_member = "SELECT a.idavis, a.commentaireavis, a.noteavis, a.dateavis, a.scorepouce, m.nomcompte, m.prenomcompte, i.pathimage
                            FROM public._avis a
                            JOIN public.membre m ON a.idmembre = m.idmembre
                            JOIN public._image i ON m.idimagepdp = i.idimage
                            WHERE a.idoffre = :idoffre AND m.idmembre = :conn_membre
                            ORDER BY a.scorepouce DESC";
                            
                        } else {
                            // Requête SQL pour récupérer les avis sur l'offre et le profil de l'utilisateur
                            $sql = "SELECT a.idavis, a.commentaireavis, a.noteavis, a.dateavis, a.scorepouce, m.nomcompte, m.prenomcompte, i.pathimage
                            FROM public._avis a
                            JOIN public.membre m ON a.idmembre = m.idmembre
                            JOIN public._image i ON m.idimagepdp = i.idimage
                            WHERE a.idoffre = :idoffre
                            ORDER BY a.scorepouce DESC";
                        }
                        

                        // Préparer et exécuter la requête de tout les avis
                        $stmt = $conn->prepare($sql);
                        if ($membre){
                            $stmt->bindValue(':conn_membre', $idmembre, PDO::PARAM_INT);
                        }
                        $stmt->bindValue(':idoffre', $idoffre, PDO::PARAM_INT);
                        $stmt->execute();

                        // Récupérer les avis
                        $avis = $stmt->fetchAll();

                        if($membre){
                            // Préparer et exécuter pour l'avis du membre
                            $stmt = $conn->prepare($sql_only_member);
                            $stmt->bindValue(':conn_membre', $idmembre, PDO::PARAM_INT);
                            $stmt->bindValue(':idoffre', $idoffre, PDO::PARAM_INT);
                            $stmt->execute();

                            // Récupérer les avis
                            $avis_membre = $stmt->fetch();
                        }

                    ?>

                    <h2>Avis sur l'offre</h2>

                    <div class="titre-moy">
                        <?php 
                            $noteMoyenne = 0;
                            $nbAvis = count($avis);
                            if (isset($membre)){ 
                                // on ajoute la note du membre connecté si il à un avis
                                if (isset($avis_membre)){
                                    if($avis_membre){
                                        $noteMoyenne += $avis_membre['noteavis']; 
                                        $nbAvis += 1; // on ajoute un avis car le membre à laissé un avis pour bien calculer la moyenne
                                    }
                                }
                            } 
                            if ($nbAvis > 0) {
                                foreach ($avis as $avi) {
                                    $noteMoyenne += $avi['noteavis'];
                                }
                                $noteMoyenne = $noteMoyenne/$nbAvis;
                            }
                        ?>
                        <?php 

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

                        <p><?= number_format($noteMoyenne, 2) ?>/5</p>

                    </div>
                    <div class="avis-container">

                    <?php if ($membre && !$avis_membre) { ?>
                        <span>Donnez votre avis sur cette offre :</span>
                        <a class="add-avis-btn" href="javascript:void(0);" id="addAvisBtn">
                            <img class="circle-on-hover" src="./img/icons/circle-plus-solid-green.svg" alt="Donner mon avis">
                        </a>

                        <!-- Formulaire caché au départ -->
                        <form id="avisForm" style="display:none;" action="upload_avis.php" method="POST">
                            <div id="addAvisForm" style="display:none;">

                                <input type="hidden" name="idoffre" value="<?= $idoffre ?>">

                                <h2 for="datevisite">Date de votre visite :</h2>
                                <input class="zone-date" type="date" id="datevisite" name="datevisite" style="width:45%;" required>
                                <br>

                                <h2 for="avis">Votre avis :</h2>
                                <textarea id="commentaire" class="textarea-avis" name="commentaire" required></textarea>

                                <h2 for="note">Votre note :</h2>
                                <div class="rating">
                                    <input type="radio" id="star1" name="note" value="1" required />
                                    <label class="label-stars" for="star1" title="1 étoiles">
                                        <img src="./img/icons/star-solid.svg" alt="star checked" width="20" height="20">
                                    </label>
                                    <input type="radio" id="star2" name="note" value="2" />
                                    <label class="label-stars" for="star2" title="2 étoiles">
                                        <img src="./img/icons/star-regular.svg" alt="star unchecked" width="20" height="20">
                                    </label>
                                    <input type="radio" id="star3" name="note" value="3" />
                                    <label class="label-stars" for="star3" title="3 étoiles">
                                        <img src="./img/icons/star-regular.svg" alt="star unchecked" width="20" height="20">
                                    </label>
                                    <input type="radio" id="star4" name="note" value="4" />
                                    <label class="label-stars" for="star4" title="4 étoiles">
                                        <img src="./img/icons/star-regular.svg" alt="star unchecked" width="20" height="20">
                                    </label>
                                    <input type="radio" id="star5" name="note" value="5" />
                                    <label class="label-stars" for="star5" title="5 étoile">
                                        <img src="./img/icons/star-regular.svg" alt="star unchecked" width="20" height="20">
                                    </label>
                                </div>

                                <br>

                                <button class="offer-btn" type="submit">Envoyer</button>
                            </div>
                        </form>

                        <script>
                            //Script pour les étoiles
                            document.querySelectorAll('.rating input').forEach(input => {
                                input.addEventListener('change', function() {
                                    let rating = this.value;

                                    // Remplir les étoiles jusqu'à celle cliquée
                                    document.querySelectorAll('.rating label img').forEach((star, index) => {
                                        if (index < rating) {
                                            star.src = './img/icons/star-solid.svg'; // Rempli
                                        } else {
                                            star.src = './img/icons/star-regular.svg'; // Non rempli
                                        }
                                    });
                                });
                            });

                            // Remplacement du bouton d'ajout d'avis par le formulaire
                            const addAvisBtn = document.getElementById('addAvisBtn');
                                const avisForm = document.getElementById('avisForm');
                                addAvisBtn.addEventListener('click', function() {
                                    // on masque le boutton (a)
                                    addAvisBtn.style.display = 'none';

                                    //
                                    avisForm.style.display = 'block';
                                    
                                    // on affiche le formulaire
                                    addAvisForm.style.display = 'flex';
                                    addAvisForm.style.flexDirection = 'column';
                                    // Centrer les éléments du formulaire
                                    addAvisForm.style.alignItems = 'center';
                                    addAvisForm.style.justifyContent = 'center';

                                    

                                });
                        </script>
                            
                            <hr style="border-top: 1px solid #ccc;" width="90%">

                        <?php } ?>

                        <?php 
                            // on affiche l'avis du membre si le membre est connecté et à un avis
                            if(isset($avis_membre)){
                                if ($avis_membre){
                                    $date_formated = date("d/m/Y", strtotime($avis_membre['dateavis']));
                                    ?>
                                    <div class="avis_m">
                                        <p><strong>Mon avis</strong></p>
                                        <p class ="pdp-name-date">
                                            <img class="pdp-avis" src="<?php echo $avis_membre['pathimage'] ?>" alt="image utilisateur">
                                            <strong style="margin-right:3px;"><?= $avis_membre['nomcompte'] . ' ' . $avis_membre['prenomcompte'] ?></strong> - <?= $date_formated ?>
                                        </p>
                                        <p><?= $avis_membre['commentaireavis'] ?></p>

                                                
                                        <?php
                                            for ($i = 0; $i < $avis_membre['noteavis']; $i++) {
                                                ?> <img src="./img/icons/star-solid.svg" alt="star checked" width="20" height="20"> <?php
                                            }
                                            for ($i = $avis_membre['noteavis']; $i < 5; $i++) {
                                                ?> <img src="./img/icons/star-regular.svg" alt="star checked" width="20" height="20"> <?php
                                           }
                                        ?>
                                        <br><br>
                                        <div class="suppr-avis">
                                            <a class="bouton-supprimer-avis" href="delete_avis.php?idoffre=<?=$idoffre?>&idmembre=<?=$idmembre?>&idavis=<?=$avis_membre['idavis']?>">Supprimer mon avis</a>
                                            
                                        </div>
                                    </div>
                                    <?php
                                }

                            }
                            
                            if ($avis) {
                                foreach ($avis as $avis) {
                                    $avisId = $avis['idavis'];
                                    $scorePouce = $avis['scorepouce'];
                                    if($_SESSION['thumbed'][$avisId]){
                                        $thumbsClicked[$avisId] = true;
                                    } else {
                                        $thumbsClicked[$avisId] = false;
                                    }
                                    
                                    $date_formated = date("d/m/Y", strtotime($avis['dateavis']));

                                    ?>
                                    <div class="avis">
                                        <p class ="pdp-name-date">
                                            <img class="pdp-avis" src="<?php echo $avis['pathimage'] ?>" alt="image utilisateur">
                                            <strong style="margin-right:3px;"><?= $avis['nomcompte'] . ' ' . $avis['prenomcompte'] ?></strong> - <?= $date_formated ?>
                                        </p>
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
                                        <div class="scorePouce">
                                            <a href="update_score_avis.php?id_avis=<?=$avisId?>&score=plus" id="thumbs-up-<?=$avisId?>" <?php if($thumbsClicked[$avisId]==true){echo 'style="pointer-events: none; opacity: 0.5;"';}?>><img src="./img/icons/thumbs-up.svg" alt="Avis pertinent">Pertinent</a>
                                            <a href="update_score_avis.php?id_avis=<?=$avisId?>&score=moins" id="thumbs-down-<?=$avisId?>" <?php if($thumbsClicked[$avisId]==true){echo 'style="pointer-events: none; opacity: 0.5;"';}?>><img src="./img/icons/thumbs-down.svg" alt="Avis non-pertinent">Non pertinent</a>
                                        </div>
                                    </div>
                                    <?php
                                }
                            } else {
                                if (empty($avis_membre)){
                                    ?>
                                    <p>Aucun avis pour cette offre.</p>
                                    <?php
                                } else {
                                    ?>
                                    <p>Aucun autre avis pour cette offre.</p>
                                    <?php
                                }
                            }


                        ?>

                        <!-- ************************************ -->
                        <!-- ********** FIN AVIS OFFRE ********** -->
                        <!-- ************************************ -->


                </div>

            </div>
            
        </main>

        <div id="footer"></div>

        <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
        <script>

            // Fonction pour ajouter progressivement des points de suspension
            function animateLoadingText() {
                const loadingText = document.getElementById("text-chargement");
                let dots = 0;
                return setInterval(() => {
                    dots = (dots + 1) % 4; // Cycle de 0 à 3 (pour les points)
                    loadingText.textContent = 'Chargement de la carte' + '.'.repeat(dots);
                }, 500); // Met à jour tous les 500ms
            }

            // Démarre l'animation des points de suspension
            const loadingAnimation = animateLoadingText();

            //MAP
            const adresse = "<?php echo addslashes($adresse); ?>"; // Passer l'adresse assemblée à JavaScript
            console.log("Adresse pour OSM : ", adresse); // Log de l'adresse

            async function geocode(adresse) {
                try {
                    const response = await fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(adresse)}&format=json&limit=1`);
                    return response.json();
                } catch (error) {
                    console.error('Erreur de géocodage:', error);
                }
            }

            geocode(adresse).then(data => {
                if (data && data.length > 0) {
                    const lat = data[0].lat;
                    const lon = data[0].lon;

                    const map = L.map('map').setView([lat, lon], 15);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19
                    }).addTo(map);

                    L.marker([lat, lon]).addTo(map)
                        .bindPopup(adresse)
                        .openPopup();

                    // Stoppe l'animation et cache le texte de chargement après le chargement de la carte
                    clearInterval(loadingAnimation);
                    document.querySelector('#text-chargement').style.display = 'none';
                } else {
                    clearInterval(loadingAnimation); // Arrête l'animation ici
                    document.querySelector('#text-chargement').textContent = 'Adresse non trouvée.'; // Change le texte si l'adresse n'est pas trouvée
                }
            }).catch(error => {
                console.error('Erreur de géocodage:', error);
                clearInterval(loadingAnimation); // Arrête l'animation en cas d'erreur
                document.querySelector('#text-chargement').textContent = 'Erreur lors du chargement de la carte.'; // Change le texte en cas d'erreur
            });
        </script>

        <div id="footer"></div>

        <script src="script.js"></script> 
    </body>
</html>
<?php
ob_end_flush();
?>