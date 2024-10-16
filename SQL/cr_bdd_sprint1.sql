// mysql 8
CREATE DATABASE IF NOT EXISTS cr_bdd_sprint1;

USE cr_bdd_sprint1;

CREATE TABLE IF NOT EXISTS '_offre' (
    'idOffre' SERIAL PRIMARY KEY,
    'idProPropose' SERIAL NOT NULL,
    'idAdresse' SERIAL NOT NULL,
    'titreOffre' VARCHAR(255) NOT NULL,
    'resumeOffre' TEXT NOT NULL,
    'descriptionOffre' TEXT NOT NULL,
    'listeIdImage' TEXT NOT NULL,
    'prixMinOffre' FLOAT NOT NULL,
    'aLaUneOffre' BOOLEAN NOT NULL,
    'enReliefOffre' BOOLEAN NOT NULL,
    'typeOffre' INT NOT NULL,
    'noteMoyenneOffre' FLOAT NOT NULL,
    'commenaiteBlacklistable' BOOLEAN NOT NULL,
    'dateCreationOffre' DATE NOT NULL,
    'conditionAccessibilite' TEXT NOT NULL,
    'horsLigne' BOOLEAN NOT NULL
)

// offreActivite, offreSpectacle, offreParcAttraction, offreVisite, offreRestaurant héritent de offre
CREATE TABLE IF NOT EXISTS '_offreActivite' (
    'idOffreActivite' SERIAL PRIMARY KEY,
    'idOffre' SERIAL,
    'indicationDuree' DATE NOT NULL,
    'ageRequis' INT NOT NULL,
    'prestationIncluse' TEXT NOT NULL,
    'idTage' INT NOT NULL
)

CREATE TABLE IF NOT EXISTS '_offreSpectacle' (
    'idOffreSpectacle' SERIAL PRIMARY KEY,
    'idOffre' SERIAL,
    'dateOffre' DATE NOT NULL,
    'indicationDuree' DATE NOT NULL,
    'capaciteAcceuil' INT NOT NULL,
    'idTage' INT NOT NULL
)

CREATE TABLE IF NOT EXISTS '_offreParcAttraction' (
    'idOffreParcAttraction' SERIAL PRIMARY KEY,
    'idOffre' SERIAL,
    'dateOuverture' DATE NOT NULL,
    'dateFermeture' DATE NOT NULL,
    'carteParc' INT NOT NULL,
    'nbrAttraction' INT NOT NULL,
    'ageMinimun' INT NOT NULL,
    'idTage' INT NOT NULL
)

CREATE TABLE IF NOT EXISTS '_offreVisite' (
    'idOffreVisite' SERIAL PRIMARY KEY,
    'idOffre' SERIAL,
    'dateOffre' DATE NOT NULL,
    'visiteGuidee' BOOLEAN NOT NULL,
    'langueProposees' BOOLEAN NOT NULL,
    'idTage' INT NOT NULL
)

CREATE TABLE IF NOT EXISTS '_offreRestaurant' (
    'idOffreRestaurant' SERIAL PRIMARY KEY,
    'idOffre' SERIAL,
    'horaireSemaine' TEXT NOT NULL,
    'gammePrix' INT NOT NULL,
    'carteResto' INT NOT NULL,
    'idTage' INT NOT NULL
)

// Modification de tag car ceci est plus simple
CREATE TABLE IF NOT EXISTS '_tag' (
    'idTag' SERIAL PRIMARY KEY,
    'typeTag' TEXT NOT NULL,
    'typeRestauration' BOOLEAN NOT NULL
)

CREATE TABLE IF NOT EXISTS '_adresse' (
    'idAdresse' SERIAL PRIMARY KEY,
    'numRue' INT NOT NULL,
    'supplementAdresse' TEXT NOT NULL,
    'adresse' TEXT NOT NULL,
    'codePostal' INT NOT NULL,
    'ville' TEXT NOT NULL,
    'departement' TEXT NOT NULL,
    'pays' TEXT NOT NULL
)

CREATE TABLE IF NOT EXISTS '_image' (
    'idImage' SERIAL PRIMARY KEY,
    'pathImage' TEXT NOT NULL,
    'idOffre' SERIAL,
    'idAvis' SERIAL
)

CREATE TABLE IF NOT EXISTS '_signamelement' (
    'idSignamelement' SERIAL PRIMARY KEY,
    'idOffre' SERIAL,
    'idAvis' SERIAL
)

CREATE TABLE IF NOT EXISTS '_avis' (
    'idAvis' SERIAL PRIMARY KEY,
    'idOffre' SERIAL,
    'noteAvis' INT NOT NULL,
    'commentaireAvis' TEXT NOT NULL,
    'idMembre' SERIAL NOT NULL,
    'dateAvis' DATE NOT NULL,
    'dateVisiteAvis' DATE NOT NULL,
    'blacklistAvis' BOOLEAN NOT NULL,
    'reponsePro' BOOLEAN NOT NULL
)

CREATE TABLE IF NOT EXISTS '_reponseAvis' (
    'idReponseAvis' SERIAL PRIMARY KEY,
    'idAvis' SERIAL,
    'idPro' SERIAL,
    'texteReponse' TEXT NOT NULL,
    'dateReponse' DATE NOT NULL
)

CREATE TABLE IF NOT EXISTS '_compte' (
    'idCompte' SERIAL PRIMARY KEY,
    'nomCompte' TEXT NOT NULL,
    'prenomCompte' TEXT NOT NULL,
    'mailCompte' TEXT NOT NULL,
    'numTelCompte' TEXT NOT NULL,
    'idImagePdp' SERIAL,
    'hashMdpCompte' TEXT NOT NULL,
    'dateCreationCompte' DATE NOT NULL,
    'dateDerniereConnexionCompte' DATE NOT NULL
)

// professionnel et membre hérédite de compte
CREATE TABLE IF NOT EXISTS '_professionnel' (
    'idPro' SERIAL PRIMARY KEY,
    'idCompte' SERIAL,
    'denominationPro' TEXT NOT NULL,
    'numSirenPro' TEXT NOT NULL
)

CREATE TABLE IF NOT EXISTS '_membre' (
    'idMembre' SERIAL PRIMARY KEY,
    'idCompte' SERIAL,
    'dateNaissanceMembre' DATE NOT NULL
)

// professionnelPrive et professionnelPublic héritent de professionnel
CREATE TABLE IF NOT EXISTS '_professionnelPublic' (
    'idProPublic' SERIAL PRIMARY KEY,
    'idPro' SERIAL,
    'listeIdOffresMiseEnAvant' TEXT NOT NULL,
    'listeIdOffresHorsLigne' TEXT NOT NULL
)

CREATE TABLE IF NOT EXISTS '_professionnelPrive' (
    'idProPrive' SERIAL PRIMARY KEY,
    'idPro' SERIAL,
    'listeIdOffresPremium' TEXT NOT NULL,
    'listeIdOffresStandard' TEXT NOT NULL,
    'listeIdOffresHorsLigne' TEXT NOT NULL,
    'coordBancairesIBAN' TEXT NOT NULL,
    'coordBancairesBIC' TEXT NOT NULL
)

CREATE TABLE IF NOT EXISTS '_facture' (
    'idFacture' SERIAL PRIMARY KEY,
    'idProPrive' SERIAL,
    'dateFacture' DATE NOT NULL,
    'listeOffresSTD' TEXT NOT NULL,
    'listeOffresPREM' TEXT NOT NULL,
    'montantHT' FLOAT NOT NULL,
    'montantTTC' FLOAT NOT NULL
)

CREATE TABLE IF NOT EXISTS 'constPrix' (
    'idConstPrix' SERIAL PRIMARY KEY,
    'prixSTD' FLOAT NOT NULL,
    'prixPREM' FLOAT NOT NULL,
    'prixALaUne' FLOAT NOT NULL,
    'prixEnRelief' FLOAT NOT NULL
)

// Contrainte
