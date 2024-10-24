<?php 
    include "header.php";
    ob_start();
    include("../SQL/connection_local.php");

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
                    SELECT o.idoffre, o.titreoffre, o.resumeoffre, o.prixminoffre, o.horsligne, i.pathimage
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


        $rue = $adresse_offre["numrue"]; // Nom de la rue
        $code_postal = $adresse_offre["codepostal"]; // Code postal
        $ville = $adresse_offre["ville"]; // Ville
        $departement = $adresse_offre["departement"]; // Département (ex : Californie)
        $pays = $adresse_offre["pays"]; // Pays
        $supplement = $adresse_offre["supplementadresse"];
        
        // Adresse pour l'affichage sur la carte
        $adresse = trim("$rue, $code_postal $ville, $departement, $pays");
        // Adresse plus complète pour l'utilisateur
        $adresseComplete = trim("$rue, $supplement, $code_postal $ville, $departement, $pays");
        ?>
        
        <main>
            <?php if ($offre): ?>
                <div class="offre-detail-container">
                    <h1 class="offre-titre"><?= htmlspecialchars($offre['titreoffre']) ?></h1>
                    <div class="offre-image-container">
                        <img class="offre-image" src="<?= !empty($offre['pathimage']) ? htmlspecialchars($offre['pathimage']) : 'img/default.jpg' ?>" alt="Image de l'offre">
                    </div>
                    <p class="offre-resume"><strong>Résumé:</strong> <?= htmlspecialchars($offre['resumeoffre']) ?></p>

                    <p>Localisation de l'offre</p>
                    <div id="map" style="display:flex;align-items:center;justify-content:center;">
                        <h2 id="text-chargement" >Chargement de la carte</h2>
                    </div>
                    <p><?php echo $adresseComplete ?><p>

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

                    <?php
                
                        // 1) On cherche d'abord dans quelle table se trouve l'ID Offre
                        $tables = [
                            '_offreactivite',
                            '_offreparcattraction',
                            '_offrerestaurant',
                            '_offrespectacle',
                            '_offrevisite'
                        ];

                        foreach ($tables as $table) {

                            $chercher_table = $conn->prepare("SELECT 1 FROM public.$table WHERE idoffre = :idoffre LIMIT 1");
                            $chercher_table-> bindValue(':idoffre', $idoffre, PDO::PARAM_INT);
                            $chercher_table-> execute();

                            if ($chercher_table->rowCount() > 0) {
                                $table_trouvee = $table; // Enregistrer la table trouvée
                                break; // Sortir de la boucle
                            }
                        }

                        // 2) On en déduit le champ qui contient la donnée qui nous intéresse
                        switch ($table_trouvee) {
                            case '_offreactivite':
                                $champ = 'indicationduree';
                                $duree = $conn->prepare("SELECT $champ FROM public.$table_trouvee WHERE idoffre = :idoffre LIMIT 1");
                                $duree-> bindValue(':idoffre', $idoffre, PDO::PARAM_INT);
                                $duree-> execute();

                                $duree_trouvee = $duree->fetch(PDO::FETCH_ASSOC);
                                
                                echo "<p> Durée de l'activité: " . $duree_trouvee["indicationduree"] . "h" . "<p>";
                                break;
                                
                            case '_offreparcattraction':
                                $champ_1 = 'dateouverture';
                                $champ_2 = 'datefermeture';
                                $duree = $conn->prepare("SELECT $champ_1, $champ_2 FROM sae._offreparcattraction WHERE idoffre = :idoffre LIMIT 1");
                                $duree-> bindValue(':idoffre', $idoffre, PDO::PARAM_INT);
                                $duree-> execute();
                                
                                $duree_trouvee = $duree->fetch(PDO::FETCH_ASSOC);
                                
                                echo "<p> Date d'ouverture: " . $duree_trouvee["dateouverture"] ."<p>";
                                echo "<p> Date de fermeture: " . $duree_trouvee["datefermeture"] ."<p>";
                                break;
                        
                            case '_offrerestaurant':
                                $champ = 'horairesemaine';
                                $duree = $conn->prepare("SELECT $champ FROM sae._offrerestaurant WHERE idoffre = :idoffre LIMIT 1");
                                $duree-> bindValue(':idoffre', $idoffre, PDO::PARAM_INT);
                                $duree-> execute();
                                
                                $duree_trouvee = $duree->fetch(PDO::FETCH_ASSOC);

                                echo "<p> Horaires hebdomadaires: " . $duree_trouvee["horairesemaine"] ."<p>";
                                break;
                        
                            case '_offrespectacle':
                            case '_offrevisite':
                                $champ = 'dateoffre';
                                $duree = $conn->prepare("SELECT $champ FROM sae.$table_trouvee WHERE idoffre = :idoffre LIMIT 1");
                                $duree-> bindValue(':idoffre', $idoffre, PDO::PARAM_INT);
                                $duree-> execute();
                                
                                $duree_trouvee = $duree->fetch(PDO::FETCH_ASSOC);

                                echo "<p> Date de l'offre: " . $duree_trouvee["dateoffre"] ."<p>";
                                break;
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
</html>
