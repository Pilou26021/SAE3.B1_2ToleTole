<?php

    include "header.php";
    ob_start();
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
        <title>Mon Compte</title>
        
        <style>
            main {
                background-color: #F2F1E9;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                text-align: center;
            }

            /* CSS pour la partie photo de profil */
            img {
                max-width: 100%;
                height: auto;
            }

            .image-edit-btn {
                display: inline-block;
                position: relative;
            }

            .edit-btn {
                width: 30px;
                height: 30px;
                cursor: pointer;
                position: absolute;
                bottom: 0;
                right: 0;
            }

            /* Styles pour la modale */
            .modal {
                display: none;
                position: fixed;
                z-index: 1;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                justify-content: center;
                align-items: center;
            }

            .modal-content {
                background-color: white;
                min-width: 300px;
                padding: 20px;
                border-radius: 5px;
                width: 20%;
                text-align: center;

            }

            .modal-content-btn {
                display: flex;
                flex-direction: column;
                align-items: center;

            }

            .close-btn {
                color: #aaa;
                float: right;
                font-size: 28px;
                font-weight: bold;
                cursor: pointer;
            }

            .close-btn:hover,
            .close-btn:focus {
                color: black;
            }

            input[type="file"] {
                margin-top: 20px;
            }

            .modal-footer button {
                margin-top: 15px;
            }

            /* CSS pour le bloc central des boutons-liens */
            .dates_inscr{
                font-size: 20px;
                margin: auto auto 5px auto;
            }

            #afficher_cat{
                font-size: 15px;
                margin: 0px auto 25px auto;
            }

            /* Style des boutons-liens */
            .liens-boutons {
                width: 100%;
                padding: 10px 20px;
                color: black;
                text-decoration: none;
                font-size: 20px;
                border-radius: 5px;
                text-align: center;
                transition: background-color 0.3s ease;
                background-color: #F2F1E9;
                border: 1px solid #36D673;
                margin: 10px 20px 10px 20px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
                box-sizing: border-box;
                flex-grow: 1;
            }

            .liens-boutons:visited {
                color: inherit;
            }

            .liens-boutons:hover {
                color: inherit;
                background-color: #36D673;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.6);
            }

            /* Classes CSS concernant la disposition du conteneur et des ses éléments */
            .creer_ligne {
                display: flex;
                flex-direction: row;
                width: 100%;
            }

            .creer_colonne {
                display: flex;
                flex-direction: column;
                align-items: center;
                margin: 20px;  
                width: 100%;
            }

            .conteneur-boutons {
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                width: 60%;
                min-width: 500px;
                margin: 20px;  
            }

            .conteneur-gauche{
                margin: 0 10px 0 0;
            }

            .conteneur-droit{
                margin: 0 0 0 10px;
            }

            @media (max-width: 1000px){

                p{
                    font-size: 15px;
                }

                #afficher_cat{
                    font-size: 10px;
                }

                h2{
                    font-size: 20px;
                }

                .creer_ligne {
                    flex-direction: column;
                }

                .creer_colonne {
                    margin: 0;  
                }

                .conteneur-gauche{
                    margin: 0;
                }

                .conteneur-droit{
                    margin: 0;
                }

                .liens-boutons {

                    font-size: 15px;
                    padding: 20px 20px;
                }

                .conteneur-boutons {
                    min-width: 300px;
                }
            }
        </style>
        
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
        <main>
            <h1>Mon Compte</h1>
            
            <!-- ===========================
               PARTIE PHOTO DE PROFIL
            =========================== -->

            <?php 
                if (isset($_SESSION['membre'])) {
                    $idcompte = $idmembre;
                } elseif (isset($_SESSION['professionnel'])) {
                    $idcompte = $idpro;
                }            

                //requete pour récupérer l'image de l'utilisateur
                $sql = "SELECT i.pathimage, c.*, numtelcompte FROM _image i JOIN _compte c 
                        ON i.idimage = c.idimagepdp
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
            <div id="imageModal" class="modal">
                <div class="modal-content">
                    <span class="close-btn">&times;</span>
                    <h2>Modifier votre photo de profil</h2>
                    <form id="editImageForm" action="upload_profile_pic.php" method="post" enctype="multipart/form-data">
                        <div class="modal-content-btn">
                            <input class="offer-btn" type="file" name="newProfileImage" accept="image/*" required>
                            <div class="modal-footer">
                                <button type="submit" class="offer-btn">Enregistrer</button>
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

            <h2><?php echo $res[0]['prenomcompte'] . " " . $res[0]['nomcompte']?></h2>

            <?php if (isset($_SESSION['professionnel'])){
                echo "<p id='afficher_cat'> Professionnel </p>";
            } else{
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

                <p class="dates_inscr">Vous êtes inscris depuis le <?php echo $jourCrea . " " . $moisCrea . " " . $anneeCrea . " et votre dernière connexion remonte au " . $jourCo . " " . $moisCo . " " . $anneeCo?></p>
            </section>

            <?php
            // Affichage du professionnel
            if (isset($_SESSION['professionnel'])){ ?>
                <!-- Conteneur des boutons-liens -->
                <div class="conteneur-boutons">
                    <section class="creer_ligne">
                        <div class="creer_colonne conteneur-gauche">
                            <a class="liens-boutons" href="mes_infos.php">Gérer mes informations personnelles</a>
                            <a class="liens-boutons" href="">Gérer mon mot de passe</a>
                            <a class="liens-boutons" href="">Gérer mon coordonnées bancaires</a>
                        </div>

                        <div class="creer_colonne conteneur-droit">
                            <a class="liens-boutons" href="">Consulter mes offres</a>
                            <a class="liens-boutons" href="">Consulter les signalements</a>
                            <a class="liens-boutons" href="">Ajouter une offre</a>  
                        </div>
                    </section>

                    <a class="liens-boutons" href="">Mes factures</a>  
                    <a class="liens-boutons" href="">Supprimer mon compte</a>
                </div>
            <?php 
            // Affichage du membre
            } else { ?>
                <!-- Conteneur des boutons-liens -->
                <div class="conteneur-boutons">
                    <section class="creer_ligne">
                        <div class="creer_colonne conteneur-gauche">
                            <a class="liens-boutons" href="mes_infos.php">Gérer mes informations personnelles</a>
                            <a class="liens-boutons" href="">Gérer mon mot de passe</a>
                        </div>
                        <div class="creer_colonne conteneur-droit">
                            <a class="liens-boutons" href="">Consulter mes visites</a>
                            <a class="liens-boutons" href="">Aide</a>
                        </div>
                    </section>
            
                    <a class="liens-boutons" href="">Supprimer mon compte</a>
                </div>
            <?php
            } ?>
                
            
            <br>
            <!-- Bouton de retour à l'accueil-->
            <a style="text-decoration:none;" href="index.php"> <button class="offer-btn">Retour à la page d'Accueil</button></a>
        </main>
        <div id="footer"></div>
    </body>
</html>
