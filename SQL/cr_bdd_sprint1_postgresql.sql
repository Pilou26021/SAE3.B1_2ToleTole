-- PostgreSql
DROP SCHEMA IF EXISTS public CASCADE;
CREATE SCHEMA public;

-- 1. Créer les tables indépendantes
CREATE TABLE public._adresse (
    idAdresse SERIAL PRIMARY KEY,
    numRue INT NOT NULL,
    supplementAdresse TEXT,
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
    idAdresse BIGINT NOT NULL,
    dateCreationCompte DATE DEFAULT NOW(),
    dateDerniereConnexionCompte DATE NOT NULL,
    chat_cleApi TEXT,
    chat_cledevoile BOOLEAN DEFAULT FALSE,
    auth_secret TEXT,
    auth_parametre BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (idImagePdp) REFERENCES public._image(idImage),
    FOREIGN KEY (idAdresse) REFERENCES public._adresse(idAdresse)
);

-- Chatator

CREATE TABLE public._chat_tokensession (
    idChatTokenSession SERIAL PRIMARY KEY,
    idCompte BIGINT NOT NULL,
    tokenSession TEXT NOT NULL,
    -- Le token de session est valide 1h
    dateExpiration TIMESTAMP NOT NULL DEFAULT NOW() + INTERVAL '1 hour',
    FOREIGN KEY (idCompte) REFERENCES public._compte(idCompte)
);

CREATE TABLE public._chat_message (
    idmessage SERIAL PRIMARY KEY,
    timestamp_envoie TIMESTAMP NOT NULL DEFAULT NOW(),
    derniere_modif TIMESTAMP NOT NULL DEFAULT NOW(),
    emetteur VARCHAR(255) NOT NULL,
    id_emetteur INT NOT NULL,
    destinataire VARCHAR(255) NOT NULL,
    id_destinataire INT NOT NULL,
    direction VARCHAR(10) NOT NULL CHECK (direction IN ('recu', 'emis')), -- Sens : reçu ou émis
    est_supprime BOOLEAN NOT NULL DEFAULT FALSE,
    content TEXT NOT NULL CHECK (LENGTH(content) <= 1000),
    CONSTRAINT check_max_size CHECK (LENGTH(content) <= 1000) 
);

CREATE TABLE public._chat_blocage (
    idBlocage SERIAL PRIMARY KEY,
    idCompteBloque BIGINT NOT NULL,
    idCompteBloquant BIGINT NOT NULL,
    dateBlocage DATE NOT NULL,
    tempsBlocage INT NOT NULL,
    FOREIGN KEY (idCompteBloque) REFERENCES public._compte(idCompte),
    FOREIGN KEY (idCompteBloquant) REFERENCES public._compte(idCompte)
);

CREATE TABLE public._chat_bannissement (
    idBannissement SERIAL PRIMARY KEY,
    idCompteBanni BIGINT NOT NULL,
    idCompteBannisseur BIGINT NOT NULL,
    dateBannissement DATE NOT NULL,
    FOREIGN KEY (idCompteBanni) REFERENCES public._compte(idCompte),
    FOREIGN KEY (idCompteBannisseur) REFERENCES public._compte(idCompte)
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

CREATE TABLE public._notification (
    idNotification SERIAL PRIMARY KEY,
    idCompte BIGINT NOT NULL,
    idOffre SERIAL NOT NULL,
    messageNotification TEXT NOT NULL,
    dateNotification DATE NOT NULL,
    lu BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (idCompte) REFERENCES public._compte(idCompte)
);

CREATE TABLE public._membre (
    idMembre SERIAL PRIMARY KEY,
    idCompte BIGINT NOT NULL,
    pseudonyme TEXT NOT NULL,
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
    coordBancairesIBAN TEXT,
    coordBancairesBIC TEXT,
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
    -- Détermination du type d'offre (1: Standard, 2: Premium, 0: Gratuit)
    typeOffre INT NOT NULL CHECK (typeOffre >= 0 AND typeOffre <= 2),
    siteWebOffre TEXT NOT NULL,
    noteMoyenneOffre FLOAT NOT NULL DEFAULT 0 CHECK (noteMoyenneOffre >= 0 AND noteMoyenneOffre <= 5),
    commentaireBlacklistable BOOLEAN NOT NULL,
    dateCreationOffre DATE NOT NULL,
    conditionAccessibilite TEXT NOT NULL,
    horsLigne BOOLEAN NOT NULL,
    nbrJetonBlacklistageRestant INT NOT NULL DEFAULT 3 CHECK (nbrJetonBlacklistageRestant >= 0),
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
    reponsePro BOOLEAN NOT NULL,
    scorePouce INT NOT NULL,
    blacklistAvis BOOLEAN NOT NULL,
    blacklistEndDate TIMESTAMP DEFAULT NULL,
    CONSTRAINT unique_avis UNIQUE (idAvis, idOffre, idMembre),
    FOREIGN KEY (idOffre) REFERENCES public._offre(idOffre),
    FOREIGN KEY (idMembre) REFERENCES public._membre(idMembre)
);

CREATE TABLE public._signalement (
    idSignalement SERIAL PRIMARY KEY,
    raison TEXT NOT NULL
);

CREATE TABLE public._alerterOffre (
    idAlerterOffre SERIAL PRIMARY KEY,
    idSignalement BIGINT NOT NULL,
    idOffre BIGINT NOT NULL,
    FOREIGN KEY (idOffre) REFERENCES public._offre(idOffre)
);

CREATE TABLE public._alerterAvis (
    idAlerterAvis SERIAL PRIMARY KEY,
    idSignalement BIGINT NOT NULL,
    idAvis BIGINT NOT NULL,
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

-- 5. Tables liées au système de facturation
CREATE TABLE public._facture (
    idFacture SERIAL PRIMARY KEY,
    idProPrive BIGINT NOT NULL,
    idConstPrix BIGINT NOT NULL,
    dateFacture DATE NOT NULL,
    montantHT FLOAT NOT NULL,
    montantTTC FLOAT NOT NULL,
    nbJoursMiseHorsLigne INT NOT NULL,
    FOREIGN KEY (idProPrive) REFERENCES public._professionnelPrive(idProPrive)
);

CREATE TABLE public._dateStatusOffre (
    idDateStatus SERIAL PRIMARY KEY,
    idOffre BIGINT NOT NULL,
    dateStatusChange DATE NOT NULL,
    statusOffre INT NOT NULL CHECK (statusOffre >= 0 AND statusOffre <= 2),
    estActive BOOLEAN NOT NULL,
    FOREIGN KEY (idOffre) REFERENCES public._offre(idOffre)
);

CREATE TABLE public._constPrix (
    idConstPrix SERIAL PRIMARY KEY,
    dateTarif DATE NOT NULL DEFAULT NOW(),
    prixSTDht FLOAT NOT NULL,
    prixSTDttc FLOAT NOT NULL,
    prixPREMht FLOAT NOT NULL,
    prixPREMttc FLOAT NOT NULL,
    prixALaUneht FLOAT NOT NULL,
    prixALaUnettc FLOAT NOT NULL,
    prixEnReliefht FLOAT NOT NULL,
    prixEnReliefttc FLOAT NOT NULL
);

CREATE TABLE public._paiement (
    idOffre BIGINT NOT NULL,
    idFacture BIGINT NOT NULL,
    idConstPrix BIGINT NOT NULL,
    datePaiement DATE NOT NULL,
    estPaye BOOLEAN NOT NULL,
    CONSTRAINT pk_paiement PRIMARY KEY (idOffre, idFacture, idConstPrix),
    FOREIGN KEY (idOffre) REFERENCES public._offre(idOffre),
    FOREIGN KEY (idFacture) REFERENCES public._facture(idFacture)
);

CREATE TABLE public._favoris (
    idFavoris SERIAL PRIMARY KEY,
    idMembre INT NOT NULL,
    idOffre INT NOT NULL,
    dateAjout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idMembre) REFERENCES public._membre(idMembre),
    FOREIGN KEY (idOffre) REFERENCES public._offre(idOffre),
    CONSTRAINT unique_favoris UNIQUE (idMembre, idOffre)
);

-- Système de facturation mensuelle

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
    SET noteMoyenneOffre = COALESCE((SELECT AVG(noteAvis) FROM public._avis WHERE idOffre = COALESCE(NEW.idOffre, OLD.idOffre)), 0)
    WHERE idOffre = COALESCE(NEW.idOffre, OLD.idOffre);
    RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';

CREATE TRIGGER trg_update_note_moyenne_offre_insert
AFTER INSERT OR DELETE OR UPDATE
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
SELECT m.idMembre, c.idCompte, c.nomCompte, c.prenomCompte, c.mailCompte, c.numTelCompte, c.idImagePdp, c.hashMdpCompte, m.pseudonyme
FROM public._membre m
JOIN public._compte c ON m.idCompte = c.idCompte;

-- vue professionnel
CREATE VIEW public.professionnel AS
SELECT p.idPro, c.idCompte, c.nomCompte, c.prenomCompte, c.mailCompte, c.numTelCompte, c.hashMdpCompte, c.idImagePdp, i.pathImage
FROM public._professionnel p
JOIN public._compte c ON p.idCompte = c.idCompte
JOIN public._image i ON c.idImagePdp = i.idImage;

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

-- vue compte  et image
CREATE VIEW public.compteImage AS
SELECT c.idCompte, c.nomCompte, c.prenomCompte, c.mailCompte, c.numTelCompte, c.idImagePdp, i.pathImage
FROM public._compte c
JOIN public._image i ON c.idImagePdp = i.idImage;

-- trouver catégorie d'offre
-- _offreactivite = 1
-- _offreparcattraction = 2	
-- _offrerestaurant	= 3
-- _offrespectacle = 4
-- _offrevisite	= 5
CREATE OR REPLACE FUNCTION public.trouver_categorie_offre(id_Offre INTEGER)
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
GRANT EXECUTE ON FUNCTION public.trouver_categorie_offre(integer) TO public;

-- vue pour voir les tags d'une offre
CREATE VIEW public.offreTag AS
SELECT o.idOffre, t.typeTag, t.typeRestauration
FROM public._offre o
JOIN public._theme th ON o.idOffre = th.idOffre
JOIN public._tag t ON th.idTag = t.idTag;

-- Vue pour avoir avis, alerteravis et signalement
CREATE VIEW public.avisSignalement AS
SELECT a.idAvis, a.idOffre, a.noteAvis, a.commentaireAvis, a.idMembre, a.dateAvis, a.dateVisiteAvis, a.blacklistAvis, a.reponsePro, a.scorePouce, s.raison
FROM public._avis a
JOIN public._alerterAvis aa ON a.idAvis = aa.idAvis
JOIN public._signalement s ON aa.idSignalement = s.idSignalement;


-- Trigger et fonction pour le blacklistage
CREATE OR REPLACE FUNCTION update_blacklist()
RETURNS void AS $$
BEGIN
    -- Ajouter un jeton à l'offre liée à chaque avis mis à jour
    UPDATE public._offre
    SET nbrJetonBlacklistageRestant = nbrJetonBlacklistageRestant + 1
    WHERE idOffre IN (
        SELECT DISTINCT idOffre
        FROM public._avis
        WHERE blacklistEndDate IS NOT NULL AND blacklistEndDate <= CURRENT_TIMESTAMP
    );

    -- Mettre à jour toutes les lignes d'avis
    UPDATE public._avis
    SET blacklistAvis = FALSE,
        blacklistEndDate = NULL
    WHERE blacklistEndDate IS NOT NULL 
    AND blacklistEndDate <= CURRENT_TIMESTAMP;

END;
$$ LANGUAGE plpgsql;