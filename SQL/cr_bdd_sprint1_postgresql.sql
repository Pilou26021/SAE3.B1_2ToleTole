-- PostgreSql
DROP SCHEMA IF EXISTS public CASCADE;
CREATE SCHEMA public;

-- 1. Créer les tables indépendantes
CREATE TABLE public._adresse (
    idAdresse SERIAL PRIMARY KEY,
    numRue INT NOT NULL,
    supplementAdresse TEXT NOT NULL,
    adresse TEXT NOT NULL,
    codePostal INT NOT NULL,
    ville TEXT NOT NULL,
    departement TEXT NOT NULL,
    pays TEXT NOT NULL
);

CREATE TABLE public._tag (
    idTag SERIAL PRIMARY KEY,
    typeTag TEXT NOT NULL,
    typeRestauration BOOLEAN NOT NULL
);

CREATE TABLE public._image (
    idImage SERIAL PRIMARY KEY,
    pathImage TEXT NOT NULL
);

CREATE TABLE public._compte (
    idCompte SERIAL PRIMARY KEY,
    nomCompte TEXT NOT NULL,
    prenomCompte TEXT NOT NULL,
    mailCompte TEXT NOT NULL,
    numTelCompte TEXT NOT NULL,
    idImagePdp BIGINT NOT NULL,
    hashMdpCompte TEXT NOT NULL,
    dateCreationCompte DATE DEFAULT NOW(),
    dateDerniereConnexionCompte DATE NOT NULL,
    FOREIGN KEY (idImagePdp) REFERENCES public._image(idImage)
);

-- 2. Créer les tables liées à public._compte
CREATE TABLE public._professionnel (
    idPro SERIAL PRIMARY KEY,
    idCompte BIGINT NOT NULL,
    denominationPro TEXT NOT NULL,
    numSirenPro TEXT NOT NULL CHECK (9 <= LENGTH(numSirenPro) AND LENGTH(numSirenPro) <= 14),
    CONSTRAINT unique_professionnel UNIQUE (idPro, idCompte),
    FOREIGN KEY (idCompte) REFERENCES public._compte(idCompte)
);

CREATE TABLE public._membre (
    idMembre SERIAL PRIMARY KEY,
    idCompte BIGINT NOT NULL,
    dateNaissanceMembre DATE NOT NULL,
    CONSTRAINT unique_membre UNIQUE (idMembre, idCompte),
    FOREIGN KEY (idCompte) REFERENCES public._compte(idCompte)
);

CREATE TABLE public._professionnelPublic (
    idProPublic SERIAL PRIMARY KEY,
    idPro BIGINT NOT NULL,
    CONSTRAINT unique_professionnelPublic UNIQUE (idProPublic, idPro),
    FOREIGN KEY (idPro) REFERENCES public._professionnel(idPro)
);

CREATE TABLE public._professionnelPrive (
    idProPrive SERIAL PRIMARY KEY,
    idPro BIGINT NOT NULL,
    coordBancairesIBAN TEXT NOT NULL CHECK (16 <= LENGTH(coordBancairesIBAN) AND LENGTH(coordBancairesIBAN) <= 34),
    coordBancairesBIC TEXT NOT NULL CHECK (8 <= LENGTH(coordBancairesBIC) AND LENGTH(coordBancairesBIC) <= 11),
    CONSTRAINT unique_professionnelPrive UNIQUE (idProPrive, idPro),
    FOREIGN KEY (idPro) REFERENCES public._professionnel(idPro)
);

-- 3. Créer les tables liées à public._professionnel et public._adresse
CREATE TABLE public._offre (
    idOffre SERIAL PRIMARY KEY,
    idProPropose BIGINT NOT NULL,
    idAdresse BIGINT NOT NULL,
    titreOffre VARCHAR(255) NOT NULL,
    resumeOffre TEXT NOT NULL,
    descriptionOffre TEXT NOT NULL,
    prixMinOffre FLOAT NOT NULL CHECK (prixMinOffre >= 0),
    aLaUneOffre BOOLEAN NOT NULL,
    enReliefOffre BOOLEAN NOT NULL,
    typeOffre INT NOT NULL CHECK (typeOffre >= 0 AND typeOffre <= 2),
    siteWebOffre TEXT NOT NULL,
    noteMoyenneOffre FLOAT NOT NULL DEFAULT 0 CHECK (noteMoyenneOffre >= 0 AND noteMoyenneOffre <= 5),
    commentaireBlacklistable BOOLEAN NOT NULL,
    dateCreationOffre DATE NOT NULL,
    conditionAccessibilite TEXT NOT NULL,
    horsLigne BOOLEAN NOT NULL,
    FOREIGN KEY (idProPropose) REFERENCES public._professionnel(idPro),
    FOREIGN KEY (idAdresse) REFERENCES public._adresse(idAdresse)
);

CREATE TABLE public._avis (
    idAvis SERIAL PRIMARY KEY,
    idOffre BIGINT NOT NULL,
    noteAvis INT NOT NULL CHECK(noteAvis >= 1 AND noteAvis <= 5),
    commentaireAvis TEXT NOT NULL,
    idMembre BIGINT NOT NULL,
    dateAvis DATE NOT NULL,
    dateVisiteAvis DATE NOT NULL,
    blacklistAvis BOOLEAN NOT NULL,
    reponsePro BOOLEAN NOT NULL,
    CONSTRAINT unique_avis UNIQUE (idAvis, idOffre, idMembre),
    FOREIGN KEY (idOffre) REFERENCES public._offre(idOffre),
    FOREIGN KEY (idMembre) REFERENCES public._membre(idMembre)
);

CREATE TABLE public._signalement (
    idSignalement SERIAL PRIMARY KEY,
    raison TEXT NOT NULL
);

CREATE TABLE public._alerterOffre (
    idSignalement BIGINT NOT NULL,
    idOffre BIGINT NOT NULL,
    CONSTRAINT pk_alerterOffre PRIMARY KEY (idSignalement, idOffre),
    FOREIGN KEY (idOffre) REFERENCES public._offre(idOffre)
);

CREATE TABLE public._alerterAvis (
    idSignalement BIGINT NOT NULL,
    idAvis BIGINT NOT NULL,
    CONSTRAINT pk_alerterAvis PRIMARY KEY (idSignalement, idAvis),
    FOREIGN KEY (idAvis) REFERENCES public._avis(idAvis)
);  

-- duplication d'info, suppression idPro car déjà donné dans oFfre
CREATE TABLE public._reponseAvis (
    idReponseAvis SERIAL PRIMARY KEY,
    idAvis BIGINT NOT NULL,
    texteReponse TEXT NOT NULL,
    dateReponse DATE NOT NULL,
    CONSTRAINT unique_reponse UNIQUE (idReponseAvis, idAvis),
    FOREIGN KEY (idAvis) REFERENCES public._avis(idAvis)
);

CREATE TABLE public._afficherImageOffre (
    idImage BIGINT NOT NULL,
    idOffre BIGINT NOT NULL,
    CONSTRAINT pk_afficher PRIMARY KEY (idImage, idOffre),
    FOREIGN KEY (idImage) REFERENCES public._image(idImage),
    FOREIGN KEY (idOffre) REFERENCES public._offre(idOffre)
);

CREATE TABLE public._imageImageAvis (
    idImage BIGINT NOT NULL,
    idAvis BIGINT NOT NULL,
    CONSTRAINT pk_image PRIMARY KEY (idImage, idAvis),
    FOREIGN KEY (idImage) REFERENCES public._image(idImage),
    FOREIGN KEY (idAvis) REFERENCES public._avis(idAvis)
);

-- 4. Créer les types spécifiques d'offres qui héritent de public._offre
CREATE TABLE public._offreActivite (
    idOffre BIGINT NOT NULL,
    indicationDuree INT NOT NULL,
    ageMinimum INT NOT NULL CHECK (ageMinimum >= 0 AND ageMinimum <= 99),
    prestationIncluse TEXT NOT NULL,
    FOREIGN KEY (idOffre) REFERENCES public._offre(idOffre)
);

CREATE TABLE public._offreSpectacle (
    idOffre BIGINT NOT NULL,
    dateOffre DATE NOT NULL CHECK (dateOffre >= NOW()),
    indicationDuree INT NOT NULL,
    capaciteAcceuil INT NOT NULL,
    FOREIGN KEY (idOffre) REFERENCES public._offre(idOffre)
);

CREATE TABLE public._offreParcAttraction (
    idOffre BIGINT NOT NULL,
    dateOuverture DATE NOT NULL CHECK (dateOuverture >= NOW()),
    dateFermeture DATE NOT NULL CHECK (dateFermeture >= NOW()),
    carteParc INT NOT NULL,
    nbrAttraction INT NOT NULL,
    ageMinimum INT NOT NULL CHECK (ageMinimum >= 0 AND ageMinimum <= 99),
    FOREIGN KEY (idOffre) REFERENCES public._offre(idOffre)
);

CREATE TABLE public._offreVisite (
    idOffre BIGINT NOT NULL,
    dateOffre DATE NOT NULL,
    visiteGuidee BOOLEAN NOT NULL,
    langueProposees TEXT NOT NULL,
    FOREIGN KEY (idOffre) REFERENCES public._offre(idOffre)
);

CREATE TABLE public._offreRestaurant (
    idOffre BIGINT NOT NULL,
    horaireSemaine TEXT NOT NULL,
    gammePrix INT NOT NULL,
    carteResto INT NOT NULL,
    FOREIGN KEY (idOffre) REFERENCES public._offre(idOffre)
);

CREATE TABLE public._theme (
    idOffre BIGINT NOT NULL,
    idTag BIGINT NOT NULL,
    CONSTRAINT pk_theme PRIMARY KEY (idOffre, idTag),
    FOREIGN KEY (idOffre) REFERENCES public._offre(idOffre),
    FOREIGN KEY (idTag) REFERENCES public._tag(idTag)
);

-- 5. Créer les autres tables liées
CREATE TABLE public._facture (
    idFacture SERIAL PRIMARY KEY,
    idProPrive BIGINT NOT NULL,
    idConstPrix BIGINT NOT NULL,
    dateFacture DATE NOT NULL,
    montantHT FLOAT NOT NULL,
    montantTTC FLOAT NOT NULL,
    FOREIGN KEY (idProPrive) REFERENCES public._professionnelPrive(idProPrive)
);

CREATE TABLE public._constPrix (
    idConstPrix SERIAL PRIMARY KEY,
    prixSTD FLOAT NOT NULL,
    prixPREM FLOAT NOT NULL,
    prixALaUne FLOAT NOT NULL,
    prixEnRelief FLOAT NOT NULL
);

CREATE TABLE public._paiement (
    idOffre BIGINT NOT NULL,
    idFacture BIGINT NOT NULL,
    CONSTRAINT pk_paiement PRIMARY KEY (idOffre, idFacture),
    FOREIGN KEY (idOffre) REFERENCES public._offre(idOffre),
    FOREIGN KEY (idFacture) REFERENCES public._facture(idFacture)
);

-- vue offre avec adresse
CREATE VIEW public.offreAdresse AS
SELECT o.idOffre, a.numRue, a.supplementAdresse, a.adresse, a.codePostal, a.ville, a.departement, a.pays
FROM public._offre o
JOIN public._adresse a ON o.idAdresse = a.idAdresse;

-- vue offre id et image
CREATE VIEW public.offreImage AS
SELECT o.idOffre, i.pathImage
FROM public._offre o
JOIN public._afficherImageOffre aio ON o.idOffre = aio.idOffre
JOIN public._image i ON aio.idImage = i.idImage;

-- trigger pour mettre à jour la note moyenne de l'offre
CREATE OR REPLACE FUNCTION update_note_moyenne_offre()
RETURNS TRIGGER AS $$
BEGIN
    UPDATE public._offre
    SET noteMoyenneOffre = (SELECT AVG(noteAvis) FROM public._avis WHERE idOffre = NEW.idOffre)
    WHERE idOffre = NEW.idOffre;
    RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';

CREATE TRIGGER trg_update_note_moyenne_offre
AFTER INSERT
ON public._avis
FOR EACH ROW
EXECUTE FUNCTION update_note_moyenne_offre();

-- Trigger commentaire blacklistable si offre type 2 alors commentaire blacklistable
CREATE OR REPLACE FUNCTION update_commentaire_blacklistable()
RETURNS TRIGGER AS $$
BEGIN
    UPDATE public._offre
    SET commentaireBlacklistable = TRUE
    WHERE idOffre = NEW.idOffre AND typeOffre = 2;
    RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';

CREATE TRIGGER trg_update_commentaire_blacklistable
AFTER INSERT
ON public._avis
FOR EACH ROW
EXECUTE FUNCTION update_commentaire_blacklistable();

-- date de création de l'offre
CREATE OR REPLACE FUNCTION update_date_creation_offre()
RETURNS TRIGGER AS $$
BEGIN
    UPDATE public._offre
    SET dateCreationOffre = NOW()
    WHERE idOffre = NEW.idOffre;
    RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';

-- trigger pour mettre en place la facturation (calcul du montant TTC, HT)
CREATE OR REPLACE FUNCTION facturation()
RETURNS TRIGGER AS $$
BEGIN
    INSERT INTO public._facture (idProPrive, idConstPrix, dateFacture, montantHT, montantTTC)
    VALUES (NEW.idProPropose, 1, NOW(), NEW.prixMinOffre, NEW.prixMinOffre * 1.2);
    RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';

-- vue professionel avec mdp
CREATE VIEW public.professionnelMdp AS
SELECT p.idPro, c.mailCompte, c.hashMdpCompte
FROM public._professionnel p
JOIN public._compte c ON p.idCompte = c.idCompte;

-- vue professionel public
CREATE VIEW public.professionnelPublic AS
SELECT p.idPro, c.nomCompte, c.prenomCompte, c.mailCompte, c.numTelCompte, c.idImagePdp
FROM public._professionnel p
JOIN public._compte c ON p.idCompte = c.idCompte;

-- vue professionnel privé
CREATE VIEW public.professionnelPrive AS
SELECT p.idPro, c.nomCompte, c.prenomCompte, c.mailCompte, c.numTelCompte, c.idImagePdp, pp.coordBancairesIBAN, pp.coordBancairesBIC
FROM public._professionnel p
JOIN public._compte c ON p.idCompte = c.idCompte
JOIN public._professionnelPrive pp ON p.idPro = pp.idPro;

-- vue membre
CREATE VIEW public.membre AS
SELECT m.idMembre, c.nomCompte, c.prenomCompte, c.mailCompte, c.numTelCompte, c.idImagePdp, m.dateNaissanceMembre
FROM public._membre m
JOIN public._compte c ON m.idCompte = c.idCompte;

-- vue avis avec leur réponse
CREATE VIEW public.avisReponse AS
SELECT a.idAvis, a.idOffre, a.noteAvis, a.commentaireAvis, a.idMembre, a.dateAvis, a.dateVisiteAvis, a.blacklistAvis, a.reponsePro, r.texteReponse, r.dateReponse
FROM public._avis a
LEFT JOIN public._reponseAvis r ON a.idAvis = r.idAvis;

-- vue avis avec leurs images
CREATE VIEW public.avisImage AS
SELECT a.idAvis, a.idOffre, i.pathImage
FROM public._avis a
JOIN public._imageImageAvis iia ON a.idAvis = iia.idAvis
JOIN public._image i ON iia.idImage = i.idImage;

-- vue compte et professionel et image
CREATE VIEW public.compteProfessionnelImage AS
SELECT c.idCompte, c.nomCompte, c.prenomCompte, c.mailCompte, c.numTelCompte, c.idImagePdp, p.idPro, i.pathImage
FROM public._compte c
JOIN public._professionnel p ON c.idCompte = p.idCompte
JOIN public._image i ON c.idImagePdp = i.idImage;

-- trouver catégorie d'offre
-- _offreactivite = 1
-- _offreparcattraction = 2	
-- _offrerestaurant	= 3
-- _offrespectacle = 4
-- _offrevisite	= 5
DROP FUNCTION IF EXISTS trouver_categorie_offre(INTEGER);
CREATE OR REPLACE FUNCTION trouver_categorie_offre(id_Offre INTEGER)
RETURNS INTEGER AS $$
DECLARE
    categorie INTEGER;
BEGIN
    -- Vérification si l'offre se trouve dans _offreactivite
    IF EXISTS (SELECT 1 FROM _offreactivite WHERE idOffre = id_Offre) THEN
        categorie := 1;
    -- Vérification si l'offre se trouve dans _offreparcattraction
    ELSIF EXISTS (SELECT 1 FROM _offreparcattraction WHERE idOffre = id_Offre) THEN
        categorie := 2;
    -- Vérification si l'offre se trouve dans _offrerestaurant
    ELSIF EXISTS (SELECT 1 FROM _offrerestaurant WHERE idOffre = id_Offre) THEN
        categorie := 3;
    -- Vérification si l'offre se trouve dans _offrespectacle
    ELSIF EXISTS (SELECT 1 FROM _offrespectacle WHERE idOffre = id_Offre) THEN
        categorie := 4;
    -- Vérification si l'offre se trouve dans _offrevisite
    ELSIF EXISTS (SELECT 1 FROM _offrevisite WHERE idOffre = id_Offre) THEN
        categorie := 5;
    ELSE
        categorie := NULL;  -- Si l'offre ne se trouve dans aucune catégorie
    END IF;

    RETURN categorie;
END;
$$ LANGUAGE 'plpgsql';

-- vue pour voir les tags d'une offre
CREATE VIEW public.offreTag AS
SELECT o.idOffre, t.typeTag, t.typeRestauration
FROM public._offre o
JOIN public._theme th ON o.idOffre = th.idOffre
JOIN public._tag t ON th.idTag = t.idTag;
