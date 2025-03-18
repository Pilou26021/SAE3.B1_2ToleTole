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
    // Lorsque l'utilisateur clique sur le bouton de filtre, affichez le pop-up
    $("#filterBtn").click(function() {
        $("#filterForm").fadeIn();
    });

    // Lorsque l'utilisateur clique en dehors du pop-up des filtres, le fermer
    $(document).click(function(event) {
        // Si le clic a eu lieu en dehors de #filterForm et #filterBtn
        if (!$(event.target).closest('#filterForm').length && !$(event.target).closest('#filterBtn').length) {
            $("#filterForm").fadeOut();  // Fermer le filtre
        }
    });

    // Empêcher le clic sur le filtre de fermer immédiatement le pop-up
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
            document.getElementById('map_offres').style.display = 'block';
        } else {
            throw new Error('Erreur lors de la récupération des résultats.');
        }
    } catch (error) {
        console.error('Erreur AJAX:', error);
    }
}

// Ajouter des écouteurs d'événements pour chaque filtre
document.addEventListener('DOMContentLoaded', () => {
    var locations = [
        { lat: 48.8566, lng: 2.3522, name: "Paris" },
        { lat: 45.7640, lng: 4.8357, name: "Lyon" },
        { lat: 43.6047, lng: 1.4442, name: "Toulouse" },
        { lat: 43.2965, lng: 5.3698, name: "Marseille" },
        { lat: 50.6292, lng: 3.0573, name: "Lille" },
        { lat: 47.2184, lng: -1.5536, name: "Nantes" },
        { lat: 48.5734, lng: 7.7521, name: "Strasbourg" }
    ];

        // Create the map and set the initial view
    var map = L.map('map_offres', {
        center: [locations[0].lat, locations[0].lng], // Starting center point
        zoom: 13, // Starting zoom level
    });

    // Add tile layer (OpenStreetMap)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    document.querySelectorAll('select, input').forEach(element => {
        element.addEventListener('change', () => {
            setTimeout(() => {
                map.invalidateSize();
            }, 300);
        });
    });

    // Define the custom icon
    var customIcon = L.icon({
        iconUrl: 'img/icons/poi/attraction.png',  
        iconSize: [64, 64], 
        iconAnchor: [16, 32], 
        popupAnchor: [0, -32] 
    });

    // Création du groupe de clusters
    var markers = L.markerClusterGroup();

    

    // Add markers with the custom icon
    locations.forEach(location => {
        var content_popup = `
            <div class="content_popup">
                <img class="popup_image" src="icones/image18.png">
                <div class="titre_note">
                    <h3><a href="">Titre</a></h3>
                    <div class="note_moy">
                        <p>4</p>
                        <img class="popup_image" src="icones/star-solid.svg">
                    </div>
                </div>
                <div>créée le jj/mm/yyyy</div>
                <p>Catégorie</p>
                <p>Adresse</p>
                <p>Prix Min €</p>
            </div>
        `;
        
        var marker = L.marker([location.lat, location.lng], { icon: customIcon })
            .bindPopup(content_popup);

        marker.on('click', function () {
            marker.openPopup();
        });

        markers.addLayer(marker);
    });

    // Ajout du cluster à la carte
    map.addLayer(markers);


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

document.addEventListener('DOMContentLoaded', function () {
    const carousels = document.querySelectorAll('.carousel-container');

    function updateCarouselBehavior() {
        const isSmallScreen = window.innerWidth < 700;

        carousels.forEach(carousel => {
            const track = carousel.querySelector('.carousel-track');
            const slides = track.querySelectorAll('.carousel-slide li');
            const prevButton = carousel.querySelector('.prev-btn');
            const nextButton = carousel.querySelector('.next-btn');
            const slideWidth = slides[0].getBoundingClientRect().width;

            // Remove cloned slides if screen width is below 700px
            if (isSmallScreen) {
                const clones = track.querySelectorAll('.carousel-slide li.clone');
                clones.forEach(clone => clone.remove());
                track.style.transform = 'translateX(0)';
                track.style.transition = 'none';
            } else {
                // Clone slides to make the carousel infinite
                slides.forEach(slide => {
                    if (!slide.classList.contains('clone')) {
                        const clone = slide.cloneNode(true);
                        clone.classList.add('clone');
                        track.appendChild(clone);
                    }
                });
            }

            let currentIndex = 0;

            function moveToSlide(index) {
                track.style.transform = `translateX(-${index * slideWidth}px)`;
                currentIndex = index;
            }

            nextButton.addEventListener('click', () => {
                if (isSmallScreen) {
                    if (currentIndex < slides.length - 1) {
                        moveToSlide(currentIndex + 1);
                    }
                } else {
                    if (currentIndex >= slides.length) {
                        track.style.transition = 'none';
                        moveToSlide(0);
                        setTimeout(() => {
                            track.style.transition = 'transform 0.5s ease-in-out';
                            moveToSlide(currentIndex + 1);
                        }, 20);
                    } else {
                        moveToSlide(currentIndex + 1);
                    }
                }
            });

            prevButton.addEventListener('click', () => {
                if (isSmallScreen) {
                    if (currentIndex > 0) {
                        moveToSlide(currentIndex - 1);
                    }
                } else {
                    if (currentIndex <= 0) {
                        track.style.transition = 'none';
                        moveToSlide(slides.length);
                        setTimeout(() => {
                            track.style.transition = 'transform 0.5s ease-in-out';
                            moveToSlide(currentIndex - 1);
                        }, 20);
                    } else {
                        moveToSlide(currentIndex - 1);
                    }
                }
            });
        });
    }

    // Call the function on load and on resize
    updateCarouselBehavior();
    window.addEventListener('resize', updateCarouselBehavior);
});


// MODALE MENU AVIS
function openModalAvis() {
    document.getElementById("modalAvis").style.display = "block";
}

// fermer la fenêtre
function closeModalAvis() {
    document.getElementById("modalAvis").style.display = "none";
}

// MODALE MENU AVIS
function openModalBlacklist() {
    document.getElementById("modalBlacklist").style.display = "block";
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

