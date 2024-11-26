-- Utiliser le schéma `public`
SET SCHEMA 'public';

-- 1. Insérer des données dans `_image`
INSERT INTO _image (pathImage) VALUES 
('./img/uploaded/image1.png'),
('./img/uploaded/image2.png'),
('./img/uploaded/image3.png'),
('./img/uploaded/image4.png'),
('./img/uploaded/image5.png'),
('./img/uploaded/image6.png'), --lou
('./img/uploaded/image7.png'), --liam
('./img/uploaded/image8.png'), --piel
('./img/uploaded/image9.png'), --Jane
('./img/uploaded/image10.png'), --Gary
('./img/uploaded/image11.png'); --Jose

-- 2. Insérer des données dans `_compte`
INSERT INTO _compte (nomCompte, prenomCompte, mailCompte, numTelCompte, idImagePdp, hashMdpCompte, dateCreationCompte, dateDerniereConnexionCompte) VALUES 
('Smith', 'John', 'john.smith@example.com', '0123456789', 3, '$2y$10$RkM09lrLhpt74shzr/w0Euihc4LraI0K2fSg3WNbzoDsbg7kFKsC6', '2023-01-15', '2025-01-15'),

('Le Verge', 'Lou', 'lou.leverge@example.com', '0123456789', 6, '$2y$10$RkM09lrLhpt74shzr/w0Euihc4LraI0K2fSg3WNbzoDsbg7kFKsC6', '2023-01-15', '2025-01-15'),
('Denis', 'Liam', 'liamdenis35@gmail.com', '0987654321', 7, '$2y$10$WQZfbr1fhF.uPf8NBXtJd.4wln6z5OrF635Lc4.DpUv5AmjsOVw7i', '2023-02-20', '2025-02-20'),
('Mallet', 'Piel', 'piel.mallet@example.com', '0123456789', 8, '$2y$10$RkM09lrLhpt74shzr/w0Euihc4LraI0K2fSg3WNbzoDsbg7kFKsC6', '2023-01-15', '2025-01-15'),
('Doe', 'Jane', 'jane.doe@example.com', '0987654321', 9, '$2y$10$R0AEBas/G8eyQM3XWdG.Ie0knRnf1yr4M22WIImwKkxH1IX4grwzu', '2023-02-20', '2025-02-20'),
('Buss', 'Gary ', 'gary.buss@example.com', '3015138427', 10, '$2y$10$RkM09lrLhpt74shzr/w0Euihc4LraI0K2fSg3WNbzoDsbg7kFKsC6', '2023-01-15', '2025-01-15'),
('Laberge', 'Jose ', 'jose.laberge@example.com', '5308287564', 11, '$2y$10$RkM09lrLhpt74shzr/w0Euihc4LraI0K2fSg3WNbzoDsbg7kFKsC6', '2023-01-15', '2025-01-15');

-- 3. Insérer des données dans `_adresse`
INSERT INTO _adresse (numRue, supplementAdresse, adresse, codePostal, ville, departement, pays) VALUES 
(17, '', 'Rue de Trolay', 22700, 'Perros-Guirec', 'Bretagne', 'France'),
(5, '', 'Place du Roi Saint-Judicael', 35380, 'Paimpont', 'Bretagne', 'France'),
(4, '', 'Rue Edouard Branly', 22300, 'Lannion', 'Bretagne', 'France');

-- 4. Insérer des données dans `_professionnel`
INSERT INTO _professionnel (idCompte, denominationPro, numSirenPro) VALUES 
(1, 'Tech Solutions', '123456789'),
(2, 'Design Experts', '987654321'),
(3, 'LIAM CO', '983455432');

-- 5. Insérer des données dans `_membre`
INSERT INTO _membre (idCompte, dateNaissanceMembre) VALUES 
(2, '2003-05-05'),
(4, '2004-08-23'),
(5, '2003-05-05'),
(6, '1995-08-23'),
(7, '1989-08-23');

-- 6. Insérer des données dans `_professionnelPublic`
INSERT INTO _professionnelPublic (idPro) VALUES 
(1),
(2);

-- 7. Insérer des données dans `_professionnelPrive`
INSERT INTO _professionnelPrive (idPro, coordBancairesIBAN, coordBancairesBIC) VALUES 
(1, 'FR7630006000011234567890189', 'BIC12345XXX'),
(2, 'FR7630006000019876543210987', 'BIC54321XXX');

-- 8. Insérer des données dans `_offre`
INSERT INTO _offre (idProPropose, idAdresse, titreOffre, resumeOffre, descriptionOffre, prixMinOffre, aLaUneOffre, enReliefOffre, typeOffre, siteWebOffre, noteMoyenneOffre, commentaireBlacklistable, dateCreationOffre, conditionAccessibilite, horsLigne) VALUES 
(3, 1, 'Côtes de Granit Rose', 'Visiter les magnifiques cotes de granit rose', 'Description de l offre 1', 150, TRUE, FALSE, 1, 'https://ilovemyself.com',0, FALSE, '2023-05-01', 'Accessible', FALSE),
(2, 2, 'Forêt de Brocéliande', 'Le celebre Jardin de Broceliande vous attend', 'Description de l offre 2', 100, FALSE, TRUE, 2, 'https://pnevot.com',0, TRUE, '2023-06-01', 'Non accessible', FALSE),
(1, 3, 'Restaurant Universitaire', 'Venez déguster nos plats', 'Ici au RU, on vous propose des plats variés et équilibrés', 50, FALSE, FALSE, 0, 'https://www.crous-rennes.fr/restaurant/resto-u-branly-3/', 1.0, FALSE, '2023-06-01', 'Accessible', FALSE);

-- 9. Insérer des données dans `_avis`
INSERT INTO _avis (idOffre, noteAvis, commentaireAvis, idMembre, dateAvis, dateVisiteAvis, blacklistAvis, reponsePro) VALUES 
(1, 5, 'Excellente offre!', 1, '2023-05-15', '2023-05-10', FALSE, TRUE),
(2, 3, 'Moyenne, pourrait être mieux.', 2, '2023-06-15', '2023-06-10', FALSE, FALSE),

(3, 4, 'Bonne offre! J''y retournerais sans problème !', 2, '2023-05-15', '2023-05-10', FALSE, FALSE),
(3, 3, 'Les repas sont peu cher mais le choix laisse à désirer.', 3, '2023-04-23', '2023-04-23', FALSE, FALSE),
(3, 2, 'Pas encore ouvert :''(', 4, '1955-11-11', '1955-11-11', FALSE, FALSE),
(3, 4, 'Personnel professionnel et sympathique !', 5, '2022-09-12', '2022-09-12', FALSE, FALSE);

-- 10. Insérer des données dans `_signalement`
INSERT INTO _signalement (raison) VALUES 
('Spam'),
('Contenu inapproprié');

-- 11. Insérer des données dans `_alerterOffre`
INSERT INTO _alerterOffre (idSignalement, idOffre) VALUES 
(1, 1),
(2, 2);

-- 12. Insérer des données dans `_alerterAvis`
INSERT INTO _alerterAvis (idSignalement, idAvis) VALUES 
(1, 1),
(2, 2);

-- 13. Insérer des données dans `_reponseAvis`
INSERT INTO _reponseAvis (idAvis, texteReponse, dateReponse) VALUES 
(1, 'Merci pour votre avis!', '2023-05-16'),
(2, 'Nous prenons vos retours en compte.', '2023-06-16');

-- 14. Insérer des données dans `_afficherImageOffre`
INSERT INTO _afficherImageOffre (idImage, idOffre) VALUES 
(1, 1),
(2, 2),
(5, 3);

-- 15. Insérer des données dans `_imageImageAvis`
INSERT INTO _imageImageAvis (idImage, idAvis) VALUES 
(1, 1),
(2, 2);

-- 16. Insérer des données dans `_offreActivite`
INSERT INTO _offreActivite (idOffre, indicationDuree, ageMinimum, prestationIncluse) VALUES 
(1, 2, 12, 'Guide inclus'),
(2, 3, 10, 'Collation incluse');

-- 17. Insérer des données dans `_offreSpectacle`
-- INSERT INTO _offreSpectacle (idOffre, dateOffre, indicationDuree, capaciteAcceuil) VALUES 

-- 18. Insérer des données dans `_offreParcAttraction`
-- INSERT INTO _offreParcAttraction (idOffre, dateOuverture, dateFermeture, carteParc, nbrAttraction, ageMinimum) VALUES 

-- 19. Insérer des données dans `_offreVisite`
-- INSERT INTO _offreVisite (idOffre, dateOffre, visiteGuidee, langueProposees) VALUES 

-- 20. Insérer des données dans `_offreRestaurant`
INSERT INTO _offreRestaurant (idOffre, horaireSemaine, gammePrix, carteResto) VALUES 
(3, '{"lunchOpen":"11:30","lunchClose":"13:30","dinnerOpen":"00:00","dinnerClose":"00:00"}', 1, '4');

-- 21. Insérer des données dans `_tag`
INSERT INTO _tag (typeTag, typeRestauration) VALUES 
-- Tag restauration :
('Française', TRUE),
('Fruit de mer', TRUE),
('Asiatique', TRUE),
('Indienne', TRUE),
('Italienne', TRUE),
('Gastronomique', TRUE),
('Restauration rapide', TRUE),
('Crêperie', TRUE),
--Tag autres :
('Classique', FALSE),
('Culturel', FALSE),
('Patrimoine', FALSE),
('Histoire', FALSE),
('Urbain', FALSE),
('Nature', FALSE),
('Plein air', FALSE),
('Sport', FALSE),
('Nautique', FALSE),
('Gastronomie', FALSE),
('Musée', FALSE),
('Atelier', FALSE),
('Musique', FALSE),
('Famille', FALSE),
('Cinéma', FALSE),
('Cirque', FALSE),
('Son et Lumière', FALSE),
('Humour', FALSE);


-- 22. Insérer des données dans `_theme`
INSERT INTO _theme (idOffre, idTag) VALUES 
(1, 1),
(2, 2);

-- 23. Insérer des données dans `_facture`
INSERT INTO _facture (idProPrive, idConstPrix, dateFacture, montantHT, montantTTC) VALUES 
(1, 1, '2025-01-01', 0, 0),
(2, 1, '2025-02-01', 0, 0);

-- 24. Insérer des données dans `_constPrix`
INSERT INTO _constPrix (prixSTDht, prixSTDttc, prixPREMht, prixPREMttc, prixALaUneht, prixALaUnettc, prixEnReliefht, prixEnReliefttc) VALUES 
(1.67, 2.0, 3.34, 4.0, 16.68, 20.0, 8.34, 10.0);

-- 25. Insérer des données dans `_paiement`
INSERT INTO _paiement (idOffre, idFacture, idConstPrix) VALUES 
(1, 1, 1),
(2, 2, 1);
