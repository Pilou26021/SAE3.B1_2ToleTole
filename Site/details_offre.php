<?php 
    ob_start();
    include "header.php";
    include "../SQL/connection_local.php";

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
                    SELECT o.idoffre, o.titreoffre, o.resumeoffre, o.descriptionoffre, o.prixminoffre, o.horsligne, i.pathimage, o.siteweboffre
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
                $updateSql = "UPDATE public._offre SET horsligne = true WHERE idoffre = :idoffre";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->bindValue(':idoffre', $idoffre, PDO::PARAM_INT);
                $updateStmt->execute();

                // Redirection après la mise à jour
                header("Location: details_offre.php?idoffre=" . $idoffre);
                exit();
            }

            // Remettre l'offre en ligne
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


        <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
        <style>
            #map {
                width: 100%; /* Largeur de la carte */
                height: 250px;
            }
        </style>

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

                    <!-- ************************************ -->
                    <!-- ******* DEBUT DETAILS OFFRE ******** -->
                    <!-- ************************************ -->

                    <?php if ($offre): ?>
                        <div class="offre-detail-container">
                            <h1 class="offre-titre"><?= $offre['titreoffre'] ?></h1>
                            <div class="offre-image-container" style="text-align:center;">
                                <img class="offre-image" src="<?= !empty($offre['pathimage']) ? $offre['pathimage'] : 'img/default.jpg' ?>" alt="Image de l'offre">
                            </div>
                            <p class="offre-resume-detail"><strong>Résumé:</strong> <?= $offre['resumeoffre'] ?></p>
                            <p class="offre-resume-detail"><strong>Description:</strong> <?= $offre['descriptionoffre'] ?></p>

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
                    <!-- ************************************ -->
                    <!-- ******* FIN DETAILS OFFRE ********** -->
                    <!-- ************************************ -->
                
                </div>

                <div class="avis-offre" >

                    <!-- ************************************ -->
                    <!-- ******* DEBUT AVIS OFFRE ********** -->
                    <!-- ************************************ -->

                    <?php
                        // Requête SQL pour récupérer les avis sur l'offre et la photo de profil de l'utilisateur
                        $sql = "SELECT a.idavis, a.commentaireavis, a.noteavis, a.dateavis, c.nomcompte, c.prenomcompte, i.pathimage
                                FROM public._avis a
                                JOIN public._compte c ON a.idmembre = c.idcompte
                                JOIN public._image i ON c.idimagepdp = i.idimage
                                WHERE a.idoffre = :idoffre
                                ORDER BY a.dateavis DESC
                        ";

                        // Préparer et exécuter la requête
                        $stmt = $conn->prepare($sql);
                        $stmt->bindValue(':idoffre', $idoffre, PDO::PARAM_INT);
                        $stmt->execute();

                        // Récupérer les avis
                        $avis = $stmt->fetchAll();

                    ?>

                    <h2>Avis sur l'offre</h2>
                    <div class="titre-moy">
                        <?php 
                            $noteMoyenne = 0;
                            $nbAvis = count($avis);
                            if ($nbAvis > 0) {
                                foreach ($avis as $avi) {
                                    $noteMoyenne += $avi['noteavis'];
                                }
                                $noteMoyenne = $noteMoyenne / $nbAvis;
                            }
                        ?>
                        <?php 
                            // étoiles pleines
                            for ($i = 0; $i < floor($noteMoyenne); $i++) {
                                ?> <img src="./img/icons/star-solid.svg" alt="star checked" width="20" height="20"> <?php
                            }

                            // moitié d'étoiles pour les notes décimales entre 0.3 et 0.7
                            if ($noteMoyenne - floor($noteMoyenne) > 0.2 && $noteMoyenne - floor($noteMoyenne) < 0.8) {
                                ?> <img src="./img/icons/star-half.svg" alt="half star checked" width="20" height="20"> <?php
                                $i++; // Compter cette moitié d'étoile
                            }

                            // vides pour le reste
                            for (; $i < 5; $i++) {
                                ?> <img src="./img/icons/star-regular.svg" alt="star unchecked" width="20" height="20"> <?php
                            }
                        ?>
                        <p><?= number_format($noteMoyenne, 1) ?>/5</p>

                    </div>
                    <div class="avis-container">
                        <?php 
                            if ($avis) {
                                foreach ($avis as $avis) {

                                    $date_formated = date("d/m/Y", strtotime($avis['dateavis']));

                                    ?>
                                    <div class="avis">
                                        <p class ="pdp-name-date">
                                            <img class="pdp-avis" src="<?php echo $avis['pathimage'] ?>" alt="image utilisateur">
                                            <strong><?= $avis['nomcompte'] . ' ' . $avis['prenomcompte'] ?></strong> - <?= $date_formated ?>
                                        </p>
                                        <p><?= $avis['commentaireavis'] ?></p>
                                        <?php
                                            for ($i = 0; $i < $avis['noteavis']; $i++) {
                                                ?> <img src="./img/icons/star-solid.svg" alt="star checked" width="20" height="20"> <?php
                                            }
                                            for ($i = $avis['noteavis']; $i < 5; $i++) {
                                                ?> <img src="./img/icons/star-regular.svg" alt="star checked" width="20" height="20"> <?php
                                            }
                                        ?>
                                    </div>
                                    <?php
                                }
                            } else {
                                ?>
                                <p>Aucun avis pour cette offre.</p>
                                <?php
                            }


                        ?>

                        <!-- ************************************ -->
                        <!-- ********** FIN AVIS OFFRE ********** -->
                        <!-- ************************************ -->


                </div>

                <style>
                    .container-details-offre {
                        display: flex;
                        flex-direction: row;
                        gap: 20px;
                    }


                    .details-offre {
                        flex: 3; /* Partie des détails de l'offre */
                        padding-left : 200px;
                        padding: 10px;
                    }

                    .avis-container{
                        display: flex;
                        flex-direction: column;
                        gap: 10px;
                        width: 200%; /* Ajustez à la taille désirée */
                        margin: 0 auto;
                    }

                    .avis-offre {
                        flex: 1; /* Partie des avis */
                        padding: 20px;
                        border-left: 1px solid #ccc; /* Ligne séparatrice */
                    }

                    .avis{
                        border: 1px solid #ccc;
                        width: 100%;
                        padding: 10px;
                        margin-bottom: 10px;
                    }

                    .pdp-name-date {
                        display: flex;
                        align-items: center;
                    }

                    .pdp-name-date strong {
                        margin-left : 10px;
                    }

                    .pdp-avis{
                        width: 50px;
                        height: 50px;
                        border-radius: 50%;
                    }

                    .titre-moy {
                        display: flex;
                        width: 200%;
                        gap : 3.5px;
                        padding-left: 10px;
                    }

                    .titre-moy p {
                        position: relative;
                        left: 5px;
                        top: -10px; /* Ajuste cette valeur pour peaufiner l'alignement */
                    }

                    @font-face {
                        font-family: 'firasans';
                        src: url('./font/firasans-regular-webfont.woff2') format('woff2');
                    }

                </style>


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
