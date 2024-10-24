-- Utiliser le schéma `sae`
SET SCHEMA 'public';

-- 1. Insérer des données dans `_image`
INSERT INTO _image (pathImage) VALUES 
('./img/uploaded/image1.png'),
('./img/uploaded/image2.png'),
('./img/uploaded/image3.png');

-- 2. Insérer des données dans `_compte`
INSERT INTO _compte (nomCompte, prenomCompte, mailCompte, numTelCompte, idImagePdp, hashMdpCompte, dateCreationCompte, dateDerniereConnexionCompte) VALUES 
('Smith', 'John', 'john.smith@example.com', '0123456789', 3, '$2y$10$RkM09lrLhpt74shzr/w0Euihc4LraI0K2fSg3WNbzoDsbg7kFKsC6', '2023-01-15', '2025-01-15'),
('Doe', 'Jane', 'jane.doe@example.com', '0987654321', 3, '$2y$10$R0AEBas/G8eyQM3XWdG.Ie0knRnf1yr4M22WIImwKkxH1IX4grwzu', '2023-02-20', '2025-02-20'),
('Denis', 'Liam', 'liamdenis35@gmail.com', '0987654321', 3, '$2y$10$WQZfbr1fhF.uPf8NBXtJd.4wln6z5OrF635Lc4.DpUv5AmjsOVw7i', '2023-02-20', '2025-02-20');

-- 3. Insérer des données dans `_adresse`
INSERT INTO _adresse (numRue, supplementAdresse, adresse, codePostal, ville, departement, pays) VALUES 
(17, '', 'Rue de Trolay', 22700, 'Perros-Guirec', 'Bretagne', 'France'),
(1, '', 'Place du Roi Saint-Judicael', 35380, 'Paimpont', 'Bretagne', 'France');

-- 4. Insérer des données dans `_professionnel`
INSERT INTO _professionnel (idCompte, denominationPro, numSirenPro) VALUES 
(1, 'Tech Solutions', '123456789'),
(2, 'Design Experts', '987654321'),
(3, 'LIAM CO', '983455432');

-- 5. Insérer des données dans `_membre`
INSERT INTO _membre (idCompte, dateNaissanceMembre) VALUES 
(1, '1990-05-12'),
(2, '1985-07-22');

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
(3, 1, 'Côtes de Granit Rose', 'Visiter les magnifiques cotes de granit rose', 'Description de l offre 1', 20, TRUE, FALSE, 1, 'https://ilovemyself.com',4.5, FALSE, '2023-05-01', 'Accessible', FALSE),
(2, 2, 'Forêt de Brocéliande', 'Le celebre Jardin de Broceliande vous attend', 'Description de l offre 2', 10, FALSE, TRUE, 2, 'https://pnevot.com',4.0, TRUE, '2023-06-01', 'Non accessible', FALSE);

-- 9. Insérer des données dans `_avis`
INSERT INTO _avis (idOffre, noteAvis, commentaireAvis, idMembre, dateAvis, dateVisiteAvis, blacklistAvis, reponsePro) VALUES 
(1, 5, 'Excellente offre!', 1, '2023-05-15', '2023-05-10', FALSE, TRUE),
(2, 3, 'Moyenne, pourrait être mieux.', 2, '2023-06-15', '2023-06-10', FALSE, FALSE);

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
(3, 1);

-- 15. Insérer des données dans `_imageImageAvis`
INSERT INTO _imageImageAvis (idImage, idAvis) VALUES 
(1, 1),
(2, 2);

-- 16. Insérer des données dans `_offreActivite`
INSERT INTO _offreActivite (idOffre, indicationDuree, ageMinimum, prestationIncluse) VALUES 
(1, 2, 12, 'Guide inclus'),
(2, 3, 10, 'Collation incluse');

-- 17. Insérer des données dans `_offreSpectacle`
INSERT INTO _offreSpectacle (idOffre, dateOffre, indicationDuree, capaciteAcceuil) VALUES 
(1, '2025-06-01', 2, 100),
(2, '2025-07-01', 1, 50);

-- 18. Insérer des données dans `_offreParcAttraction`
INSERT INTO _offreParcAttraction (idOffre, dateOuverture, dateFermeture, carteParc, nbrAttraction, ageMinimum) VALUES 
(1, '2025-06-01', '2025-06-30', 5, 10, 3),
(2, '2025-07-01', '2025-07-31', 10, 20, 5);

-- 19. Insérer des données dans `_offreVisite`
INSERT INTO _offreVisite (idOffre, dateOffre, visiteGuidee, langueProposees) VALUES 
(1, '2025-06-01', TRUE, TRUE),
(2, '2025-07-01', FALSE, TRUE);

-- 20. Insérer des données dans `_offreRestaurant`
INSERT INTO _offreRestaurant (idOffre, horaireSemaine, gammePrix, carteResto) VALUES 
(1, '9h - 22h', 30, 1),
(2, '10h - 23h', 50, 2);

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
(1, 1, '2025-01-01', 100.0, 120.0),
(2, 2, '2025-02-01', 200.0, 240.0);

-- 24. Insérer des données dans `_constPrix`
INSERT INTO _constPrix (prixSTD, prixPREM, prixALaUne, prixEnRelief) VALUES 
(10.0, 20.0, 30.0, 40.0);

-- 25. Insérer des données dans `_paiement`
INSERT INTO _paiement (idOffre, idFacture) VALUES 
(1, 1),
(2, 2);
