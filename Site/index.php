<?php 
include "header.php";
ob_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
    <title>Offres</title>
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
    <div id="header"></div>

    <main>
        <div class="recherche">
            <form action="">
                <div class="recherche_top">
                    <img src="img/Search.png" alt="Search">
                    <input class="input" placeholder="Votre recherche" type="text">
                    <img src="img/filtre.png" alt="Filtre" id="filterBtn">
                </div>
                <hr>
                <div>
                    <input class="button_1" type="submit" value="Recherche" >
                </div>
            </form>
        </div>
        <div id="filterForm" class="filter-form">
            <h3> Filtres</h3>
            <form action="#">
                <label for="category">Catégorie :</label>
                <select class="choose" id="category" name="category">
                    <option value="">--Choisissez une option--</option>
                    <option value="Restauration">Restauration</option>
                    <option value="Spectacles">Spectacles</option>
                    <option value="Visites">Visites</option>
                    <option value="Activités">Activités</option>
                    <option value="Parcs">Parcs d’attractions</option>
                </select>

                <label for="lieux">Lieux :</label>
                <div style="display: flex; align-items:center; justify-content: space-around;">
                    <input class="input-filtre"  style="width: 60%;" type="text" placeholder="Lieux (Rennes) ">
                    <div class="slider-container" style="width: 30%;">
                        <div class="price-label">
                            Rayon: <span id="rayon-value">25</span>km
                        </div>
                        
                        <input type="range" id="rayon-range" class="slider" min="0" max="100" step="5" value="25">
                        
                        <div class="range-values">
                            <span class="range-value">0km</span>
                            <span class="range-value">100km</span>
                        </div>
                    </div>
                </div>

                <label for="date">Date :</label>
                <div style="display: flex; align-items:center; justify-content: space-around;">
                    <input id="datedeb" class="input-filtre"  style="width: 40%;" type="date" >
                    <input id="datefin" class="input-filtre"  style="width: 40%;" type="date" >
                </div>

                <label for="priceRange">Gamme de prix :</label>
                <div class="slider-container">
                    <div class="price-label">
                        Prix: <span id="price-value">500</span>€
                    </div>
                    
                    <input type="range" id="price-range" class="slider" min="100" max="1000" step="50" value="500">
                    
                    <div class="range-values">
                        <span class="range-value">100€</span>
                        <span class="range-value">1000€</span>
                    </div>
                </div>


                <label for="sort">Notes :</label>
                <div style="display: flex; justify-content: space-around;">
                    <select  class="choose" id="notemin" name="notemin" style="width: 30%; height: 30px;">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                    <p>a</p>
                    <select  class="choose" id="notemax" name="notemax" style="width: 30%; height: 30px;">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </div>
                <h3>Tri</h3>

                <label for="TrieC">Tri par Prix  :</label>
                <select class="choose" id="Tprix" name="Tprix">
                    <option value="">--Choisissez une option--</option>
                    <option value="CroissantP">Tri par ordre Croissant</option>
                    <option value="DecroissantP">Tri par ordre Decroissant</option>
                </select>

                <label for="TrieC">Tri par Notes  :</label>
                <select class="choose" id="Tnote" name="Tnote">
                    <option value="">--Choisissez une option--</option>
                    <option value="CroissantN">Tri par ordre Croissant</option>
                    <option value="DecroissantN">Tri par ordre Decroissant</option>
                </select>

                <div style="display: flex; justify-content: center;">
                    <input class="button_1" type="submit" value="Appliquer">
                </div>
            </form>
        </div>
    </main>
    
    <div id="footer"></div>

    <script src="script.js"></script> 
</body>
</html>