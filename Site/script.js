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

// FILTRES

async function applyFilters() {
    const search = document.getElementById('search-query').value;
    const category = document.getElementById('category').value;
    const lieux = document.getElementById('lieux').value;
    const minPrice = document.getElementById("price-range-min").value;
    const maxPrice = document.getElementById("price-range-max").value;
    const notemin = document.getElementById('notemin').value;
    const notemax = document.getElementById('notemax').value;
    const Tprix = document.getElementById('Tprix').value;
    const Tnote = document.getElementById('Tnote').value;
    const aLaUne = document.getElementById('aLaUne').value;


    const filters = new URLSearchParams({
        search,
        category,
        lieux,
        minPrice,
        maxPrice,
        notemin,
        notemax,
        Tprix,
        Tnote,
        aLaUne,
    });

    try {
        const response = await fetch('ajax_filtres.php?' + filters.toString(), {
            method: 'GET',
        });

        if (response.ok) {
            const result = await response.json();
            if (result.status === 'success') {
                const offers = result.data;
                const offersContainer = document.querySelector('.offres-display');
                let offersHTML = '';

                offers.forEach(offer => {
                    offersHTML += `
                        <div class="offre-card">
                            <img src="${offer.pathimage}" alt="${offer.titreoffre}" class="offre-image">
                            <div class="offre-details">
                                <h3>${offer.titreoffre}</h3>
                                <p>${offer.resumeoffre}</p>
                                <p>Prix: ${offer.prixminoffre} €</p>
                                <p>Note: ${offer.noteMoyenneOffre !== null ? offer.noteMoyenneOffre : 'N/A'}/5</p>
                            </div>
                        </div>`;
                });

                offersContainer.innerHTML = offersHTML;
            } else {
                throw new Error(result.message);
            }
        } else {
            throw new Error(`Erreur lors de la récupération des résultats: ${response.statusText}`);
        }
    } catch (error) {
        console.error('Erreur AJAX:', error);
        document.querySelector('.offres-display').innerHTML = `<p style="color: red;">Erreur: ${error.message}</p>`;
    }
}

// Ajouter des écouteurs d'événements pour chaque filtre
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('search-query').addEventListener('input', applyFilters);
    document.getElementById('Tprix').addEventListener('change', applyFilters);
    document.getElementById('Tnote').addEventListener('change', applyFilters);
    document.getElementById('category').addEventListener('change', applyFilters);
    document.getElementById('lieux').addEventListener('input', applyFilters);
    document.getElementById('price-range-min').addEventListener("input", applyFilters);
    document.getElementById('price-range-max').addEventListener("input", applyFilters);
    document.getElementById('notemin').addEventListener("change", applyFilters);
    document.getElementById('notemax').addEventListener("change", applyFilters);
    document.getElementById('aLaUne').addEventListener('change', applyFilters);
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
