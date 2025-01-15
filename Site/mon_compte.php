<?php

    ob_start();

    include "header.php";
    include "../SQL/connection_local.php";

    $professionel = false;
    $membre = false;
    if (isset($_SESSION['membre'])) {
        $membre = true;
        $idmembre = $_SESSION['membre'];
        $userID = $_SESSION['membre'];

    } elseif (isset($_SESSION['professionnel'])) {
        $professionel = true;
        $idpro = $_SESSION['professionnel'];
        $userID = $_SESSION['professionnel'];

        // On vérifie si c'est un pro public ou privé
        $sql = "SELECT idpro
                FROM professionnelpublic
                WHERE idpro = :id
                ;";

        // Préparer et exécuter la requête
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id', $userID, PDO::PARAM_INT);
        $stmt->execute();
        $res = $stmt->fetchAll();

        foreach($res as $resultat){

            if ($resultat['idpro'] == $userID && $resultat['idpro'] !== 1){
                $_SESSION['typePro'] = "publique";
            }
    
            else{
                $_SESSION['typePro'] = "prive";
            }  
        }

    }
?>


<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <link rel="stylesheet" href="./style.css">   
        <title>Mon Compte</title>
        
    </head>
    <body>

        <!-- Chargement du header et du footer -->
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

        <div id="header"></div>

        <!-- Flèche de retour à mon_compte -->
        <div style=" position:sticky; top:20px; left:20px; width: 100%;">
                <a style="text-decoration: none; font-size: 30px; color: #040316; cursor: pointer;" href="./index.php">&#8617;</a>
        </div>
        
        <main class="mc_main">
            <h1>Mon Compte</h1>
            
            <!-- ===========================
               PARTIE PHOTO DE PROFIL
            =========================== -->

            <?php 
                if (isset($_SESSION['membre'])) {
                    $idcompte = $idmembre;

                    $sql = "SELECT pseudonyme 
                            FROM _membre
                            WHERE idcompte = :idcompte";
                    
                    $stmt = $conn->prepare($sql);
                    $stmt->bindValue(':idcompte', $idcompte, PDO::PARAM_INT);
                    $stmt->execute();
                    $pseudo = $stmt->fetch();

                } elseif (isset($_SESSION['professionnel'])) {
                    $idcompte = $idpro;
                }            

                //requete pour récupérer l'image de l'utilisateur
                $sql = "SELECT i.pathimage, c.*, numtelcompte FROM _image i 
                        JOIN _compte c ON i.idimage = c.idimagepdp
                        WHERE idcompte = :idcompte";

                // Préparer et exécuter la requête
                $stmt = $conn->prepare($sql);
                $stmt->bindValue(':idcompte', $idcompte, PDO::PARAM_INT);
                $stmt->execute();

                $res = $stmt->fetchAll();
            ?>

            <div class="image-edit-btn">
                <img src="<?php echo $res[0]['pathimage']; ?>" alt="Photo de profil" style="width: 200px; height: 200px; border-radius: 50%;">
                <!-- Bouton d'édition -->
                <img class="edit-btn" src="./img/icons/edit.svg" alt="Éditer">
            </div>

            <!-- Modale pour l'upload de l'image -->
            <div id="imageModal" class="imageModal">
                <div class="image-modal-content">
                    <span class="close-btn">&times;</span>
                    <h2>Modifier votre photo de profil</h2>
                    <form id="editImageForm" action="upload_profile_pic.php" method="post" enctype="multipart/form-data">
                        <div class="modal-content-btn">
                            <input class="offer-btn <?php echo $professionel ? 'professionnel' : ($membre ? 'membre' : 'guest'); ?>" type="file" name="newProfileImage" accept="image/*" required>
                            <div class="modal-footer">
                                <button type="submit" class="offer-btn <?php echo $professionel ? 'professionnel' : ($membre ? 'membre' : 'guest'); ?>">Enregistrer</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
            <script>
                $(function() {
                    $("#footer").load("./footer.html");
                });

                // Récupération des éléments modale
                const modal = document.getElementById("imageModal");
                const btn = document.querySelector(".edit-btn");
                const closeBtn = document.querySelector(".close-btn");

                // Ouverture du popup lorsque l'utilisateur clique sur le bouton 
                btn.onclick = function() {
                    modal.style.display = "flex";
                }

                // Fermeture du popup lorsqu'on clique sur le bouton de fermeture
                closeBtn.onclick = function() {
                    modal.style.display = "none";
                }

                // Fermeture du popup lorsqu'on clique en dehors du contenu
                window.onclick = function(event) {
                    if (event.target == modal) {
                        modal.style.display = "none";
                    }
                }

            </script>
            <script src="script.js"></script> 

            <!-- =============================
               PARTIE CENTRALE BOUTONS-LIENS
            ============================= -->

            <?php if (isset($_SESSION['professionnel'])){

                if ($_SESSION['typePro'] == 'publique'){
                    ?><h2><?php echo $res[0]['prenomcompte'] . " " . $res[0]['nomcompte'] ?></h2><?php
                    echo "<p id='afficher_cat'> Professionnel public </p>";
                }
                else{
                    ?><h2><?php echo $res[0]['prenomcompte'] . " " . $res[0]['nomcompte'] ?></h2><?php
                    echo "<p id='afficher_cat'> Professionnel privé </p>";
                }
            } else{
                    ?><h2><?php echo $res[0]['prenomcompte'] . " " . $res[0]['nomcompte'] . " | " . $pseudo['pseudonyme'] ?></h2><?php
                    echo "<p id='afficher_cat'> Membre </p>";
            }
            ?>

            <section>
                <!-- Gestion de l'affichage de la date -->
                <?php
                $dateCreation = $res[0]['datecreationcompte'];
                $dateConnexion = $res[0]['datederniereconnexioncompte'];

            
                // Convertir la date en timestamp
                $timestampCreation = strtotime($dateCreation);
                $timestampConnexion = strtotime($dateConnexion);

                $mois_francais = [
                    '01' => 'janvier', '02' => 'février', '03' => 'mars', '04' => 'avril',
                    '05' => 'mai', '06' => 'juin', '07' => 'juillet', '08' => 'août',
                    '09' => 'septembre', '10' => 'octobre', '11' => 'novembre', '12' => 'décembre'
                ];
            
                $jourCrea = date('d', $timestampCreation);
                $moisCrea = $mois_francais[date('m', $timestampCreation)];
                $anneeCrea = date('Y', $timestampCreation);

                $jourCo = date('d', $timestampConnexion);
                $moisCo = $mois_francais[date('m', $timestampConnexion)];
                $anneeCo = date('Y', $timestampConnexion);
            
                ?>

                <p class="p-moncompte">Vous êtes inscris depuis le <?php echo $jourCrea . " " . $moisCrea . " " . $anneeCrea . " et votre dernière connexion remonte au " . $jourCo . " " . $moisCo . " " . $anneeCo?></p>
            </section>

            <?php
            // Affichage du professionnel
            if (isset($_SESSION['professionnel'])){ ?>
                <?php if ($_SESSION['typePro'] == 'prive'){ ?>
                    <!-- Conteneur des boutons-liens -->
                    <div class="conteneur-boutons">
                        <section class="creer_ligne">
                            <!-- <div class="creer_colonne conteneur-gauche"> -->
                                <a class="liens-boutons <?php echo $professionel ? 'professionnel' : ($membre ? 'membre' : 'guest'); ?>" href="mes_infos.php">Gérer mes informations personnelles</a>
                                <!-- <a class="liens-boutons" href="">Gérer mon mot de passe</a> -->
                                <a class="liens-boutons <?php echo $professionel ? 'professionnel' : ($membre ? 'membre' : 'guest'); ?>" href="mes_infos_bancaires.php">Gérer mes coordonnées bancaires</a>
                            <!-- </div> -->

                            <!-- <div class="creer_colonne conteneur-droit"> -->
                                <!-- <a class="liens-boutons" href="">Consulter mes offres</a>
                                <a class="liens-boutons" href="">Consulter les signalements</a>
                                <a class="liens-boutons" href="">Ajouter une offre</a>   -->
                            <!-- </div> -->
                        </section>
                        <section class="creer_ligne">
                            <a class="liens-boutons <?php echo $professionel ? 'professionnel' : ($membre ? 'membre' : 'guest'); ?>" href="avis_mes_offres.php">Consulter les avis sur mes offres</a>
                            <a class="liens-boutons <?php echo $professionel ? 'professionnel' : ($membre ? 'membre' : 'guest'); ?>" href="securite.php">Gérer mon mot de passe</a> 
                        </section>

                        <!-- <a class="liens-boutons" href="">Mes factures</a>  
                        <a class="liens-boutons" href="">Supprimer mon compte</a> -->
                    </div>
                <?php } ?>

                <?php if ($_SESSION['typePro'] == 'publique'){ ?>
                    <!-- Conteneur des boutons-liens -->
                    <div class="conteneur-boutons">
                        <section class="creer_ligne">
                            <!-- <div class="creer_colonne conteneur-gauche"> -->
                                <a class="liens-boutons <?php echo $professionel ? 'professionnel' : ($membre ? 'membre' : 'guest'); ?>" href="mes_infos.php">Gérer mes informations personnelles</a>
                                <!-- <a class="liens-boutons" href="">Gérer mon mot de passe</a> -->
                            <!-- </div> -->

                            <!-- <div class="creer_colonne conteneur-droit"> -->
                                <!-- <a class="liens-boutons" href="">Consulter mes offres</a>
                                <a class="liens-boutons" href="">Ajouter une offre</a>   -->
                            <!-- </div> -->
                        </section>
                        <section class="creer_ligne">
                            <a class="liens-boutons <?php echo $professionel ? 'professionnel' : ($membre ? 'membre' : 'guest'); ?>" href="avis_mes_offres.php">Consulter les avis sur mes offres</a>
                            <a class="liens-boutons <?php echo $professionel ? 'professionnel' : ($membre ? 'membre' : 'guest'); ?>" href="securite.php">Gérer mon mot de passe</a> 

                        </section>

                        <!-- <a class="liens-boutons" href="">Consulter les signalements</a>
                        <a class="liens-boutons" href="">Supprimer mon compte</a> -->
                    </div>
                <?php } ?>
            <?php 
            // Affichage du membre
            } else { ?>
                <!-- Conteneur des boutons-liens -->
                <div class="conteneur-boutons">
                    <section class="creer_ligne">
                        <!-- <div class="creer_colonne conteneur-gauche"> -->
                            <a class="liens-boutons" href="mes_infos.php">Gérer mes informations personnelles</a>
                            <!-- <a class="liens-boutons" href="">Gérer mon mot de passe</a> -->
                        <!-- </div> -->
                        <!-- <div class="creer_colonne conteneur-droit"> -->
                            <!-- <a class="liens-boutons" href="">Consulter mes visites</a>
                            <a class="liens-boutons" href="">Aide</a> -->
                        <!-- </div> -->
                    </section>
            
                    <!-- <a class="liens-boutons" href="">Supprimer mon compte</a> -->
                </div>
            <?php
            } ?>
                
        </main>
        <div id="footer"></div>
    </body>
</html>
