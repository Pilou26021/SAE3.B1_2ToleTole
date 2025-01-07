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
('./img/uploaded/image11.png'),
('./img/uploaded/image12.png'),
('./img/uploaded/image13.png'),
('./img/uploaded/image14.png'), --Jose
('./img/uploaded/image15.png'),  --Image Pdp par défaut
('./img/uploaded/image16.png'),
('./img/uploaded/image17.png'),
('./img/uploaded/image18.png'),
('./img/uploaded/image19.png'),
('./img/uploaded/image20.png'),
('./img/uploaded/image21.png'); -- carte parc d'attraction Le Village Gaulois

-- 2. Insérer des données dans `_adresse`
INSERT INTO _adresse (numRue, supplementAdresse, adresse, codePostal, ville, departement, pays) VALUES 
(17, '', 'Rue de Trolay', 22700, 'Perros-Guirec', 'Bretagne', 'France'), -- 1
(5, '', 'Place du Roi Saint-Judicael', 35380, 'Paimpont', 'Bretagne', 'France'), -- 2
(4, '', 'Rue Edouard Branly', 22300, 'Lannion', 'Bretagne', 'France'), -- 3
(21, 'Appartement 3B', 'Rue de la Liberté', 75000, 'Paris', 'Ile-de-France', 'France'), -- 4
(12, '', 'Avenue des Champs-Élysées', 75008, 'Paris', 'Ile-de-France', 'France'), -- 5
(3, '', 'Rue de la République', 69001, 'Lyon', 'Auvergne-Rhône-Alpes', 'France'), -- 6
(7, '', 'Rue de la Paix', 69001, 'Lyon', 'Auvergne-Rhône-Alpes', 'France'), -- 7
(35, '', 'Boulevard Haussmann', 75009, 'Paris', 'Ile-de-France', 'France'), -- 8
(2, '', 'Rue de la République', 69002, 'Lyon', 'Auvergne-Rhône-Alpes', 'France'), -- 9
(9, '', 'Rue de Rivoli', 75001, 'Paris', 'Ile-de-France', 'France'), -- 10
(1, '', 'Lloret de Mar', 17310, 'Lloret de Mar', 'Catalogne', 'Espagne'), -- 11
(95, '', ' Rue du Palais', 93800, 'Épinay-sur-seine', 'Île-de-France', 'France'), -- 12
(10, '', 'Rue de la Gare', 35000, 'Rennes', 'Bretagne', 'France'), -- 13
(15, '', 'Avenue Jean Jaurès', 69007, 'Lyon', 'Auvergne-Rhône-Alpes', 'France'), -- 14
(22, '', 'Boulevard de la République', 13001, 'Marseille', 'Provence-Alpes-Côte d''Azur', 'France'), -- 15
(8, '', 'Rue de la Paix', 75002, 'Paris', 'Ile-de-France', 'France'), -- 16
(14, '', 'Rue de la Liberté', 21000, 'Dijon', 'Bourgogne-Franche-Comté', 'France'), -- 17
(30, '', 'Rue Nationale', 37000, 'Tours', 'Centre-Val de Loire', 'France'), -- 18
(32, '', 'Rue Nationale', 37000, 'Tours', 'Centre-Val de Loire', 'France'); -- 19

-- 3. Insérer des données dans `_compte`
INSERT INTO _compte (nomCompte, prenomCompte, mailCompte, numTelCompte, idImagePdp, hashMdpCompte, idAdresse, dateCreationCompte, dateDerniereConnexionCompte) VALUES 
('Smith', 'John', 'john.smith@example.com', '0123456789', 3, '$2y$10$RkM09lrLhpt74shzr/w0Euihc4LraI0K2fSg3WNbzoDsbg7kFKsC6', 12, '2023-01-15', '2025-01-15'),
('Le Verge', 'Lou', 'lou.leverge@example.com', '0123456789', 6, '$2y$10$RkM09lrLhpt74shzr/w0Euihc4LraI0K2fSg3WNbzoDsbg7kFKsC6', 13, '2023-01-15', '2025-01-15'),
('Denis', 'Liam', 'liamdenis35@gmail.com', '0987654321', 7, '$2y$10$RkM09lrLhpt74shzr/w0Euihc4LraI0K2fSg3WNbzoDsbg7kFKsC6', 14, '2023-02-20', '2025-02-20'),
('Smath', 'Johnny', 'johnny.smath@example.com', '0123456789', 20, '$2y$10$RkM09lrLhpt74shzr/w0Euihc4LraI0K2fSg3WNbzoDsbg7kFKsC6', 15, '2023-01-15', '2025-01-15'),
('Mallet', 'Piel', 'piel.mallet@example.com', '0123456789', 8, '$2y$10$RkM09lrLhpt74shzr/w0Euihc4LraI0K2fSg3WNbzoDsbg7kFKsC6', 16, '2023-01-15', '2025-01-15'),
('Doe', 'Jane', 'jane.doe@example.com', '0987654321', 9, '$2y$10$R0AEBas/G8eyQM3XWdG.Ie0knRnf1yr4M22WIImwKkxH1IX4grwzu', 17, '2023-02-20', '2025-02-20'),
('Buss', 'Gary ', 'gary.buss@example.com', '3015138427', 10, '$2y$10$RkM09lrLhpt74shzr/w0Euihc4LraI0K2fSg3WNbzoDsbg7kFKsC6', 18, '2023-01-15', '2025-01-15'),
('Laberge', 'Jose ', 'jose.laberge@example.com', '5308287564', 11, '$2y$10$RkM09lrLhpt74shzr/w0Euihc4LraI0K2fSg3WNbzoDsbg7kFKsC6', 19, '2023-01-15', '2025-01-15');

-- 4. Insérer des données dans `_professionnel`
INSERT INTO _professionnel (idCompte, denominationPro, numSirenPro) VALUES 
(1, 'SARL - Tech Solutions', '123456789'),
(2, 'SARL - Design Experts', '987654321'),
(3, 'SARL - LIAM CO', '983455432');

-- 5. Insérer des données dans `_membre`
INSERT INTO _membre (idCompte, pseudonyme) VALUES 
(4, 'JohnnyHally'), -- 1
(5, 'Piel'), -- 2
(6, 'Janess'), -- 3
(7, 'GaryBuss'), -- 4
(8, 'Josetto'); -- 5

-- 6. Insérer des données dans `_professionnelPublic`
INSERT INTO _professionnelPublic (idPro) VALUES 
(3);

-- 7. Insérer des données dans `_professionnelPrive`
INSERT INTO _professionnelPrive (idPro, coordBancairesIBAN, coordBancairesBIC) VALUES 
(1, 'FR7630006000011234567890189', 'BIC12345XXX'),
(2, 'FR7630006000019876543210987', 'BIC54321XXX');

-- 8. Insérer des données dans `_offre`
INSERT INTO _offre (idProPropose, idAdresse, titreOffre, resumeOffre, descriptionOffre, prixMinOffre, aLaUneOffre, enReliefOffre, typeOffre, siteWebOffre, noteMoyenneOffre, commentaireBlacklistable, dateCreationOffre, conditionAccessibilite, horsLigne) VALUES 
(3, 1, 'Côtes de Granit Rose', 'Visiter les magnifiques cotes de granit rose', 'Description de l offre 1', 15, TRUE, FALSE, 0, 'https://ilovemyself.com',0, FALSE, '2023-05-01', 'Accessible', FALSE),
(2, 2, 'Forêt de Brocéliande', 'Le celebre Jardin de Broceliande vous attend', 'Description de l offre 2', 10, TRUE, TRUE, 2, 'https://pnevot.com',0, TRUE, '2023-06-01', 'Non accessible', FALSE),
(1, 3, 'Restaurant Universitaire', 'Venez déguster nos plats', 'Ici au RU, on vous propose des plats variés et équilibrés', 1, FALSE, FALSE, 1, 'https://www.crous-rennes.fr/restaurant/resto-u-branly-3/', 1.0, FALSE, '2023-06-01', 'Accessible', FALSE),
(3, 4, 'Petit-déjeuner Gourmand', 'Savourez des viennoiseries fraîches', 'Une offre spéciale pour les amateurs de pâtisseries.', 12, TRUE, FALSE, 0, 'https://boulangerie.example.com', 0, FALSE, '2023-07-10', 'Accessible', FALSE),
(3, 1, 'Archipel de Bréhat en kayak', 'Découvrez l archipel en kayak', 'Les sorties sont limitées entre 15 et 20 km, adaptées aux familles avec pauses régulières. Accessibilité pour les personnes en situation de handicap.', 0, TRUE, FALSE, 0, 'https://planete-kayak.example.com', 0, FALSE, '2023-05-01', 'Accessible', FALSE),
(2, 2, 'Balade familiale à vélo', 'Balade à vélo dans le Trégor', 'Une sortie sur de petites routes tranquilles, adaptée aux enfants à partir de 6 ans. Équipement requis pour les plus jeunes.', 0, TRUE, TRUE, 0, 'https://tregor-bicyclette.example.com', 0, FALSE, '2023-06-01', 'Accessible', FALSE),
(1, 3, 'Découverte des Sept-Îles', 'Excursion vers les Sept-Îles', 'Découvrez la plus grande réserve ornithologique de France avec une visite guidée en bateau.', 15, TRUE, TRUE, 0, 'https://armor-navigation.example.com', 0, FALSE, '2023-06-15', 'Accessible avec fauteuil manuel', FALSE),
(3, 4, 'La Magie des arbres', 'Un spectacle son et lumière', 'Un festival unique mêlant musique, lumière, et effets pyrotechniques sur la Côte de Granit Rose.', 5, TRUE, FALSE, 1, 'https://magie-des-arbres.example.com', 0, FALSE, '2023-07-26', 'Accessible', FALSE),
(2, 5, 'Le Village Gaulois', 'Découvrez l histoire Gauloise', 'Un parc d attractions immersif recréant la vie à l époque des Gaulois.', 10, FALSE, TRUE, 2, 'https://village-gaulois.example.com', 0, TRUE, '2023-08-01', 'Accessible', FALSE),
(3, 6, 'La Ville Blanche', 'Une expérience gastronomique', 'Un restaurant au cœur du Trégor offrant des plats raffinés dans un cadre historique.', 50, FALSE, TRUE, 1, 'https://la-ville-blanche.example.com', 4.5, FALSE, '2023-09-15', 'Accessible', FALSE);

-- 9. Insérer des données dans `_avis`
INSERT INTO _avis (idOffre, noteAvis, commentaireAvis, idMembre, dateAvis, dateVisiteAvis, blacklistAvis, reponsePro, scorePouce) VALUES 
(1, 5, 'Excellente offre!', 1, '2023-05-15', '2023-05-10', FALSE, TRUE, 0),
(2, 3, 'La piste pourrait être mieux indiquée', 4, '2023-06-15', '2023-06-10', FALSE, FALSE, 0),
(2, 4, 'Super balade en famille !', 2, '2023-06-15', '2023-06-10', FALSE, FALSE, 0),
(3, 4, 'Bonne offre! J''y retournerais sans problème !', 4, '2023-05-15', '2023-05-10', FALSE, FALSE, 0),
(3, 3, 'Les repas sont peu cher mais le choix laisse à désirer.', 3, '2023-04-23', '2023-04-23', FALSE, FALSE, 0),
(3, 2, 'Pas encore ouvert :''(', 2, '1955-11-11', '1955-11-11', FALSE, FALSE, 0),
(3, 4, 'Personnel professionnel et sympathique !', 5, '2022-09-12', '2022-09-12', FALSE, FALSE, 0),
(4, 5, 'Bien gourmand 😋', 2, '2024-12-02', '2024-12-01', FALSE, FALSE, 0),
(10, 2, 'Elastique un peu trop détendu...', 5, '2024-12-02', '2024-12-01', FALSE, FALSE, 0),
(10, 5, 'Professionnel et sécurité au top !', 4, '2024-12-02', '2024-12-01', FALSE, FALSE, 0),
(9, 4, 'Super oeuvres d''art', 4, '2024-12-02', '2024-12-01', FALSE, FALSE, 0);

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
(5, 3),
(13, 4),
(14, 5),
(12, 6),
(16, 7),
(17, 8),
(18, 9),
(19, 10);

-- 15. Insérer des données dans `_imageImageAvis`
INSERT INTO _imageImageAvis (idImage, idAvis) VALUES 
(1, 1),
(2, 2);

-- 16. Insérer des données dans `_offreActivite`
INSERT INTO _offreActivite (idOffre, indicationDuree, ageMinimum, prestationIncluse) VALUES 
(1, 2, 12, 'Guide inclus'),
(2, 3, 10, 'Collation incluse'),
(5, 2, 12, 'Guide inclus'),
(6, 2, 12, 'Guide inclus'),
(7, 2, 12, 'Guide inclus'),
(8, 2, 12, 'Guide inclus'),
(9, 2, 12, 'Guide inclus'),
(10, 2, 12, 'Guide inclus');

-- 17. Insérer des données dans `_offreSpectacle`
INSERT INTO _offreSpectacle (idOffre, dateOffre, indicationDuree, capaciteAcceuil) VALUES 
(8, '2025-07-26', 2, 500);

-- 18. Insérer des données dans `_offreParcAttraction`
INSERT INTO _offreParcAttraction (idOffre, dateOuverture, dateFermeture, carteParc, nbrAttraction, ageMinimum) VALUES 
(9, '2025-07-01', '2025-08-31', 21, 15, 6);

-- 19. Insérer des données dans `_offreVisite`
INSERT INTO _offreVisite (idOffre, dateOffre, visiteGuidee, langueProposees) VALUES 
(7, '2025-06-15', TRUE, '["français", "anglais"]');

-- 20. Insérer des données dans `_offreRestaurant`
INSERT INTO _offreRestaurant (idOffre, horaireSemaine, gammePrix, carteResto) VALUES 
(3, '{"lunchOpen":"11:30","lunchClose":"13:30","dinnerOpen":"00:00","dinnerClose":"00:00"}', 1, '4'),
(4, '{"lunchOpen":"11:30","lunchClose":"13:30","dinnerOpen":"00:00","dinnerClose":"00:00"}', 1, '4');

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

-- 23. Insérer des données dans `_constPrix`
INSERT INTO _constPrix (dateTarif, prixSTDht, prixSTDttc, prixPREMht, prixPREMttc, prixALaUneht, prixALaUnettc, prixEnReliefht, prixEnReliefttc) VALUES 
('2024-11-25', 1.67, 2.0, 3.34, 4.0, 16.68, 20.0, 8.34, 10.0);

