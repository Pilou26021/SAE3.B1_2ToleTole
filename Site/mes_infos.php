<?php
    include "header.php";
    ob_start();
    include "../SQL/connection_local.php";

    $professionel = false;
    $membre = false;
    if (isset($_SESSION['membre'])) {
        $membre = true;
        $idcompte = $_SESSION['membre'];
    } elseif (isset($_SESSION['professionnel'])) {
        $professionel = true;
        $idcompte = $_SESSION['professionnel'];
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
                flex-direction: row;
                justify-content: space-between;  /* Modifié pour espacer les deux sections */
                align-items: flex-start;  /* Aligne les sections au début du conteneur */
                width: 100%;
                padding: 60px 30px 60px 30px;
            }

            .one {
            margin-left: 4%;
            width: 50%;  /* Occupe 60% de la largeur */
            padding: 20px;
            display: flex;
            flex-direction: row;
            justify-content: center;  /* Aligne le contenu en haut */

            }

            .two {
            width: 30%;  /* Occupe 30% de la largeur */
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            box-sizing: border-box;
            }

            /* Style des boutons-liens */
            .liens-boutons {
                width: 90%;
                padding: 10px 20px;
                color: black;
                text-decoration: none;
                font-size: 20px;
                border-radius: 5px;
                text-align: center;
                transition: background-color 0.3s ease;
                background-color: #F2F1E9;
                margin: 10px 0px 10px 0px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                box-sizing: border-box;
                flex-grow: 1;
            }

            #lien_page{
                background-color: #36D673;
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
                width: 20%;
                position: absolute;
                right: 0;
                
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

            /* deuxieme */

            .container {
            width: 90%;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.5);
            text-align: center;
            padding: 0;
        }

        .back-button {
            display: inline-block;
            font-size: 18px;
            background: none;
            border: none;
            color: #333;
            cursor: pointer;
            text-align: left;
            margin-bottom: 20px;
        }

        .back-button:hover {
            color: #666;
        }

        h1 {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
        }

        form{
            justify-content: center;
            align-items: center;
        }

        form label {
            display: block;
            font-size: 14px;
            color: #333;
            margin-bottom: 5px;
            text-align: left;
        }

        form input {
            width: 100%;
            padding: 10px;
           
            border: none;
            border-radius: 5px;
            background-color: #a5d6a7;
            font-size: 14px;
            box-sizing: border-box;
            text-align: center;
        }

        form textarea {
            resize: none;
        }

        form input:focus {
            outline: none;
            border-color: #a5d6a7;
        }

        #toggleButton {
            width: 80%;
            padding: 12px;
            margin: 20px;
            background-color: #81c784;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        #toggleButton:hover {
            background-color: #66bb6a;
        }

        .mes_infos_ligne {
            display: flex;
            flex-direction: row;
            justify-content: center;
            max-width: none;
        }

        .mes_infos_colonne {
            display: flex;
            flex-direction: column;
            margin: 20px;        
            flex: 1;   
        }

        /* Conteneur principal avec un overflow caché */
        .menu-container {
            position: relative;
            width: 100%; /* Largeur du conteneur */
            overflow: hidden; /* Cache les éléments qui dépassent */
            margin: 20px 0;
            padding-left: 50px; /* Espace pour la flèche gauche */
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        /* Liste horizontale des éléments du menu */
        .menu {
            display: flex; /* Affiche les éléments horizontalement */
            list-style: none;
            margin: 0;
            padding: 0;
            transition: transform 0.3s ease; /* Animation de défilement fluide */
        }

        .menu li {
            padding: 10px 20px;
        }

        .menu a {
            text-decoration: none;
            color: white;
            background-color: #333;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .menu a:hover {
            background-color: #555;
        }

        /* Flèches de navigation */
        .arrow {
            position: absolute;
            color: white;
            padding: 10px;
            cursor: pointer;
            font-size: 20px;
            z-index: 10; /* Pour être au-dessus de la liste */
            width: 40px; /* Taille du bouton carré */
            height: 40px; /* Taille du bouton carré */
            border-radius: 2px;
            text-align: center;
            align-self: center;
        }

        /* Flèche gauche */
        .arrow-left {
            left: 0px;
            background: linear-gradient(to right, #4caf50, #ffffff 100%); /* Dégradé du vert au blanc */
            height: 30px;
        }

        /* Flèche droite */
        .arrow-right {
            top: 50%;
            transform: translateY(-50%);
            right: 10px;
            background: radial-gradient(circle, #4caf50 40%, white 60%); /* Dégradé du vert au blanc */
        }

        /* Effet au survol des flèches */
        .arrow:hover {
            background-color: #66bb6a; /* Changement de couleur au survol */
        }

    </style>
</head>
    <body>

        <main>
        <?php
            if (isset($_SESSION['professionnel'])){ ?>
                <!-- deuxieme -->
                <section class="one">
                    <div class="container">
                        <h1>Modification de vos informations personnelles</h1>
                        <form id="infoForm" action="modifier_infos.php" method="POST">
                            <div class="mes_infos_ligne">
                                <div class="mes_infos_colonne">
                                    <label for="nom">Nom</label>
                                    <input type="text" id="nom" name="nom" value="" required readonly>
                                </div> 
                                <div class="mes_infos_colonne">
                                    <label for="prenom">Prénom</label>
                                    <input type="text" id="prenom" name="prenom" value="" required readonly>
                                </div>
                            </div>
                            <div class="mes_infos_ligne">
                                <div class="mes_infos_colonne">
                                    <label for="telephone">Téléphone</label>
                                    <input type="tel" id="tel" name="telephone" value="" required readonly>
                                </div>
                                <div class="mes_infos_colonne">
                                    <label for="email">Email</label>
                                    <input type="text" id="mail" name="email" value="" required readonly>
                                </div>
                            </div>
                            <div class="mes_infos_ligne">
                                <div class="mes_infos_colonne">
                                    <label for="denominationpro">Dénomination professionnelle</label>
                                    <input type="text" id="denomination" name="denominationpro" value="" required readonly>
                                </div>
                                <div class="mes_infos_colonne">
                                    <label for="numsiren">Numéro de siren</label>
                                    <input type="text" id="numsiren" name="numsiren" value="" required readonly>
                                </div>
                            </div>
                            <button type="button" id="toggleButton">Modifier les infos</button>
                        </form>
                    </div>
                </section>
                <section class="two">
                        <a class="liens-boutons" id="lien_page" href="mes_infos.php">Gérer mes informations personnelles</a>
                        <a class="liens-boutons" href="">Gérer mon mot de passe</a>
                        <a class="liens-boutons" href="">Gérer mon coordonnées bancaires</a>
                        <a class="liens-boutons" href="">Consulter mes offres</a>
                        <a class="liens-boutons" href="">Consulter les signalements</a>
                        <a class="liens-boutons" href="">Ajouter une offre</a>
                        <a class="liens-boutons" href="">Mes factures</a>  
                        <a class="liens-boutons" href="">Supprimer mon compte</a>
                </section>
            <?php
            }
            ?>


            <script>
                const form = document.getElementById('infoForm');
                const toggleButton = document.getElementById('toggleButton');
                const inputs = form.querySelectorAll('input');
                let isEditing = false;

                toggleButton.addEventListener('click', () => {
                    if (isEditing) {
                        inputs.forEach(input => input.setAttribute('readonly', 'readonly'));
                        toggleButton.textContent = 'Modifier les infos';
                        form.submit();
                    } else {
                        inputs.forEach(input => input.removeAttribute('readonly'));
                        inputs.forEach(input => input.style.backgroundColor = "#dcedc8");
                        toggleButton.textContent = 'Enregistrer les modifications';
                    }
                    isEditing = !isEditing;
                });
            </script>
        </main>
        <div id="footer"></div>
        <!-- Script foireux -->
        <script>
        const menu = document.getElementById("menu");
        const leftArrow = document.getElementById("arrow-left");
        const rightArrow = document.getElementById("arrow-right");
        const container = document.querySelector(".menu-container");

        let scrollPosition = 0; // Position initiale du scroll

        // Fonction pour faire défiler à gauche
        function scrollLeft() {
            const containerWidth = container.offsetWidth; // Largeur du conteneur
            scrollPosition = Math.max(scrollPosition - containerWidth / 2, 0); // Empêche de dépasser la gauche
            menu.style.transform = `translateX(-${scrollPosition}px)`; // Applique le défilement
        }

        // Fonction pour faire défiler à droite
        function scrollRight() {
            const containerWidth = container.offsetWidth; // Largeur du conteneur
            const scrollWidth = menu.scrollWidth; // Largeur totale du menu
            scrollPosition = Math.min(scrollPosition + containerWidth / 2, scrollWidth - containerWidth); // Empêche de dépasser la droite
            menu.style.transform = `translateX(-${scrollPosition}px)`; // Applique le défilement
        }

        // Ajout des événements de clic sur les flèches
        leftArrow.addEventListener("click", scrollLeft);
        rightArrow.addEventListener("click", scrollRight);
    </script>
    <!-- fin du script foireux -->

        <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
        <script>
            $(function() {
                $("#footer").load("./footer.html");
            });
        </script>
        <script src="./script.js" ></script>
    </body>
</html>
