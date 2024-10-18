DROP DATABASE IF EXISTS cr_bdd_sprint1;
CREATE DATABASE IF NOT EXISTS cr_bdd_sprint1;

USE cr_bdd_sprint1;

-- 1. Créer les tables indépendantes
CREATE TABLE IF NOT EXISTS `_adresse` (
    `idAdresse` SERIAL PRIMARY KEY,
    `numRue` INT NOT NULL,
    `supplementAdresse` TEXT NOT NULL,
    `adresse` TEXT NOT NULL,
    `codePostal` INT NOT NULL,
    `ville` TEXT NOT NULL,
    `departement` TEXT NOT NULL,
    `pays` TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS `_tag` (
    `idTag` SERIAL PRIMARY KEY,
    `typeTag` TEXT NOT NULL,
    `typeRestauration` BOOLEAN NOT NULL
);

CREATE TABLE IF NOT EXISTS `_compte` (
    `idCompte` SERIAL PRIMARY KEY,
    `nomCompte` TEXT NOT NULL,
    `prenomCompte` TEXT NOT NULL,
    `mailCompte` TEXT NOT NULL,
    `numTelCompte` TEXT NOT NULL,
    `idImagePdp` BIGINT,
    `hashMdpCompte` TEXT NOT NULL,
    `dateCreationCompte` DATE NOT NULL,
    `dateDerniereConnexionCompte` DATE NOT NULL
);

-- 2. Créer les tables liées à `_compte`
CREATE TABLE IF NOT EXISTS `_professionnel` (
    `idPro` SERIAL PRIMARY KEY,
    `idCompte` BIGINT UNSIGNED NOT NULL,
    `denominationPro` TEXT NOT NULL,
    `numSirenPro` TEXT NOT NULL,
    FOREIGN KEY (`idCompte`) REFERENCES `_compte`(`idCompte`)
);

CREATE TABLE IF NOT EXISTS `_membre` (
    `idMembre` SERIAL PRIMARY KEY,
    `idCompte` BIGINT UNSIGNED NOT NULL,
    `dateNaissanceMembre` DATE NOT NULL,
    FOREIGN KEY (`idCompte`) REFERENCES `_compte`(`idCompte`)
);

CREATE TABLE IF NOT EXISTS `_professionnelPublic` (
    `idProPublic` SERIAL PRIMARY KEY,
    `idPro` BIGINT UNSIGNED NOT NULL,
    FOREIGN KEY (`idPro`) REFERENCES `_professionnel`(`idPro`)
);

CREATE TABLE IF NOT EXISTS `_professionnelPrive` (
    `idProPrive` SERIAL PRIMARY KEY,
    `idPro` BIGINT UNSIGNED NOT NULL,
    `coordBancairesIBAN` TEXT NOT NULL,
    `coordBancairesBIC` TEXT NOT NULL,
    FOREIGN KEY (`idPro`) REFERENCES `_professionnel`(`idPro`)
);

-- 3. Créer les tables liées à `_professionnel` et `_adresse`
CREATE TABLE IF NOT EXISTS `_offre` (
    `idOffre` SERIAL PRIMARY KEY,
    `idProPropose` BIGINT UNSIGNED NOT NULL,
    `idAdresse` BIGINT UNSIGNED NOT NULL,
    `titreOffre` VARCHAR(255) NOT NULL,
    `resumeOffre` TEXT NOT NULL,
    `descriptionOffre` TEXT NOT NULL,
    `listeIdImage` TEXT NOT NULL,
    `prixMinOffre` FLOAT NOT NULL,
    `aLaUneOffre` BOOLEAN NOT NULL,
    `enReliefOffre` BOOLEAN NOT NULL,
    `typeOffre` INT NOT NULL,
    `noteMoyenneOffre` FLOAT NOT NULL,
    `commenaiteBlacklistable` BOOLEAN NOT NULL,
    `dateCreationOffre` DATE NOT NULL,
    `conditionAccessibilite` TEXT NOT NULL,
    `horsLigne` BOOLEAN NOT NULL,
    FOREIGN KEY (`idProPropose`) REFERENCES `_professionnel`(`idPro`),
    FOREIGN KEY (`idAdresse`) REFERENCES `_adresse`(`idAdresse`)
);

CREATE TABLE IF NOT EXISTS `_avis` (
    `idAvis` SERIAL PRIMARY KEY,
    `idOffre` BIGINT UNSIGNED NOT NULL,
    `noteAvis` INT NOT NULL,
    `commentaireAvis` TEXT NOT NULL,
    `listeIdImage` TEXT NOT NULL,
    `idMembre` BIGINT UNSIGNED NOT NULL,
    `dateAvis` DATE NOT NULL,
    `dateVisiteAvis` DATE NOT NULL,
    `blacklistAvis` BOOLEAN NOT NULL,
    `reponsePro` BOOLEAN NOT NULL,
    FOREIGN KEY (`idOffre`) REFERENCES `_offre`(`idOffre`),
    FOREIGN KEY (`idMembre`) REFERENCES `_membre`(`idMembre`)
);

CREATE TABLE IF NOT EXISTS `_reponseAvis` (
    `idReponseAvis` SERIAL PRIMARY KEY,
    `idAvis` BIGINT UNSIGNED NOT NULL,
    `idPro` BIGINT UNSIGNED NOT NULL,
    `texteReponse` TEXT NOT NULL,
    `dateReponse` DATE NOT NULL,
    FOREIGN KEY (`idAvis`) REFERENCES `_avis`(`idAvis`),
    FOREIGN KEY (`idPro`) REFERENCES `_professionnel`(`idPro`)
);

CREATE TABLE IF NOT EXISTS `_image` (
    `idImage` SERIAL PRIMARY KEY,
    `pathImage` TEXT NOT NULL
);

-- 4. Créer les types spécifiques d'offres qui héritent de `_offre`
CREATE TABLE IF NOT EXISTS `_offreActivite` (
    `idOffre` BIGINT UNSIGNED NOT NULL,
    `indicationDuree` DATE NOT NULL,
    `ageRequis` INT NOT NULL,
    `prestationIncluse` TEXT NOT NULL,
    `listIdTag` TEXT NOT NULL,
    FOREIGN KEY (`idOffre`) REFERENCES `_offre`(`idOffre`)
);

CREATE TABLE IF NOT EXISTS `_offreSpectacle` (
    `idOffre` BIGINT UNSIGNED NOT NULL,
    `dateOffre` DATE NOT NULL,
    `indicationDuree` DATE NOT NULL,
    `capaciteAcceuil` INT NOT NULL,
    `listIdTag` TEXT NOT NULL,
    FOREIGN KEY (`idOffre`) REFERENCES `_offre`(`idOffre`)
);

CREATE TABLE IF NOT EXISTS `_offreParcAttraction` (
    `idOffre` BIGINT UNSIGNED NOT NULL,
    `dateOuverture` DATE NOT NULL,
    `dateFermeture` DATE NOT NULL,
    `carteParc` INT NOT NULL,
    `nbrAttraction` INT NOT NULL,
    `ageMinimun` INT NOT NULL,
    `listIdTag` TEXT NOT NULL,
    FOREIGN KEY (`idOffre`) REFERENCES `_offre`(`idOffre`)
);

CREATE TABLE IF NOT EXISTS `_offreVisite` (
    `idOffre` BIGINT UNSIGNED NOT NULL,
    `dateOffre` DATE NOT NULL,
    `visiteGuidee` BOOLEAN NOT NULL,
    `langueProposees` BOOLEAN NOT NULL,
    `listIdTag` TEXT NOT NULL,
    FOREIGN KEY (`idOffre`) REFERENCES `_offre`(`idOffre`)
);

CREATE TABLE IF NOT EXISTS `_offreRestaurant` (
    `idOffre` BIGINT UNSIGNED NOT NULL,
    `horaireSemaine` TEXT NOT NULL,
    `gammePrix` INT NOT NULL,
    `carteResto` INT NOT NULL,
    `listIdTag` TEXT NOT NULL,
    FOREIGN KEY (`idOffre`) REFERENCES `_offre`(`idOffre`)
);

-- 5. Créer les autres tables liées
CREATE TABLE IF NOT EXISTS `_facture` (
    `idFacture` SERIAL PRIMARY KEY,
    `idProPrive` BIGINT UNSIGNED NOT NULL,
    `ìdConstPrix` BIGINT UNSIGNED NOT NULL,
    `dateFacture` DATE NOT NULL,
    `listeOffresSTD` TEXT NOT NULL,
    `listeOffresPREM` TEXT NOT NULL,
    'listeOffresALaUne' TEXT NOT NULL,
    'listeOffresEnRelief' TEXT NOT NULL,
    `montantHT` FLOAT NOT NULL,
    `montantTTC` FLOAT NOT NULL,
    FOREIGN KEY (`idProPrive`) REFERENCES `_professionnelPrive`(`idProPrive`)
);

CREATE TABLE IF NOT EXISTS `_constPrix` (
    `idConstPrix` SERIAL PRIMARY KEY,
    `prixSTD` FLOAT NOT NULL,
    `prixPREM` FLOAT NOT NULL,
    `prixALaUne` FLOAT NOT NULL,
    `prixEnRelief` FLOAT NOT NULL
);

-- 6. signalement
CREATE TABLE IF NOT EXISTS `_signalement`(
    `idSignalement` SERIAL PRIMARY KEY,
    `texteRaison` TEXT NOT NULL,
    `idOffre` BIGINT UNSIGNED,
    `idAvis` BIGINT UNSIGNED,
    FOREIGN KEY (`idOffre`) REFERENCES `_offre`(`idOffre`),
    FOREIGN KEY (`idAvis`) REFERENCES `_avis`(`idAvis`)
);
