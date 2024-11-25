<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer mon compte</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="body-creer-pro">
    <main class="form-container">
        <div class="back-arrow">
        <a style="text-decoration: none; font-size: 30px; color: #040316; cursor: pointer;" onclick="history.back();">&#8617;</a>
        </div>
        <h1 class="subtitle">Créer mon compte Professionnel </h1>
        <section class="form-section">
                    <!-- Étape 1 -->
            <h2>1. Apprenons à nous connaître</h2>
            <form class="form-creer-pro">
                <div class="input-row">
                    <div class="input-group">
                        <label for="nom">Nom</label>
                        <div class="input-container">
                            <input type="text" id="nom" name="nom" placeholder="Jean" required>
                            <span class="required">*</span>
                        </div>
                    </div>
                    <div class="input-group">
                        <label for="prenom">Prénom</label>
                        <div class="input-container">
                            <input type="text" id="prenom" name="prenom" placeholder="Duchamp" required>
                            <span class="required">*</span>
                        </div>
                    </div>
                </div>
                <div class="input-group">
                    <label for="email">E-mail</label>
                    <div class="input-container">
                        <input type="email" id="email" name="email" placeholder="jeanDuchamp@exemple.fr" required>
                        <span class="required">*</span>
                    </div>
                </div>
                <div class="input-group">
                    <label for="adresse">Adresse Postale</label>
                    <div class="input-container">
                        <input type="text" id="adresse" name="adresse" placeholder="4 rue des affaires" required>
                        <span class="required">*</span>
                    </div>
                </div>
                <div class="input-row">
                    <div class="input-group">
                        <label for="ville">Ville</label>
                        <div class="input-container">
                            <input type="text" id="ville" name="ville" placeholder="Villeperdue" required>
                            <span class="required">*</span>
                        </div>
                    </div>
                    <div class="input-group">
                        <label for="tel">Tel</label>
                        <div class="input-container">
                            <input type="tel" id="tel" name="tel" placeholder="0685236954" required>
                            <span class="required">*</span>
                        </div>
                    </div>
                </div>
                <button type="submit" class="submit-btn">SUIVANT</button>
                <p class="almost-done">Vous y êtes presque</p>
            </form>
        </section>

        <!-- Étape 2 -->
        <section id="form-step-2" class="form-step hidden">
            <h1>Créer mon compte</h1>
            <p class="subtitle">Professionnel</p>
            <h2>2. Et votre entreprise ?</h2>
            <form class="form-creer-pro">
                <div class="input-group">
                    <label for="siren">Numéro de SIREN</label>
                    <div class="input-container">
                        <input type="text" id="siren" name="siren" placeholder="120027016" required>
                    </div>
                </div>
                <div class="input-group">
                    <label for="raison-sociale">Raison sociale</label>
                    <div class="input-container">
                        <input type="text" id="raison-sociale" name="raison-sociale" placeholder="Mon entreprise" required>
                    </div>
                </div>
                <div class="input-group">
                    <label for="iban">IBAN</label>
                    <div class="input-container">
                        <input type="text" id="iban" name="iban" placeholder="FR7610107001011234567890129" required>
                    </div>
                </div>
                <button type="button" class="submit-btn" onclick="goToStep(1)">RETOUR</button>
                <button type="submit" class="submit-btn">VALIDER</button>
            </form>
        </section>
    </main>
    <script>
    function goToStep(step) {
        const steps = document.querySelectorAll('.form-step');
        steps.forEach((formStep, index) => {
            if (index === step - 1) {
                formStep.classList.remove('hidden');
            } else {
                formStep.classList.add('hidden');
            }
        });
    }

    // Gérer la soumission finale du formulaire (si vous voulez envoyer des données ou afficher un message)
    function finalSubmit() {
        // Vous pouvez récupérer les données du formulaire ici si vous le souhaitez
        const form1 = document.getElementById('form-1');
        const form2 = document.getElementById('form-2');

        const formData1 = new FormData(form1);
        const formData2 = new FormData(form2);

        // Exemple de console log avec les données (pour le débogage)
        console.log('Données du formulaire 1 :', Object.fromEntries(formData1.entries()));
        console.log('Données du formulaire 2 :', Object.fromEntries(formData2.entries()));

        // Vous pouvez envoyer ces données à un serveur ou effectuer d'autres actions ici
        alert('Formulaire soumis avec succès!');
    }
    </script>
</body>
</html>
