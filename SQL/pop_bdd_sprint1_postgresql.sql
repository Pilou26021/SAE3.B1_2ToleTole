-- Utiliser le schéma `sae`
SET SCHEMA 'public';

-- 1. Insérer des données dans `_image`
INSERT INTO _image (pathImage) VALUES 
('path/to/image1.jpg'),
('path/to/image2.jpg'),
('path/to/image3.jpg');

-- 2. Insérer des données dans `_compte`
INSERT INTO _compte (nomCompte, prenomCompte, mailCompte, numTelCompte, idImagePdp, hashMdpCompte, dateCreationCompte, dateDerniereConnexionCompte) VALUES 
('Smith', 'John', 'john.smith@example.com', '0123456789', 1, '$2y$10$RkM09lrLhpt74shzr/w0Euihc4LraI0K2fSg3WNbzoDsbg7kFKsC6', '2023-01-15', '2025-01-15'),
('Doe', 'Jane', 'jane.doe@example.com', '0987654321', 2, '$2y$10$R0AEBas/G8eyQM3XWdG.Ie0knRnf1yr4M22WIImwKkxH1IX4grwzu', '2023-02-20', '2025-02-20');

-- 3. Insérer des données dans `_adresse`
INSERT INTO _adresse (numRue, supplementAdresse, adresse, codePostal, ville, departement, pays) VALUES 
(12, 'Apt 5', 'Rue de la Paix', 75000, 'Paris', 'Ile-de-France', 'France'),
(21, '', 'Avenue des Champs-Elysées', 75008, 'Paris', 'Ile-de-France', 'France');

-- 4. Insérer des données dans `_professionnel`
INSERT INTO _professionnel (idCompte, denominationPro, numSirenPro) VALUES 
(1, 'Tech Solutions', '123456789'),
(2, 'Design Experts', '987654321');

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
(1, 1, 'Offre de service 1', 'Résumé 1', 'Description de l offre 1', 100.0, TRUE, FALSE, 1, 'https://ilovemyself.com',4.5, FALSE, '2023-05-01', 'Accessible', FALSE),
(2, 2, 'Offre de service 2', 'Résumé 2', 'Description de l offre 2', 200.0, FALSE, TRUE, 2, 'https://pnevot.com',4.0, TRUE, '2023-06-01', 'Non accessible', TRUE);

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
('Vegan', TRUE),
('Végétarien', TRUE);

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
