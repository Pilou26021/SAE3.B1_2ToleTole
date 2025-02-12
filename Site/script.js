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
        } else {
            throw new Error('Erreur lors de la récupération des résultats.');
        }
    } catch (error) {
        console.error('Erreur AJAX:', error);
    }
}

// Ajouter des écouteurs d'événements pour chaque filtre
document.addEventListener('DOMContentLoaded', () => {
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

    document.getElementById('Alaune').addEventListener('click', function () {
        // Définir une valeur par défaut pour le champ Mavant
        const mavantSelect = document.getElementById('Mavant');
        mavantSelect.value = 'Alaune'; // Définir "true" ou une valeur par défaut

        // Appeler la fonction applyFilters pour appliquer les filtres
        applyFilters();
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

document.addEventListener('DOMContentLoaded', () => {
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');
    const track = document.querySelector('.carousel-track');
    const slides = document.querySelectorAll('.offer-alaune');
    const slideWidth = 371; // Largeur de chaque carte
    let currentIndex = 0;
    let cardsPerView = calculateCardsPerView()-1;

    // Fonction pour calculer le nombre de cartes visibles
    function calculateCardsPerView() {
        const containerWidth = document.querySelector('.carousel-container').offsetWidth;
        return Math.floor(containerWidth / slideWidth);
    }

    // Fonction pour mettre à jour la visibilité des boutons
    function updateButtonVisibility() {
        const maxIndex = Math.max(0, Math.ceil((slides.length - cardsPerView) / 1));
        if (currentIndex === 0) {
            prevBtn.classList.add('hidden');
        } else {
            prevBtn.classList.remove('hidden');
        }

        if (currentIndex === maxIndex) {
            nextBtn.classList.add('hidden');
        } else {
            nextBtn.classList.remove('hidden');
        }
    }

    // Fonction pour gérer le changement de la taille de la fenêtre
    function onResize() {
        cardsPerView = calculateCardsPerView();
        const maxIndex = Math.max(0, Math.ceil((slides.length - cardsPerView) / 1));
        if (currentIndex > maxIndex) {
            currentIndex = maxIndex; // Réinitialiser l'index si nécessaire
        }
        track.style.transform = `translateX(-${currentIndex * slideWidth}px)`;
        updateButtonVisibility();
    }

    // Initialiser la visibilité des boutons et écouter le redimensionnement
    updateButtonVisibility();
    window.addEventListener('resize', onResize);

    // Événements de clic pour faire défiler le slider
    nextBtn.addEventListener('click', () => {
        const maxIndex = Math.max(0, Math.ceil((slides.length - cardsPerView) / 1));
        if (currentIndex < maxIndex) {
            currentIndex++;
            track.style.transform = `translateX(-${currentIndex * slideWidth}px)`;
            updateButtonVisibility();
        }
    });

    prevBtn.addEventListener('click', () => {
        if (currentIndex > 0) {
            currentIndex--;
            track.style.transform = `translateX(-${currentIndex * slideWidth}px)`;
            updateButtonVisibility();
        }
    });
});

// MODALE MENU AVIS
function openModalAvis() {
    document.getElementById("modalAvis").style.display = "block";
}

// fermer la fenêtre
function closeModalAvis() {
    document.getElementById("modalAvis").style.display = "none";
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