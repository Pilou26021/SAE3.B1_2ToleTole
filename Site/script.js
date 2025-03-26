const priceRangeMax = document.getElementById("price-range-max");
const priceValueMax = document.getElementById("price-value-max");
const priceRangeMin = document.getElementById("price-range-min");
const priceValueMin = document.getElementById("price-value-min");
const rayonRange = document.getElementById("rayon-range");
const rayonValue = document.getElementById("rayon-value");
const notemin = document.getElementById('notemin');
const notemax = document.getElementById('notemax');
const datedeb = document.getElementById('datedeb');
const datefin = document.getElementById('datefin');

function openNav() {
    document.getElementById("mySidenav").style.width = "300px";
}

function closeNav() {
    document.getElementById("mySidenav").style.width = "0";
}

function adjustValues() {
    let minValue = parseInt(notemin.value);
    let maxValue = parseInt(notemax.value);

    if (minValue > maxValue) {
        notemax.value = minValue;
    }
}

function adjustDates() {
    const startDate = new Date(datedeb.value);
    const endDate = new Date(datefin.value);

    if (startDate > endDate) {
        datefin.value = datedeb.value; 
    }
}

$(document).ready(function() {
    // Ouvrir le filtre
    $("#filterBtn").click(function(event) {
        event.stopPropagation();
        $("#filterForm").addClass("open");
    });

    // Fermer à l'aide de la croix
    $(".filter-close").click(function(event) {
        event.stopPropagation();
        $("#filterForm").removeClass("open");
    });

    // Fermer en cliquant en dehors
    $(document).click(function(event) {
        if (!$(event.target).closest("#filterForm, #filterBtn").length) {
            $("#filterForm").removeClass("open");
        }
    });

    // Empêcher le clic sur le panneau lui-même de fermer le panneau
    $("#filterForm").click(function(event) {
        event.stopPropagation();
    });
});



priceRangeMax.addEventListener("input", function() {
    // Empêcher priceRangeMax d'être inférieur à priceRangeMin
    if (parseInt(priceRangeMax.value) < parseInt(priceRangeMin.value)) {
        priceRangeMax.value = priceRangeMin.value;
    }
    priceValueMax.textContent = priceRangeMax.value;
});

priceRangeMin.addEventListener("input", function() {
    // Empêcher priceRangeMin d'être supérieur à priceRangeMax
    if (parseInt(priceRangeMin.value) > parseInt(priceRangeMax.value)) {
        priceRangeMin.value = priceRangeMax.value;
    }
    priceValueMin.textContent = priceRangeMin.value;
});

rayonRange.addEventListener("input", function() {
    rayonValue.textContent = rayonRange.value;
});

notemin.addEventListener('change', adjustValues);
notemax.addEventListener('change', adjustValues);
datedeb.addEventListener('change', adjustDates);
datefin.addEventListener('change', adjustDates);

/* CREER OFFRE */
function checkNegativeValue(input) {
    const errorMessage = document.getElementById('error-' + input.id);
    if (input.value < 0) {
        input.value = "";
        errorMessage.style.display = 'block';
        errorMessage.style.color = 'red';
    } else {
        errorMessage.style.display = 'none';
    }
}

function preventInvalidChars(event) {
    const invalidChars = ['-', '+'];
    if (invalidChars.includes(event.key)) {
        event.preventDefault();
    }
}

function checkValidWebsite(input){
    const errorMessage = document.getElementById('error-' + input.id);
    const regex = /^(http[s]?:\/\/)?([\da-z.-]+)\.([a-z.]{2,6})([\/\w.-]*)*\/?$/;
    if (!regex.test(input.value)) {
        errorMessage.style.display = 'block';
        errorMessage.style.color = 'red';
    } else {
        errorMessage.style.display = 'none';
    }
}

function checkCodePostal(input) {
    const maxLength = 4;
    let value = input.value;

    // Affiche le message d'erreur si le code postal est trop long
    const errorElement = document.getElementById('error-' + input.id);
    if (value.length > maxLength) {
        errorElement.style.display = 'block';
        errorMessage.style.color = 'red';
    } else {
        errorElement.style.display = 'none';
    }

    // Efface les caractères en trop après l'affichage du message d'erreur
    if (value.length > maxLength) {
        input.value = value.slice(0, maxLength);
    }
}

//update la catégorie après le click dans le dropdown
function updateCategory() {
    var selectedCategory = document.getElementById('categorie').value;
    window.location.href = 'creer_offre.php?categorie=' + selectedCategory;
}

// Fonction pour afficher le dropdown
function showDropdown() {
    document.getElementById("dropdown-content").style.display = "block";
}

// Fonction pour cacher le dropdown
function hideDropdown() {
    document.getElementById("dropdown-content").style.display = "none";
}

//changer le texte du bouton quand on hover sur une option
function changeButtonText(element) {
    document.getElementById("dropdown-btn").innerText = element.innerText;
}

function getTextFromCat(cat){
    switch (cat) {
        case 'restauration':
            return "Restauration";
        case 'spectacle':
            return "Spectacle";
        case 'visite':
            return "Visite";
        case 'activite':
            return "Activité";
        case 'parc':
            return "Parc d'attractions";
        default :
            return "---";
    }
}

function resetButtonText(cat) {
    console.log(cat);
    let urlParams = new URLSearchParams(window.location.search);
    let catJS = urlParams.get('categorie');
    if(catJS == "restauration" || catJS == "spectacle" || catJS == "activite" || catJS == "parc" || catJS == "visite") {
        document.getElementById("dropdown-btn").innerText = getTextFromCat(catJS);
    } else {
        document.getElementById("dropdown-btn").innerText = "Choisir une catégorie";
    }
}

//si restauration est séléctionné dans les filtres, afficher la checkbox dateouvert
function showDateOuvert() {
    if (document.getElementById('category').value == 'Restauration') {
        document.getElementById('switch').style.display = 'inline-block';
        document.getElementById('textedateouvert').style.display = 'block';
        document.getElementById('category').style.width = '30vw';
    } else {
        document.getElementById('switch').style.display = 'none';
        document.getElementById('textedateouvert').style.display = 'none';
        document.getElementById('category').style.width = '34.7vw';
    }
}

var map;

// FILTRES

async function applyFilters() {
    // Récupérer les valeurs des filtres
    const category = document.getElementById('category').value;
    const mavant = document.getElementById('Mavant').value;
    const type = document.getElementById('type').value;
    const lieux = document.getElementById('lieux').value;
    const minPrice = document.getElementById("price-range-min").value;
    const maxPrice = document.getElementById("price-range-max").value;
    const notemin = document.getElementById('notemin').value;
    const notemax = document.getElementById('notemax').value;
    const datedeb = document.getElementById('datedeb').value;
    const datefin = document.getElementById('datefin').value;
    const search = document.getElementById('search-query').value;
    const startDate = document.getElementById('datedeb').value;
    const endDate = document.getElementById('datefin').value;
    const ouvert = document.getElementById('ouvert').value;
    let Tprix = document.getElementById('Tprix').value;

    

    

    // Préparer les paramètres de filtre
    const filters = new URLSearchParams();
    filters.append('category', category);
    filters.append('lieux', lieux);
    filters.append('minPrice', minPrice);
    filters.append('maxPrice', maxPrice);
    filters.append('notemin', notemin);
    filters.append('notemax', notemax);
    filters.append('datedeb', datedeb);
    filters.append('datefin', datefin);
    filters.append('search', search);
    filters.append('startDate', startDate);
    filters.append('endDate', endDate);
    filters.append('Tprix', Tprix);
    filters.append('mavant', mavant);
    filters.append('type', type);
    filters.append('ouvert', ouvert);
    
    try {
        // Effectuer la requête AJAX en envoyant tous les filtres
        const response = await fetch('ajax_filtres.php?' + filters.toString(), {
            method: 'GET',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        });

        // Vérifier la réponse
        if (response.ok) {
            // Récupérer le contenu et l'injecter dans le DOM
            const data = await response.text();
            document.querySelector('.offres-display').innerHTML = data;
            toggleMap();
            updateMap();
        } else {
            throw new Error('Erreur lors de la récupération des résultats.');
        }
    } catch (error) {
        console.error('Erreur AJAX:', error);
    }
}

 
// Function to toggle the visibility of the map
function toggleMap() {
    var mapElement = document.getElementById('map_offres');
    mapElement.style.display = 'block'; // Show the map
    if (!map) {
        // Initialize the map only once
        initializeMap();
    } else {
        map.invalidateSize(); // Recalculate map size if already initialized
    }
}



function initializeMap(){

    // Create the map and set the initial view
    map = L.map('map_offres', {
        center: [48.202047, -2.932644], // Position initiale
        zoom: 8, // Niveau de zoom initial
        minZoom: 3, // Niveau de zoom minimum
        maxZoom: 18, // Niveau de zoom maximum
        maxBounds: [
            [-90, -180], // Coin sud-ouest
            [90, 180] // Coin nord-est
        ],
        maxBoundsViscosity: 1.0 // Empêche de trop sortir des limites
    });

    // Ajouter un fond de carte personnalisé (par exemple, CartoDB)
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png').addTo(map);

    // Ajouter un marqueur à une position spécifique
    var marker = L.marker([48.202047, -2.932644]).addTo(map);  // Coordonnées du marqueur

    // Lier un popup avec les détails de l'offre
    marker.bindPopup(`
        <div class="content_popup">
            <img class="popup_image" src="img/uploaded/image18.png">
            <h3><a href="details_offre.php?idoffre=2">Titre gigalong pour test</a></h3>
            <div class="note_moy">
                <img class="popup_image" src="img/icons/star-solid.svg">
                <img class="popup_image" src="img/icons/star-solid.svg">
                <img class="popup_image" src="img/icons/star-solid.svg">
                <img class="popup_image" src="img/icons/star-solid.svg">
                <img class="popup_image" src="img/icons/star-regular.svg">
            </div>
            <p class="popup_description">Découvrez l'histoire Gauloise </p>
            <p>Paris</p>
            <p>Prix Min: 50e</p>
        </div>
    `);
}

// Création du groupe de clusters
var markers = L.markerClusterGroup();

window.addEventListener('resize', function () {
    map.invalidateSize(); // Force la mise à jour de la carte après un redimensionnement
});

async function updateMap() {
    map.setView([48.202047, -2.932644], 8);

    // Clear old markers
    markers.clearLayers();
    var div = document.getElementById("offres-data");

    // Retrieve JSON data
    var offres = JSON.parse(div.textContent);

    // Check if the 'offres' array is valid and not empty
    if (!Array.isArray(offres) || offres.length === 0) {
        console.log('Aucune offre disponible.');
        markers.clearLayers(); // Clear any markers, even if no new ones are added
        map.addLayer(markers); // Add the empty cluster layer to the map
        return;
    }

    // Function to add a delay between geocoding requests
    function delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    // Use async/await to handle asynchronous operations properly
    for (const offre of offres) {
        if (offre.titre) {
            const adresse = offre.adresse;

            // Create the content for the popup with stars
            let stars = '';
            const note = offre.note || 0; // Default to 0 if the note is invalid

            // Add full stars based on the rating
            for (let j = 0; j < Math.floor(note); j++) {
                stars += `<img class="popup_image" src="img/icons/star-solid.svg" alt="star">`;
            }

            // Add half or empty stars if the rating is not an integer
            if (note % 1 !== 0) {
                stars += `<img class="popup_image" src="img/icons/star-half.svg" alt="half-star">`;
            }

            // Add empty stars to complete up to 5
            for (let j = Math.ceil(note); j < 5; j++) {
                stars += `<img class="popup_image" src="img/icons/star-regular.svg" alt="empty-star">`;
            }

            // Geocode the address to get latitude and longitude
            async function geocode(adresse) {
                await delay(1000); // Wait for 1 second between requests
                try {
                    const response = await fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(adresse)}&format=json&limit=1`);
                    return response.json();
                } catch (error) {
                    console.error('Erreur de géocodage:', error);
                    return []; // Return an empty array in case of error
                }
            }

            const geocodeData = await geocode(adresse);
            if (geocodeData.length > 0) {
                const lat = geocodeData[0].lat;
                const lon = geocodeData[0].lon;

                // Create the marker after getting the coordinates
                const marker = L.marker([lat, lon]);

                // Bind the popup with offer details
                marker.bindPopup(`
                    <div class="content_popup">
                        <img class="popup_image" src="${offre.image || 'img/icons/image18.png'}">
                        <h3><a href="details_offre.php?idoffre=${offre.id}">${offre.titre}</a></h3>
                        <div class="note_moy">
                            <p>${stars || 'N/A'}</p>
                        </div>
                        <p class="popup_description">${offre.description}</p>
                        <p>${offre.ville || 'Adresse non disponible'}</p>
                        <p>Prix Min: ${offre.prix|| 'N/A'} €</p>
                    </div>
                `);

                // Add the marker to the markers layer
                markers.addLayer(marker);
            } else {
                console.log(`Adresse non trouvée pour l'offre: ${offre.titre}`);
            }
        }
    }

    // Add markers to the cluster layer and then to the map
    map.addLayer(markers);
}

// Ajouter des écouteurs d'événements pour chaque filtre
document.addEventListener('DOMContentLoaded', () => {
    initializeMap();

    document.getElementById('search-query').addEventListener('input', applyFilters);
    document.getElementById('category').addEventListener('change', applyFilters);
    document.getElementById('lieux').addEventListener('input', applyFilters);
    document.getElementById('price-range-min').addEventListener("input", applyFilters);
    document.getElementById('price-range-max').addEventListener("input", applyFilters);
    document.getElementById('notemin').addEventListener("change", applyFilters);
    document.getElementById('notemax').addEventListener("change", applyFilters);
    document.getElementById('datedeb').addEventListener('change', applyFilters);
    document.getElementById('datefin').addEventListener('change', applyFilters);
    document.getElementById('Tprix').addEventListener('change', applyFilters);
    document.getElementById('Mavant').addEventListener('change', applyFilters);
    document.getElementById('type').addEventListener('change', applyFilters);
    document.getElementById('ouvert').addEventListener('change', applyFilters);
    });



document.addEventListener('DOMContentLoaded', () => {
    
    // Ajout de l'event listener pour le lien "voir plus"
    const alauneLink = document.getElementById("Alaune");
    const mavantSelect = document.getElementById("Mavant");
    if (alauneLink && mavantSelect) {
        alauneLink.addEventListener("click", function() {
            // Définir le filtre à "À la Une"
            mavantSelect.value = "Alaune";
            // Appeler applyFilters() pour mettre à jour les résultats
            applyFilters();
            window.scrollTo(0, 0);

        });
    }
});

document.addEventListener('DOMContentLoaded', () => {
    
    // Ajout de l'event listener pour le lien "voir plus"
    const TprixLink = document.getElementById("Nouv");
    const TprixSelect = document.getElementById("Tprix");
    if (TprixLink && TprixSelect) {
        TprixLink.addEventListener("click", function() {
            // Définir le filtre à "À la Une"
            TprixSelect.value = "Recent";
            // Appeler applyFilters() pour mettre à jour les résultats
            applyFilters();
            window.scrollTo(0, 0);
        });
    }
});






function validImages(inputElements) {
    var validExtensions = ['image/jpeg', 'image/png', 'image/jpg']; // Formats acceptés

    for (var i = 0; i < inputElements.length; i++) {
        var file = inputElements[i].files[0];
        if (file && !validExtensions.includes(file.type)) {
            alert('Format d\'image non supporté dans le champ ' + inputElements[i].name + '. Veuillez choisir un fichier .png, .jpg, ou .jpeg.');
            inputElements[i].value = ''; // Réinitialiser l'input
            return false;
        }
    }
    return true;
}



// MODALE MENU AVIS
function openModalAvis(idavis) {
    document.getElementById("modalAvis").style.display = "block";
    document.getElementById("reportjsavisid").value = idavis;
}

document.querySelector("form").addEventListener("submit", function(event) {
    console.log("idavis value: ", document.querySelector('input[name="idavis"]').value);
});


// fermer la fenêtre
function closeModalAvis() {
    document.getElementById("modalAvis").style.display = "none";
}

// MODALE blacklist
function openModalBlacklist(idavis) {
    console.log("idavis value: ", idavis);
    document.getElementById("modalBlacklist").style.display = "block";
    document.getElementById("blacklistjsavisid").value = idavis;
}

// fermer la fenêtre
function closeModalBlacklist() {
    document.getElementById("modalBlacklist").style.display = "none";
}

function submitSignalementAvis(idAvis) {
    alert('Le signalement a bien été pris en compte.');
}

function openReplyForm(avisId) {
    var form = document.getElementById('replyForm-' + avisId);
    var arrow = document.getElementById('arrow-' + avisId);
    var replyB = document.getElementById('replyButton-' + avisId);

    if (form.style.display === 'none' || !form.style.display) {
        form.style.display = 'flex';
        form.style.transition = 'all 0.3s ease';
        form.style.opacity = '0';
        form.style.maxHeight = '0';
        setTimeout(() => {
            form.style.opacity = '1';
            form.style.maxHeight = '500px';
        }, 10);

        //Rotation de la flèche
        arrow.style.transform = 'rotate(-180deg)';
    } else {
        form.style.transition = 'all 0.3s ease';
        form.style.opacity = '0';
        form.style.maxHeight = '0';
        setTimeout(() => {
            form.style.display = 'none';
        }, 300);
        arrow.style.transform = 'rotate(0deg)';
    }
    replyB.style.margin = "10px 0px 5px 0px";
    arrow.style.transition = 'transform 0.3s ease';
}


document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.querySelector('.carousel');
    const container = document.querySelector('.carousel-container');
    let slides = document.querySelectorAll('.slide');
    const originalCount = slides.length; // nombre de slides initial
    let currentIndex = 0;
    const transitionTime = 500; // Durée de transition en ms
    let autoSlideInterval = null;
    let isDesktop = window.innerWidth >= 780;
    let isDragging = false, startX = 0, currentX = 0;
    
    // Mise à jour de la position du carousel
    function updateCarousel(animate = true) {
        const slideWidth = slides[0].offsetWidth;
        const gap = parseInt(window.getComputedStyle(carousel).gap) || 0;
        const step = slideWidth + gap;
        carousel.style.transition = animate ? 'transform 0.5s ease' : 'none';
        carousel.style.transform = 'translateX(' + (-step * currentIndex) + 'px)';
    }
    
    // Gestion du mode desktop (auto-défilement)
    function setupDesktop() {
        // Dupliquer l'ensemble des slides pour un effet continu (si ce n'est pas déjà fait)
        if (!carousel.dataset.duplicated) {
            carousel.innerHTML += carousel.innerHTML;
            slides = document.querySelectorAll('.slide');
            carousel.dataset.duplicated = 'true';
        }
        // Désactiver les écouteurs tactiles
        container.removeEventListener('touchstart', touchStartHandler);
        container.removeEventListener('touchmove', touchMoveHandler);
        container.removeEventListener('touchend', touchEndHandler);
        // Lancer l'auto-défilement
        if(autoSlideInterval) clearInterval(autoSlideInterval);
        currentIndex = 0;
        updateCarousel(false);
        autoSlideInterval = setInterval(function() {
            currentIndex++;
            updateCarousel();
            // Réinitialiser dès la fin des slides originales
            if (currentIndex >= originalCount) {
                setTimeout(function() {
                    currentIndex = 0;
                    updateCarousel(false);
                }, transitionTime);
            }
        }, 2500);
    }
    
    // Gestion des événements tactiles pour le mode mobile
    function touchStartHandler(e) {
        isDragging = true;
        startX = e.touches[0].clientX;
    }
    function touchMoveHandler(e) {
        if (!isDragging) return;
        currentX = e.touches[0].clientX;
        const deltaX = currentX - startX;
        const slideWidth = slides[0].offsetWidth;
        const gap = parseInt(window.getComputedStyle(carousel).gap) || 0;
        const step = slideWidth + gap;
        carousel.style.transition = 'none';
        carousel.style.transform = 'translateX(' + (-step * currentIndex + deltaX) + 'px)';
    }
    function touchEndHandler() {
        isDragging = false;
        const deltaX = currentX - startX;
        // Seuil de 50px pour passer à la slide suivante ou précédente
        if (deltaX < -50 && currentIndex < originalCount - 1) {
            currentIndex++;
        } else if (deltaX > 50 && currentIndex > 0) {
            currentIndex--;
        }
        updateCarousel();
    }
    
    function setupMobile() {
        // Annuler auto-défilement s'il existe
        if(autoSlideInterval) {
            clearInterval(autoSlideInterval);
            autoSlideInterval = null;
        }
        currentIndex = 0;
        updateCarousel(false);
        // Ajouter les écouteurs tactiles
        container.addEventListener('touchstart', touchStartHandler);
        container.addEventListener('touchmove', touchMoveHandler);
        container.addEventListener('touchend', touchEndHandler);
    }
    
    // Initialisation en fonction de la largeur actuelle
    function initCarousel() {
        if (window.innerWidth >= 780) {
            isDesktop = true;
            setupDesktop();
        } else {
            isDesktop = false;
            setupMobile();
        }
    }
    
    initCarousel();
    
    // Réinitialisation dynamique lors du redimensionnement sans recharger la page
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            let newDesktop = window.innerWidth >= 780;
            if(newDesktop !== isDesktop) {
                // Reset l'index et réinitialise le carrousel dans le nouveau mode
                currentIndex = 0;
                initCarousel();
            } else {
                updateCarousel(false);
            }
        }, 250);
    });
});

$(document).ready(function(){
    $('.vertical-carousel').slick({
         vertical: true,
         centerMode: true,      // Active le centre
         centerPadding: '0px',
         arrows: false,
         autoplay: true,
         autoplaySpeed: 3000,
         slidesToShow: 1,
         slidesToScroll: 1,
         pauseOnHover: false
    });
});

function addToRecentlyViewed(offerId) {
    const cookieName = "recently_viewed";
    const maxItems = 10; // Limite le nombre d'offres stockées

    let recentlyViewed = getRecentlyViewed();

    // Supprimer l'offre si elle est déjà présente
    recentlyViewed = recentlyViewed.filter(id => id !== offerId);

    // Ajouter l'offre en tête de liste
    recentlyViewed.unshift(offerId);

    // Limiter la taille de la liste (FIFO)
    if (recentlyViewed.length > maxItems) {
        recentlyViewed.pop(); // Supprime le plus ancien (dernier élément)
    }

    // Stocker dans le cookie (valide pour 30 jours)
    document.cookie = `${cookieName}=${JSON.stringify(recentlyViewed)}; path=/; max-age=${30 * 24 * 60 * 60}`;
}

// Fonction pour récupérer les offres stockées
function getRecentlyViewed() {
    const cookieName = "recently_viewed=";
    const cookies = document.cookie.split("; ");

    for (let cookie of cookies) {
        if (cookie.startsWith(cookieName)) {
            return JSON.parse(cookie.substring(cookieName.length));
        }
    }

    return [];
}

function resetRecentlyViewed() {
    document.cookie = "recently_viewed=[]; path=/; max-age=0"; // Supprime le cookie
    console.log("Liste des offres consultées réinitialisée !");
}

// Récupérer les offres récemment consultées
let recentlyViewedOffers = getRecentlyViewed(); // Exemple : [1, 2, 3, 4, 5]
if (recentlyViewedOffers.length > 0) {
    // Envoi des IDs au backend PHP
    fetch('get_offres_details.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ offerIds: recentlyViewedOffers }),
    })
    .then(response => response.json())
    .then(data => {
        // Manipuler les données (les offres récupérées)
        displayOffers(data);
    })
    .catch(error => {
        console.error('Erreur lors du chargement des offres:', error);
    });
}

// Fonction pour afficher les offres dans l'interface utilisateur
function displayOffers(offers) {
    const offresContainer = document.getElementById('offresContainer');
    offresContainer.innerHTML = ''; // Vider le conteneur avant de remplir

    offers.forEach(offre => {
        const offerElement = document.createElement('div');
        offerElement.classList.add('offre-item');

        offerElement.innerHTML = `
            <a onclick="addToRecentlyViewed(${offre.idoffre})" style="text-decoration:none;" href="details_offre.php?idoffre=${offre.idoffre}">
                <div class="offre-card" ${offre.enreliefoffre ? "style='border: 3px solid #36D673;'" : ""}>
                    <div class="offre-image-container" style="position: relative;">
                        <!-- Affichage de l'image -->
                        <img class="offre-image" src="${offre.pathimage ? offre.pathimage : 'img/default.jpg'}" alt="Image de l'offre">
                    </div>
                    <div class="offre-details">
                        <!-- Titre de l'offre -->
                        <h2 class="offre-titre-index">${offre.titreoffre ? offre.titreoffre : 'Titre non disponible'}</h2>
                        
                        <!-- Résumé de l'offre -->
                        <p class="offre-resume"><strong>Résumé:</strong> ${offre.resumeoffre ? offre.resumeoffre : 'Résumé non disponible'}</p>
                        
                        <!-- Prix minimum de l'offre -->
                        <p class="offre-prix"><strong>Prix Minimum:</strong> ${(!offre.prixminoffre || offre.prixminoffre <= 0) ? 'Gratuit' : offre.prixminoffre + ' €'}</p>

                        <!-- Notes -->
                        <div class="titre-moy-index">
                            <p class="offre-resume"><strong>Note :</strong></p>
                            <div class="texte_note_etoiles_container">
                                ${generateStars(offre.notemoyenneoffre)}
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        `;
        
        offresContainer.appendChild(offerElement);
    });


}


function generateStars(rating) {
    if (!rating) return '<p>Pas d\'évaluations</p>';
    let starsHtml = '';
    let noteMoyenne = parseFloat(rating);
    let fullStars = Math.floor(noteMoyenne);

    // Arrondir selon le seuil défini
    if (noteMoyenne - fullStars > 0.705) {
        fullStars++;
    }

    // Ajouter les étoiles pleines
    for (let i = 0; i < fullStars; i++) {
        starsHtml += `<img src="img/icons/star-solid.svg" alt="star checked" width="20" height="20">`;
    }

    // Si la partie décimale est comprise entre 0.295 et 0.705, ajouter une demi-étoile
    if (noteMoyenne - fullStars >= 0.295 && noteMoyenne - fullStars <= 0.705) {
        starsHtml += `<img src="img/icons/star-half.svg" alt="half star checked" width="20" height="20">`;
        fullStars++;
    }

    // Compléter jusqu'à 5 étoiles
    for (let i = fullStars; i < 5; i++) {
        starsHtml += `<img src="img/icons/star-regular.svg" alt="star unchecked" width="20" height="20">`;
    }
    
    starsHtml += `<p class="nombre_note">${rating}/5</p>`;
    return starsHtml;
}








