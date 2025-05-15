Décrire le schéma relationnel de la base de données et définir les relations entre les tables.

USE timeless;

-- Table UTILISATEUR
CREATE TABLE IF NOT EXISTS UTILISATEUR (
    idutilisateur INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    motdepasse VARCHAR(10) NOT NULL,  
    datedenaissance DATE,
    numeroTel VARCHAR(20) NOT NULL,
    adresse VARCHAR(50) NOT NULL,
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table MONTRE
CREATE TABLE IF NOT EXISTS MONTRE (
    idmontre INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(100) NOT NULL,
    description TEXT,
    prix DECIMAL(10, 2) NOT NULL,
    couleur VARCHAR(50),
    categorie VARCHAR(50),
    sexe ENUM('HOMME', 'FEMME', 'MIX') NOT NULL ,
    pathImage VARCHAR(255),             
    quantiteStock INT DEFAULT 0,      
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table COMMANDE
CREATE TABLE IF NOT EXISTS COMMANDE (
    idcommande INT AUTO_INCREMENT PRIMARY KEY,
    idutilisateur INT NOT NULL,
    datecommande DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('en attente', 'faite', 'annulée') DEFAULT 'en attente',
    FOREIGN KEY (idutilisateur) REFERENCES UTILISATEUR(idutilisateur)
) ENGINE=InnoDB;

-- Table COMMANDE_DETAIL
CREATE TABLE IF NOT EXISTS COMMANDE_DETAIL (
    id_detail INT AUTO_INCREMENT PRIMARY KEY,
    idcommande INT NOT NULL,
    idmontre INT NOT NULL,
    quantite INT NOT NULL,
    prixunitaire DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (idcommande) REFERENCES COMMANDE(idcommande),
    FOREIGN KEY (idmontre) REFERENCES MONTRE(idmontre)
) ENGINE=InnoDB;

-- Table HISTORIQUE_COMMANDES (pour les annulations)
CREATE TABLE IF NOT EXISTS HISTORIQUE_COMMANDES (
    idhistorique INT AUTO_INCREMENT PRIMARY KEY,
    idcommande INT NOT NULL,
    idutilisateur INT NOT NULL,
    dateannulation DATETIME DEFAULT CURRENT_TIMESTAMP,
    raison TEXT,
    FOREIGN KEY (idcommande) REFERENCES COMMANDE(idcommande),
    FOREIGN KEY (idutilisateur) REFERENCES UTILISATEUR(idutilisateur)
) ENGINE=InnoDB;

-- Table PANIER (temporaire)
CREATE TABLE IF NOT EXISTS PANIER (
    idpanier INT AUTO_INCREMENT PRIMARY KEY,
    idutilisateur INT NOT NULL,
    idmontre INT NOT NULL,
    quantite INT NOT NULL DEFAULT 1,
    FOREIGN KEY (idutilisateur) REFERENCES UTILISATEUR(idutilisateur),
    FOREIGN KEY (idmontre) REFERENCES MONTRE(idmontre),
    UNIQUE KEY (idutilisateur, idmontre)  -- Un seul article du même type par utilisateur
) ENGINE=InnoDB;



-- Montre 1
INSERT INTO MONTRE (idmontre, titre, description, prix, couleur, categorie, sexe, quantiteStock, pathImage)
VALUES (1, 'Rolex Air-King Precision', 'Montre élégante Rolex Air-King avec cadran noir, boîtier en acier inoxydable et bracelet Oyster. Mouvement automatique de haute précision. Parfaite pour les occasions formelles.', 20000, 'Argent/Noir', 'Classique', 'HOMME', 5, 'montre1.jpg');

-- Montre 2
INSERT INTO MONTRE (idmontre, titre, description, prix, couleur, categorie, sexe, quantiteStock, pathImage)
VALUES (2, 'Omega Speedmaster Chronographe Automatique', 'Chronographe sportif avec cadran noir, tachymètre sur la lunette et trois sous-cadrans. Bracelet en acier inoxydable. Montre emblématique reconnue pour sa fiabilité et son design intemporel.', 18000, 'Argent/Noir', 'Sport', 'HOMME', 10, 'montre2.jpg');

-- Montre 3
INSERT INTO MONTRE (idmontre, titre, description, prix, couleur, categorie, sexe, quantiteStock, pathImage)
VALUES (3, 'Rolex Air-King Precision Bracelet Cuir', 'Version élégante de l''Air-King avec cadran argenté et bracelet en cuir beige. Allie le prestige Rolex à un style décontracté chic parfait pour le quotidien.', 4800, 'Argent/Beige', 'Classique', 'FEMME', 17, 'montre3.jpg');

-- Montre 4
INSERT INTO MONTRE (idmontre, titre, description, prix, couleur, categorie, sexe, quantiteStock, pathImage)
VALUES (4, 'Rolex Datejust 36 Acier', 'Montre Rolex Datejust avec cadran argenté, guichet de date à 3h et lunette cannelée. Bracelet Oyster en acier inoxydable. Symbole d''élégance intemporelle.', 20000, 'Argent', 'Luxe', 'FEMME', 11, 'montre4.jpg');

-- Montre 5
INSERT INTO MONTRE (idmontre, titre, description, prix, couleur, categorie, sexe, quantiteStock, pathImage)
VALUES (5, 'Cartier Must 21', 'Montre femme Must de Cartier avec cadran blanc, chiffres romains et aiguilles bleues. Boîtier doré et bracelet en cuir bleu foncé. Alliance parfaite entre tradition horlogère et design contemporain.', 3500, 'Or/Bleu', 'Luxe', 'FEMME', 13, 'montre5.jpg');

-- Montre 6
INSERT INTO MONTRE (idmontre, titre, description, prix, couleur, categorie, sexe, quantiteStock, pathImage)
VALUES (6, 'Panerai Radiomir Black Seal', 'Montre Panerai Radiomir avec boîtier coussin en acier, cadran noir minimaliste et bracelet en cuir noir. Design italien distinctif avec une excellente lisibilité et présence au poignet.', 14000, 'Noir/Argent', 'Sport', 'HOMME', 6, 'montre6.jpg');

-- Montre 7
INSERT INTO MONTRE (idmontre, titre, description, prix, couleur, categorie, sexe, quantiteStock, pathImage)
VALUES (7, 'Rolex Oyster Perpetual 36 Cadran Bleu', 'Montre Rolex Oyster Perpetual avec cadran bleu vif et bracelet Oyster en acier. Mouvement automatique certifié Chronomètre. Le parfait équilibre entre sportivité et élégance.', 9000, 'Argent/Bleu', 'Classique', 'MIX', 10, 'montre7.jpg');

-- Montre 8
INSERT INTO MONTRE (idmontre, titre, description, prix, couleur, categorie, sexe, quantiteStock, pathImage)
VALUES (8, 'IWC Ingenieur Automatique', 'Montre IWC Ingenieur avec cadran argenté, index luminescents et affichage de la date. Boîtier en acier et bracelet en cuir noir. Alliance de précision suisse et d''élégance contemporaine.', 12000, 'classique', 'Luxe', 'FEMME', 8, 'montre8.jpg');

-- Montre 9
INSERT INTO MONTRE (idmontre, titre, description, prix, couleur, categorie, sexe, quantiteStock, pathImage)
VALUES (9, 'Cartier Pasha Automatique - Montre Élégante', 'Montre Cartier Pasha avec boîtier rond en acier, cadran noir avec chiffres arabes et bracelet en acier. Design distinctif avec couronne cannelée à cabochon. Symbole de sophistication.', 19000, 'Argent/Noir', 'Luxe', 'MIX', 3, 'montre9.jpg');

-- Montre 10
INSERT INTO MONTRE (idmontre, titre, description, prix, couleur, categorie, sexe, quantiteStock, pathImage)
VALUES (10, 'Poiray Ma Première - Montre Femme Rose', 'Montre rectangulaire Poiray Ma Première avec cadran nacré rose et boîtier en acier. Bracelet en cuir rose. Design élégant et raffiné, parfait pour les occasions spéciales.', 7900, 'Rose/Argent', 'classique', 'FEMME', 20, 'montre10.jpg');

-- Montre 11
INSERT INTO MONTRE (idmontre, titre, description, prix, couleur, categorie, sexe, quantiteStock, pathImage)
VALUES (11, 'Rolex Day-Date President Or', 'Prestigieuse Rolex Day-Date en or jaune 18 carats avec cadran champagne et bracelet President. Affichage du jour et de la date. Le summum du luxe horloger.', 30000, 'Or', 'Luxe', 'FEMME', 5, 'montre11.jpg');


Écrire une procédure stockée qui affiche les détails d’une commande pour un client ainsi que le total à payer.

DELIMITER //

CREATE PROCEDURE GetCompleteOrderDetails(IN p_order_id INT, IN p_user_id INT)
BEGIN
    -- Déclarer les variables pour le total
    DECLARE v_total DECIMAL(10,2) DEFAULT 0;
    
    -- 1. Vérifier que la commande appartient bien au client
    IF NOT EXISTS (
        SELECT 1 FROM COMMANDE 
        WHERE idcommande = p_order_id 
        AND idutilisateur = p_user_id
    ) THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Commande introuvable ou non autorisée';
    ELSE
        -- 2. Créer une table temporaire pour les résultats
        DROP TEMPORARY TABLE IF EXISTS temp_order_details;
        CREATE TEMPORARY TABLE temp_order_details (
            section VARCHAR(20),
            idcommande INT,
            date_commande DATETIME,
            statut VARCHAR(20),
            client VARCHAR(100),
            email VARCHAR(100),
            article VARCHAR(100),
            quantite INT,
            prix_unitaire DECIMAL(10,2),
            sous_total DECIMAL(10,2),
            image VARCHAR(255),
            total DECIMAL(10,2)
        );
        
       -- 3. Insérer les informations de la commande
        INSERT INTO temp_order_details (section, idcommande, date_commande, statut, client, email)
        SELECT 
            'header',
            c.idcommande,
            c.datecommande,
            COALESCE(c.statut, 'en attente') AS statut,
            CONCAT(u.prenom, ' ', u.nom),
            u.email
        FROM 
            COMMANDE c 
        JOIN 
            UTILISATEUR u ON c.idutilisateur = u.idutilisateur
        WHERE 
            c.idcommande = p_order_id;
        
        -- 4. Insérer les détails des articles
        INSERT INTO temp_order_details (section, article, quantite, prix_unitaire, sous_total, image)
        SELECT 
            'detail',
            m.titre,
            cd.quantite,
            cd.prixunitaire,
            (cd.quantite * cd.prixunitaire),
            m.pathImage
        FROM 
            COMMANDE_DETAIL cd
        JOIN 
            montre m ON cd.idmontre = m.idmontre
        WHERE 
            cd.idcommande = p_order_id;
        
        -- 5. Calculer et insérer le total
        SELECT SUM(quantite * prix_unitaire) INTO v_total
        FROM temp_order_details 
        WHERE section = 'detail';
        
        INSERT INTO temp_order_details (section, total)
        VALUES ('total', v_total);
        
        -- 6. Retourner les résultats
        SELECT * FROM temp_order_details;
    END IF;
END //

DELIMITER ;

Proposer une procédure stockée qui permet de finaliser une commande et de vider le panier une fois la commande validée.


 DELIMITER //

CREATE  PROCEDURE FinaliserCommandeEtViderPanier(IN p_user_id INT, IN p_order_id INT)
BEGIN
    DECLARE v_cart_count INT;
    
    -- Vérifier que la commande appartient bien à l'utilisateur
    IF NOT EXISTS (
        SELECT 1 FROM COMMANDE 
        WHERE idcommande = p_order_id 
        AND idutilisateur = p_user_id
    ) THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Commande introuvable ou non autorisée';
    END IF;
    
    -- Vérifier que le panier n'est pas déjà vide
    SELECT COUNT(*) INTO v_cart_count
    FROM panier
    WHERE idutilisateur = p_user_id;
    
    IF v_cart_count > 0 THEN
        -- Vider le panier
        DELETE FROM panier 
        WHERE idutilisateur = p_user_id;
    END IF;
    
    -- Mettre à jour le statut de la commande
    UPDATE COMMANDE
    SET statut = 'validée'
    WHERE idcommande = p_order_id;
END //

DELIMITER ;



Écrire un trigger qui empêche l’insertion d’une commande si la quantité demandée dépasse le stock disponible.

 DELIMITER //

CREATE TRIGGER before_commande_detail_insert
BEFORE INSERT ON COMMANDE_DETAIL
FOR EACH ROW
BEGIN
    DECLARE stock_actuel INT;
    DECLARE message_erreur VARCHAR(255);
    
    -- Vérification du stock disponible
    SELECT quantiteStock INTO stock_actuel
    FROM MONTRE
    WHERE idmontre = NEW.idmontre;
    
    -- Bloquer si stock insuffisant
    IF NEW.quantite > stock_actuel THEN
        SET message_erreur = CONCAT('Stock insuffisant pour la montre ID ', NEW.idmontre, '. Disponible: ', stock_actuel);
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = message_erreur;
    END IF;
END//

DELIMITER ;


Écrire une procédure qui permet d'afficher l'historique des commandes d'un client.

DELIMITER //

CREATE PROCEDURE GetOrderHistory(IN p_user_id INT)
BEGIN
    SELECT 
        c.idcommande,
        c.datecommande,
        c.statut,
        COUNT(cd.id_detail) AS nombre_articles,
        SUM(cd.quantite * cd.prixunitaire) AS montant_total
    FROM 
        COMMANDE c
    LEFT JOIN 
        COMMANDE_DETAIL cd ON c.idcommande = cd.idcommande
    WHERE 
        c.idutilisateur = p_user_id
    GROUP BY 
        c.idcommande, c.datecommande, c.statut
    ORDER BY 
        c.datecommande DESC;
END //

DELIMITER ;


*Écrire un trigger qui met automatiquement à jour le stock d’un item après la validation d’une commande.**


DELIMITER //

CREATE TRIGGER after_commande_validation
AFTER UPDATE ON COMMANDE
FOR EACH ROW
BEGIN
    -- Vérifier si le statut est passé à 'faite' (validée)
    IF NEW.statut = 'faite' AND OLD.statut = 'en attente' THEN
        -- Mettre à jour le stock pour chaque article de la commande
        UPDATE MONTRE m
        JOIN COMMANDE_DETAIL cd ON m.idmontre = cd.idmontre
        SET m.quantiteStock = m.quantiteStock - cd.quantite
        WHERE cd.idcommande = NEW.idcommande;
    END IF;
END //

DELIMITER ;


Écrire un trigger qui permet de restaurer le stock après l'annulation d'une commande

DELIMITER //

CREATE TRIGGER after_commande_annulation
AFTER UPDATE ON COMMANDE
FOR EACH ROW
BEGIN
    -- Vérifier si le statut est passé à 'annulée'
    IF NEW.statut = 'annulée' AND OLD.statut != 'annulée' THEN
        -- Restaurer le stock pour chaque article de la commande
        UPDATE MONTRE m
        JOIN COMMANDE_DETAIL cd ON m.idmontre = cd.idmontre
        SET m.quantiteStock = m.quantiteStock + cd.quantite
        WHERE cd.idcommande = NEW.idcommande;
    END IF;
END //

DELIMITER ;

Écrire un trigger qui permet de garder trace des commandes annulées dans une table historique qu'il faudra créer. 

DELIMITER //

CREATE TRIGGER after_commande_cancelled
AFTER UPDATE ON COMMANDE
FOR EACH ROW
BEGIN
    -- Vérifier si le statut est passé à 'annulée'
    IF NEW.statut = 'annulée' AND OLD.statut != 'annulée' THEN
        -- Ajouter un enregistrement dans l'historique des commandes
        INSERT INTO HISTORIQUE_COMMANDES (idcommande, idutilisateur, raison)
        VALUES (NEW.idcommande, NEW.idutilisateur, 'Commande annulée par le client');
    END IF;
END //

DELIMITER ;