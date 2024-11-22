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
        <h1>Créer mon compte</h1>
        <p class="subtitle">Professionnel</p>
        <section class="form-section">
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
    </main>
</body>
</html>
