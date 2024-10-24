<?php 
    include "header.php";
    ob_start();
    include "./SQL/connection_envdev.php";

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
            <?php if ($offre): ?>
                <div class="offre-detail-container">
                    <h1 class="offre-titre"><?= htmlspecialchars($offre['titreoffre']) ?></h1>
                    <div class="offre-image-container">
                        <img class="offre-image" src="<?= !empty($offre['pathimage']) ? htmlspecialchars($offre['pathimage']) : 'img/default.jpg' ?>" alt="Image de l'offre">
                    </div>
                    <p class="offre-resume-detail"><strong>Résumé:</strong> <?= htmlspecialchars($offre['resumeoffre']) ?></p>
                    <p class="offre-resume-detail"><strong>Description:</strong> <?= htmlspecialchars($offre['descriptionoffre']) ?></p>

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

                        // 2) On affiche les détails de l'offre
                        /*
                        _offreactivite:
                        idoffre,indicationduree,ageminimum,prestationincluse
                        
                        _offreparcattraction:
                        idoffre,indicationduree,ageminimum,prestationincluse
                        
                        _offrerestaurant:
                        idoffre,horairesemaine,gammeprix,carteresto
                        horairesemaine est dans ce format : {"lunchOpen":"12:30","lunchClose":"14:00","dinnerOpen":"22:00","dinnerClose":"00:00"}

                        _offrespectacle:
                        idoffre,dateoffre,indicationduree,capaciteacceuil

                        _offrevisite:
                        idoffre,dateoffre,visiteguidee,langueproposees
                        */    
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
                                                <td><?php echo htmlspecialchars($offreDetails['indicationduree']); ?></td>
                                                <td><?php echo htmlspecialchars($offreDetails['ageminimum']); ?></td>
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
                                                <td><?php echo htmlspecialchars($offreDetails['indicationduree']); ?></td>
                                                <td><?php echo htmlspecialchars($offreDetails['ageminimum']); ?></td>
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
                                                <th>Carte resto</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><?php echo "Déjeuner : " . $horaires['lunchOpen'] . " - " . $horaires['lunchClose'] . "<br>Dîner : " . $horaires['dinnerOpen'] . " - " . $horaires['dinnerClose']; ?></td>
                                                <td><?php echo htmlspecialchars($offreDetails['gammeprix']); ?></td>
                                                <td><?php echo htmlspecialchars($offreDetails['carteresto']); ?></td>
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
                                                <td><?php echo htmlspecialchars($offreDetails['indicationduree']); ?></td>
                                                <td><?php echo htmlspecialchars($offreDetails['capaciteacceuil']); ?></td>
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

        <script src="script.js"></script> 
    </body>
    <?php include "footer.html"; ?>
</html>
