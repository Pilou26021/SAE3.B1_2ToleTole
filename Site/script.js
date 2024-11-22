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

document.getElementById("filterBtn").addEventListener("click", function() {
    var filterForm = document.getElementById("filterForm");
    if (filterForm.style.display === "none" || filterForm.style.display === "") {
        filterForm.style.display = "block";
    } else {
        filterForm.style.display = "none";
    }
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

async function applyFilters() {
    // Récupérer les valeurs des filtres
    const category = document.getElementById('category').value;
    const lieux = document.getElementById('lieux').value;


    // Préparer les données pour l'envoi
    const filters = new URLSearchParams();
    filters.append('category', category);
    filters.append('lieux', lieux);

    try {
        // Utiliser fetch pour envoyer les filtres et récupérer les résultats
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
    document.getElementById('category').addEventListener('change', applyFilters);
    document.getElementById('lieux').addEventListener('input', applyFilters);
});

// Fonction pour gérer les requêtes AJAX
async function updateOffers() {
    const minPrice = document.getElementById("price-range-min").value;
    const maxPrice = document.getElementById("price-range-max").value;

    // Mettre à jour les valeurs affichées
    document.getElementById("price-value-min").textContent = minPrice;
    document.getElementById("price-value-max").textContent = maxPrice;

    // URL pour la requête AJAX
    const url = document.getElementById("price-range-min").getAttribute("data-url");

    // Préparer les paramètres
    const params = new URLSearchParams();
    params.append("minPrice", minPrice);
    params.append("maxPrice", maxPrice);

    try {
        // Effectuer une requête AJAX
        const response = await fetch(`${url}?${params.toString()}`, {
            method: "GET",
        });

        // Vérifier la réponse
        if (response.ok) {
            const data = await response.text();
            // Injecter les résultats dans la section des offres
            document.querySelector(".offres-display").innerHTML = data;
        } else {
            throw new Error("Erreur lors du chargement des offres.");
        }
    } catch (error) {
        console.error("Erreur AJAX :", error);
    }
}

// Ajout des événements sur les sliders
document.getElementById("price-range-min").addEventListener("input", updateOffers);
document.getElementById("price-range-max").addEventListener("input", updateOffers);


const noteMinSelect = document.getElementById("notemin");
const noteMaxSelect = document.getElementById("notemax");

async function filterByNotes() {
    const minNote = parseInt(noteMinSelect.value);
    const maxNote = parseInt(noteMaxSelect.value);

    // Vérifier que minNote est inférieur ou égal à maxNote

    const url = noteMinSelect.getAttribute("data-url");
    const params = new URLSearchParams();
    params.append("notemin", minNote);
    params.append("notemax", maxNote);

    try {
        const response = await fetch(`${url}?${params.toString()}`, { method: "GET" });

        if (response.ok) {
            const data = await response.text();
            document.querySelector(".offres-display").innerHTML = data;
        } else {
            throw new Error("Erreur lors du filtrage des offres.");
        }
    } catch (error) {
        console.error("Erreur AJAX :", error);
    }
}

// Ajouter des écouteurs d'événements
noteMinSelect.addEventListener("change", filterByNotes);
noteMaxSelect.addEventListener("change", filterByNotes);


  

