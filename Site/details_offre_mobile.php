<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Détails de l'Offre</title>
    <link rel="stylesheet" href="details_offre_mobile.css">
</head>

    <body class="details_offre_mobile">
            <script
                src="https://code.jquery.com/jquery-3.3.1.js"
                integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
                crossorigin="anonymous">
            </script>
            <script> 
                $(function(){
                $("#header").load("./header.html"); 
                $("#footer").load("footer.html"); 
                });
            </script> 

            <div id="header"></div>

            <!-- Croix en haut à droite -->
            <div class="details_offre_mobile_croix">
                <a href="#">&#x2716;</a>
            </div>

            <!-- Titre -->
            <h1 class="details_offre_mobile">Titre de l'Offre</h1>

            <!-- Professionnel à l'origine de l'offre -->
            <p class="details_offre_mobile">Proposé par Nom du professionnel</p>

            <!-- Nombre d'avis et note globale -->
            <div class="details_offre_mobile_nbAvis">
                <p>★★★★☆</p>
                <p>120 Avis</p>
                <p>Signaler</p>
            </div>

            <!-- Illustration -->
            <img src="image-offre.jpg" alt="Illustration de l'offre">

            <!-- Description détaillée -->
            <p>Voici une description complète de l'offre...</p>

            <!-- Plan -->
            <img src="plan.jpg" alt="Plan de l'offre">

            <!-- Section Grille tarifaire -->
            <section class="details_offre_mobile_tarifs">
                <h3>Grille tarifaire</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Tarif Adulte</th>
                            <th>Tarif Enfant</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>15 €</td>
                            <td>10 €</td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <!-- Périodes d'ouverture -->
            <section class="details_offre_mobile_calendrier">
                <h3>Périodes d'ouverture</h3>
                <p>Lundi - Vendredi : 9h - 18h</p>
                <p>Samedi : 10h - 16h</p>
                <p>Dimanche : Fermé</p>
            </section>

            <!-- Avis -->
            <section class="details_offre_mobile_avis">
                <h2>Avis</h2>
                <p>Avis de l'utilisateur 1 : Très bon service !</p>
                <p>Avis de l'utilisateur 2 : J'ai adoré cette offre !</p>
            </section>

            <!-- Contacts -->
            <div class="contact-info">
                <h2>Contacts</h2>
                <p>Téléphone : 01 23 45 67 89</p>
                <p>Email : contact@offre.com</p>
            </div>

            <div id="footer"></div>
    </body>
</html>