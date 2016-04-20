DROP DATABASE IF EXISTS ifocop_lokisalle ;

CREATE DATABASE ifocop_lokisalle ;

USE ifocop_lokisalle ;


CREATE TABLE membre (
    id_membre       int(5) unsigned NOT NULL AUTO_INCREMENT,
    pseudo          varchar(15) UNIQUE NOT NULL,
    password        varchar(60) NOT NULL,
    email           varchar(30) NOT NULL,
    prenom          varchar(20) NOT NULL,
    nom             varchar(20) NOT NULL,
    sexe            enum('h','f') NOT NULL,
    adresse         varchar(30) NOT NULL,
    ville           varchar(30) NOT NULL,
    cp              varchar(5) NOT NULL,
    pays            varchar(20) NOT NULL,
    statut          int(1) NOT NULL,
    newsletter      int(1) NOT NULL,
    PRIMARY KEY (id_membre)
) ENGINE=InnoDb CHARACTER SET=utf8 ;


CREATE TABLE salle (
    id_salle        int(5) unsigned NOT NULL AUTO_INCREMENT,
    titre           varchar(200) NOT NULL,
    adresse         varchar(30) NOT NULL,
    ville           varchar(30) NOT NULL,
    cp              varchar(5) NOT NULL,
    pays            varchar(20) NOT NULL,
    latitude        float NOT NULL,
    longitude       float NOT NULL,
    categorie       enum('Réunion', 'Multimédia', 'Fête'),
    capacite        int(3) unsigned NOT NULL,
    photo           varchar(200) DEFAULT NULL,
    description     text NOT NULL,
    PRIMARY KEY (id_salle)
) ENGINE=InnoDb CHARACTER SET=utf8 ;


CREATE TABLE avis (
    id_avis         int(5) unsigned NOT NULL AUTO_INCREMENT,
    id_membre       int(5) unsigned DEFAULT NULL,
    id_salle        int(5) unsigned DEFAULT NULL,
    commentaire     text DEFAULT NULL,
    note            int(2) unsigned DEFAULT NULL,
    date            datetime DEFAULT NULL,
    PRIMARY KEY (id_avis)
) ENGINE=InnoDb CHARACTER SET=utf8 ;


CREATE TABLE commande (
    id_commande     int(6) unsigned NOT NULL AUTO_INCREMENT,
    montant         int(5) NOT NULL,
    id_membre       int(5) unsigned DEFAULT NULL,
    date            datetime NOT NULL,
    PRIMARY KEY (id_commande)
) ENGINE=InnoDb CHARACTER SET=utf8 ;


CREATE TABLE produit (
    id_produit      int(5) unsigned NOT NULL AUTO_INCREMENT,
    date_arrivee    date NOT NULL,
    date_depart     date NOT NULL,
    id_salle        int(5) unsigned NOT NULL,
    id_promo        int(2) unsigned DEFAULT NULL,
    prix            int(5) unsigned NOT NULL,
    etat            int(1) unsigned NOT NULL,
    PRIMARY KEY (id_produit)
) ENGINE=InnoDb CHARACTER SET=utf8 ;


CREATE TABLE promotion (
    id_promo        int(2) unsigned NOT NULL AUTO_INCREMENT,
    code_promo      varchar(6) NOT NULL,
    reduction       int(5) unsigned NOT NULL,
    PRIMARY KEY (id_promo)
) ENGINE=InnoDb CHARACTER SET=utf8 ;


CREATE TABLE details_commande (
    id_details_commande     int(6) unsigned NOT NULL AUTO_INCREMENT,
    id_commande             int(6) unsigned NOT NULL,
    id_produit              int(5) unsigned NOT NULL,
    PRIMARY KEY (id_details_commande)
) ENGINE=InnoDb CHARACTER SET=utf8 ;


CREATE TABLE newsletter (
    id_newsletter   int(5) unsigned NOT NULL AUTO_INCREMENT,
    sujet           VARCHAR(255),
    message         text NOT NULL,
    date_news       datetime NOT NULL,
    PRIMARY KEY (id_newsletter)
) ENGINE=InnoDb CHARACTER SET=utf8 ;



ALTER TABLE avis
ADD CONSTRAINT fk_avis_membre
    FOREIGN KEY (id_membre) REFERENCES membre (id_membre)
    ON DELETE SET NULL
    ON UPDATE CASCADE ,
ADD CONSTRAINT fk_avis_salle
    FOREIGN KEY (id_salle) REFERENCES salle (id_salle)
    ON DELETE SET NULL
    ON UPDATE CASCADE ;


ALTER TABLE commande
ADD CONSTRAINT fk_commande_membre
    FOREIGN KEY (id_membre) REFERENCES membre (id_membre)
    ON DELETE SET NULL
    ON UPDATE CASCADE ;


ALTER TABLE produit
ADD CONSTRAINT fk_produit_salle
    FOREIGN KEY (id_salle) REFERENCES salle (id_salle)
    ON DELETE CASCADE
    ON UPDATE CASCADE ,
ADD CONSTRAINT fk_produit_promo
    FOREIGN KEY (id_promo) REFERENCES promotion (id_promo)
    ON DELETE SET NULL
    ON UPDATE CASCADE ;


ALTER TABLE details_commande
ADD CONSTRAINT fk_details_commande
    FOREIGN KEY (id_commande) REFERENCES commande (id_commande)
    ON DELETE CASCADE
    ON UPDATE CASCADE ,
ADD CONSTRAINT fk_details_produit
    FOREIGN KEY (id_produit) REFERENCES produit (id_produit)
    ON DELETE CASCADE
    ON UPDATE CASCADE ;




INSERT INTO salle
(id_salle, titre, adresse, ville, cp, pays, latitude, longitude, categorie, capacite, photo, description)
VALUES
(1, 'Rolland', '100 Avenue des Champs Elysées', 'Paris', '75008', 'France', 48.8721, 2.30264, 'Réunion', 50, 'images/salles/001_salle_rolland.jpg', 'nec tellus. Nunc lectus pede, ultrices a, auctor non, feugiat nec, diam. Duis mi enim, condimentum eget, volutpat ornare, facilisis eget, ipsum. Donec sollicitudin adipiscing ligula. Aenean gravida nunc sed pede. Cum sociis natoque penatibus et'),
(2, 'Lefebvre', '30 Boulevard de Sébastopol', 'Paris', '75001', 'France', 48.8613, 2.34982, 'Multimédia', 20, 'images/salles/002_salle_lefebvre.jpg', 'malesuada fames ac turpis egestas. Aliquam fringilla cursus purus. Nullam scelerisque neque sed sem egestas blandit. Nam nulla magna, malesuada vel, convallis in, cursus et, eros. Proin ultrices. Duis volutpat nunc sit'),
(3, 'Bouvier', '25 Avenue Foch', 'Paris', '75116', 'France', 48.8731, 2.28787, 'Fête', 100, 'images/salles/003_salle_bouvier.jpg', 'metus vitae velit egestas lacinia. Sed congue, elit sed consequat auctor, nunc nulla vulputate dui, nec tempus mauris erat eget ipsum. Suspendisse sagittis. Nullam vitae diam. Proin dolor. Nulla semper tellus id nunc interdum feugiat. Sed nec metus'),
(4, 'Renault', '165 rue Garibaldi', 'Lyon', '69003', 'France', 45.7597, 4.85239, 'Réunion', 40, 'images/salles/004_salle_renault.jpg', 'ut cursus luctus, ipsum leo elementum sem, vitae aliquam eros turpis non enim. Mauris quis turpis vitae purus gravida sagittis. Duis gravida. Praesent eu nulla at sem molestie'),
(5, 'Blanc', '146 Boulevard de Paris', 'Marseille', '13003', 'France', 43.3118, 5.36922, 'Réunion', 30, 'images/salles/005_salle_blanc.jpg', 'mi pede, nonummy ut, molestie in, tempus eu, ligula. Aenean euismod mauris eu elit. Nulla facilisi. Sed neque. Sed eget lacus. Mauris non dui nec urna suscipit nonummy. Fusce fermentum fermentum arcu. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia'),
(6, 'Dupuis', '341 rue de Lyon', 'Marseille', '13015', 'France', 43.3379, 5.3625, 'Fête', 70, 'images/salles/006_salle_dupuis.jpg', 'id, erat. Etiam vestibulum massa rutrum magna. Cras convallis convallis dolor. Quisque tincidunt pede ac urna. Ut tincidunt vehicula risus. Nulla eget metus eu erat semper rutrum. Fusce dolor quam, elementum at, egestas a, scelerisque sed, sapien. Nunc pulvinar arcu et pede. Nunc sed orci lobortis'),
(7, 'Leroux', '128 rue des Pyrénées', 'Paris', '75015', 'France', 48.8583, 2.40324, 'Multimédia', 40, 'images/salles/007_salle_leroux.jpg', 'lorem, auctor quis, tristique ac, eleifend vitae, erat. Vivamus nisi. Mauris nulla. Integer urna. Vivamus molestie dapibus ligula. Aliquam erat volutpat. Nulla dignissim. Maecenas ornare egestas ligula. Nullam feugiat placerat velit. Quisque varius. Nam porttitor scelerisque neque. Nullam nisl. Maecenas malesuada fringilla est. Mauris'),
(8, 'Marchal', '190 Avenue Barthélémy Buyer', 'Lyon', '69009', 'France', 45.7639, 4.79385, 'Fête', 80, 'images/salles/008_salle_marchal.jpg', 'Etiam bibendum fermentum metus. Aenean sed pede nec ante blandit viverra. Donec tempus, lorem fringilla ornare placerat, orci lacus vestibulum lorem, sit amet ultricies sem magna nec quam. Curabitur vel lectus. Cum sociis natoque'),
(9, 'Cousin', '88 Boulevard Auguste Blanqui', 'Paris', '75013', 'France', 48.8308, 2.34549, 'Réunion', 20, 'images/salles/009_salle_cousin.jpg', 'amet diam eu dolor egestas rhoncus. Proin nisl sem, consequat nec, mollis vitae, posuere at, velit. Cras lorem lorem, luctus ut, pellentesque eget, dictum placerat, augue. Sed molestie. Sed id risus quis'),
(10, 'Adam', '149 Avenue Lacassagne', 'Lyon', '69003', 'France', 45.7484, 4.87952, 'Multimédia', 30, 'images/salles/010_salle_adam.jpg', 'Proin velit. Sed malesuada augue ut lacus. Nulla tincidunt, neque vitae semper egestas, urna justo faucibus lectus, a sollicitudin orci sem'),
(11, 'test', 'Rue de test', 'Ville de Test', '12345', 'TEST', 48.8532, 2.34989, 'Fête', 45, 'images/salles/011_salle_test.jpg', 'Test<br />\r\nblabla');


INSERT INTO promotion
(id_promo, code_promo, reduction)
VALUES
    (17, 'LKSP15', 15),
    (18, 'LKSP30', 30),
    (19, 'LKSP50', 50);


INSERT INTO produit
(id_produit, date_arrivee, date_depart, id_salle, id_promo, prix, etat)
VALUES
(1, '2015-10-12', '2015-10-18', 10, NULL, 499, 0),
(2, '2015-11-03', '2016-11-07', 8, NULL, 750, 0),
(3, '2015-12-22', '2015-12-24', 3, NULL, 500, 1),
(4, '2016-01-14', '2016-01-16', 2, NULL, 429, 1),
(5, '2016-01-19', '2016-01-23', 9, NULL, 639, 1),
(6, '2016-02-08', '2016-02-11', 7, NULL, 589, 1),
(7, '2016-03-01', '2016-03-04', 1, NULL, 600, 1),
(8, '2016-03-02', '2016-03-05', 4, NULL, 460, 0),
(9, '2016-03-10', '2016-03-15', 5, NULL, 899, 1),
(10, '2016-03-14', '2016-03-19', 6, 18, 899, 1),
(11, '2016-03-28', '2016-04-06', 7, 17, 390, 1),
(12, '2016-03-29', '2016-04-02', 9, 19, 290, 1),
(13, '2016-04-01', '2016-04-04', 2, NULL, 560, 1),
(14, '2016-04-05', '2016-04-09', 6, NULL, 710, 1),
(15, '2016-05-12', '2016-05-19', 3, NULL, 660, 1),
(16, '2016-05-12', '2016-05-15', 1, NULL, 660, 1),
(17, '2016-03-18', '2016-03-20', 11, 18, 450, 1),
(23, '2016-03-30', '2016-03-31', 11, 17, 319, 1),
(24, '2016-03-23', '2016-03-25', 9, NULL, 299, 1),
(25, '2016-03-30', '2016-03-31', 5, NULL, 349, 1),
(26, '2016-04-11', '2016-04-15', 7, 17, 999, 1),
(27, '2016-04-06', '2016-04-08', 6, NULL, 369, 1),
(28, '2016-04-04', '2016-04-08', 8, NULL, 799, 0),
(29, '2016-04-28', '2016-04-30', 4, NULL, 499, 0),
(30, '2016-05-09', '2016-05-10', 1, NULL, 259, 0),
(31, '2016-05-10', '2016-05-11', 2, NULL, 309, 0),
(32, '2016-05-12', '2016-05-13', 3, 18, 699, 0),
(33, '2016-05-20', '2016-05-22', 3, NULL, 799, 0),
(34, '2016-06-01', '2016-06-04', 2, 17, 499, 0),
(35, '2016-06-04', '2016-06-08', 4, 19, 589, 0),
(36, '2016-06-09', '2016-06-12', 6, NULL, 419, 0),
(37, '2016-06-14', '2016-06-16', 8, NULL, 489, 0),
(38, '2016-06-19', '2016-06-21', 10, NULL, 769, 0),
(39, '2016-06-23', '2016-06-27', 5, NULL, 999, 0);



INSERT INTO membre
(id_membre, pseudo, password, email, prenom, nom, sexe, adresse, cp, ville, pays, statut, newsletter)
VALUES
(1, 'admin', '$2a$04$bg.2.tcnaAfomBfLEfiVT.T/TAToyYCAZwStQLjWDTlLlaQqVkwi2', 'admin@athib.com', 'super', 'user', 'h', 'Rue de Localhost', '12701', 'Home', 'France', '1', '0'),
(2, 'arnaud', '$2a$04$nkEAMLQvs8Va7LBV6zIrNeKK/coabYnEL6fiGQFE7gLrPG19u6hY2', 'arnaud@athib.com', 'Arnaud', 'Thibaudet', 'h', 'Rue de la Bergerie', '78960', 'Voisins-le-Bretonneux', 'France', '0', '0'),
(3, 'mdupont', '$2a$04$IxizDG/aSgxCdc2AIN2.3eCNGiUL3eLsEjg0D5qrQNKnFYJr42eBu', 'martin@dupont.fr', 'Martin', 'Dupont', 'f', '65 Avenue des Essais', '56789', 'Ville de Test', 'France', '0', '0'),
(4, 'jcode', '$2a$04$05H4AQiDgL9YvLhtbmDmxeg7UN64BG0AB1kohUUEg48npsKGadla.', 'jean@code.com', 'Jean', 'Code', 'h', '127 Port Localhost', '10101', 'Dev City', 'France', '0', '1'),
(5, 'pseudo', '$2a$04$TCQi.7Ok01u3/yEvr3.yNe3U3S0CID.r5WzgJ4YyZCX6W4281B.V.', 'pseudo@exemple.fr', 'Prénom', 'Nom', 'h', '10 Rue des Tests', '12345', 'Ville de l\'Essai', 'France', '0', '0'),
(6, 'schneider', '$2a$04$DRvHk3YaIgc/qeChb40pB.A4C/S9.9tLiIw.CYMnf9ecK1V8FAWW2', 'odio.phasellus@velvenenatis.com', 'name', 'Nguyen', 'f', '544-2987 Aenean Avenue', '810650', 'La Serena', 'American Samoa', '0', '0'),
(7, 'dufour', '$2a$04$UibvWxbTE/4RIPazmEQDIuxA9r0J1t5R7Ff1vJytZDfalsg9VyLpK', 'dapibus@sagittis.com', 'name', 'Rodriguez', 'h', 'P.O. Box 814,  9216 Adipiscing Avenue', '52858', 'Gressoney-Saint-Jean', 'Solomon Islands', '0', '1'),
(8, 'richard', '$2a$04$hcC3/i.lfyVtddH9dPvt0.2LFRnHLpUp6IgClx5xnCJsIq0hOfsPW', 'ultricies.ligula.Nullam@orciDonec.ca', 'name', 'Benoit', 'f', '3149 Ultrices. Ave', '78885', 'Bellegem', 'Central African Republic', '0', '1'),
(9, 'guerin', '$2a$04$GPYYjGFO6gyBQBDQ1NtZoeaPLvE7L0KrPn9KbBjS3atuCbPqItwhW', 'a@felisNullatempor.ca', 'name', 'Breton', 'h', '969-4878 In Road', '99036', 'Tranent', 'Liechtenstein', '0', '0'),
(10, 'blanchard', '$2a$04$MOhpqTxBfmwZmiL/rarEhO16uSoo2yZfB8e.0yfg5fI2eO1FAe9/u', 'sem.elit.pharetra@molestietellusAenean.co.uk', 'name', 'Vidal', 'f', 'P.O. Box 479,  965 Lorem,  Rd.', '31802-070', 'Bergisch Gladbach', 'Monaco', '0', '0'),
(11, 'durand', '$2a$04$DrlsArbE2NF1ZkBVlerPTuOcB9BWFE9F9p8xSNTd1PyHmUVynHYjy', 'Quisque.ornare.tortor@Donec.edu', 'name', 'Germain', 'f', '206 Pharetra,  St.', '9573', 'Zevekote', 'Ghana', '0', '1'),
(12, 'benoit', '$2a$04$SkE70pPn7M2N0YeXYECVseIeqDJuQqRZ7.dvcz/rJycszhnz/YPOS', 'eu@Quisquevarius.org', 'name', 'Martinez', 'h', 'P.O. Box 987,  6633 Dapibus Rd.', '48142-148', 'Isla de Maipo', 'Yemen', '0', '0'),
(13, 'colin', '$2a$04$tmC9/GOxPunbjk4dgvw1Hub6Jy4T4BSdr1JOPHM/U6GThfw4rEqvC', 'Vivamus.non.lorem@Suspendisse.com', 'name', 'Fournier', 'f', 'P.O. Box 312,  1916 Senectus Av.', '18416', 'Messancy', 'Bermuda', '0', '1'),
(14, 'bonnet', '$2a$04$YVIdNN/vUqajvrBRsfHWU.xvRels8mY7uVLjPJOevWLtir/qOwnQi', 'nisi.Aenean.eget@orciluctuset.co.uk', 'name', 'Joly', 'h', 'Ap #454-8846 Cras Avenue', 'P7T 5Y2', 'High Wycombe', 'Algeria', '0', '1'),
(15, 'dubois', '$2a$04$ylIktOsCumyhAuZOkLd.I.yHHwr2MsS//sTYPf1T8uJxOARj2Uleu', 'Morbi.sit.amet@euodioPhasellus.edu', 'name', 'Martinez', 'f', 'Ap #364-1375 Massa. Rd.', '43525', 'Butte', 'Iran', '0', '1'),
(16, 'vidal', '$2a$04$sy7dp3oRNkzLEd7ed9Ow4O51n6H9o1cQvI5mQaYop/30.5mLwAeX2', 'est.Mauris.eu@accumsanconvallisante.net', 'name', 'Marchal', 'h', 'Ap #708-7548 Enim St.', '19133', 'Ebenthal in Kärnten', 'Cape Verde', '0', '0'),
(17, 'barre', '$2a$04$2QWrOOVQoK6mkLOJNXFAmO4Zk4Xggns3IJR.50hSDFFnWs/NEA6BO', 'turpis@laoreet.co.uk', 'name', 'Gauthier', 'h', 'P.O. Box 743,  9487 Erat,  Street', '90999', 'Eisleben', 'Seychelles', '0', '1'),
(18, 'carre', '$2a$04$/mEbBIDxKELPJuq5xtPgLOBc8uRqLX/ikci5z3fSlFbiv9jN8vDP.', 'cursus.vestibulum@Quisqueornare.edu', 'name', 'Lecomte', 'h', '5190 Eu St.', '47591-545', 'Anápolis', 'Algeria', '0', '1'),
(19, 'chevallier', '$2a$04$KWIHQRjWJlJ/mtCehScp5.3Kwx4DjfAdFZy1epWSUpj3BkOaNACxC', 'aliquet@semper.org', 'name', 'Barre', 'f', '996-4297 Tempor,  Av.', '3196', 'Rezzoaglio', 'Montenegro', '0', '0'),
(20, 'renault', '$2a$04$i0jxLqJb5UfQVP8Zft0ZgOeSP/rLDnNv9KpV6FwCUc7oAtleryj4y', 'ullamcorper.nisl.arcu@eueuismodac.com', 'name', 'Renault', 'f', '772-9893 A Rd.', '7931', 'Ledbury', 'Portugal', '0', '1'),
(21, 'albert', '$2a$04$KknG18.c0X9D.5R7T7g3ju1xp6kN6D.3eWk.9IFbU4NbK7is11EWm', 'fermentum@justoPraesent.co.uk', 'name', 'Poulain', 'h', '7937 Suspendisse St.', 'P4N 5W7', 'Villers-sur-Semois', 'Turkey', '0', '0'),
(22, 'denis', '$2a$04$bsQNggM5qR9ZJd8EFEBYP.dNa/2UuIAaE5putiiacw8LOFiYOjNpW', 'elit.Aliquam@nonantebibendum.ca', 'name', 'Schneider', 'f', '2921 Consectetuer Ave', '16296', 'Varna/Vahrn', 'Bermuda', '0', '0'),
(23, 'bary', '$2a$04$fxHzRpprHXRZwBx7MEBAU.hmOhFkyxsbx323q46vie22AkMNlb1i.', 'Phasellus@feugiatnecdiam.ca', 'name', 'David', 'h', 'Ap #565-6671 Sed Rd.', '86-873', 'Vietri di Potenza', 'Guam', '0', '0'),
(24, 'dumas', '$2a$04$j52RDjnU7.22KR5h5nU/8uhLIgDt1GhGxjMf2f3k30xGVT9hbfYOO', 'eu.placerat.eget@velsapien.com', 'name', 'Boulanger', 'f', '4732 Euismod Rd.', '20924', 'Rosolini', 'Jersey', '0', '1'),
(25, 'nguyen', '$2a$04$WUymQaT3kfw8eJuiGZqmcOia5VoZYxnrApYP.t873uuk7lLW.AsAy', 'eu.metus@eu.co.uk', 'name', 'Chevallier', 'f', 'P.O. Box 760,  6222 Sed Avenue', '91666', 'Harrogate', 'Bermuda', '0', '0');




INSERT INTO avis
(id_avis, id_membre, id_salle, note, date, commentaire)
VALUES
(1, 5, 2, 7, '2015-09-23 17:17:50', 'Proin vel nisl. Quisque fringilla euismod enim. Etiam'),
(2, 2, 7, 7, '2015-10-29 06:42:42', 'iaculis quis,  pede. Praesent eu dui. Cum sociis natoque penatibus et magnis dis parturient montes,  nascetur'),
(3, 5, 2, 2, '2015-05-06 22:30:44', 'bibendum fermentum metus. Aenean sed pede nec ante blandit viverra. Donec tempus,  lorem fringilla ornare placerat,  orci'),
(4, 10, 6, 9, '2016-02-17 04:38:16', 'aliquam arcu. Aliquam ultrices iaculis odio. Nam interdum enim non nisi. Aenean eget metus. In'),
(5, 3, 1, 1, '2015-05-05 10:39:54', 'ut,  pharetra sed,  hendrerit a,  arcu. Sed et libero. Proin'),
(6, 9, 1, 8, '2015-06-22 22:57:15', 'Cras eget nisi dictum augue malesuada malesuada. Integer id magna et ipsum cursus vestibulum. Mauris magna. Duis dignissim tempor arcu.'),
(7, 3, 7, 8, '2015-04-10 02:44:25', 'mauris ipsum porta elit,  a feugiat tellus lorem eu metus. In lorem. Donec'),
(8, 2, 1, 3, '2015-07-20 07:33:59', 'eu erat semper rutrum. Fusce dolor quam,  elementum at,  egestas a,  scelerisque sed,  sapien. Nunc pulvinar arcu et pede. Nunc'),
(9, 6, 7, 8, '2015-03-11 23:10:02', 'tincidunt,  neque vitae semper egestas, '),
(10, 6, 6, 8, '2015-09-30 01:49:55', 'purus ac tellus. Suspendisse sed dolor. Fusce mi lorem,  vehicula'),
(11, 7, 4, 10, '2015-04-02 14:27:49', 'Suspendisse non leo. Vivamus nibh'),
(12, 5, 6, 2, '2015-07-20 07:04:23', 'tincidunt dui augue eu tellus. Phasellus elit pede,  malesuada'),
(13, 4, 5, 9, '2015-10-27 21:00:27', 'eget odio. Aliquam vulputate ullamcorper magna. Sed eu eros. Nam consequat dolor vitae dolor. Donec fringilla. Donec'),
(14, 4, 5, 5, '2015-12-03 02:08:12', 'facilisis lorem tristique aliquet. Phasellus fermentum convallis ligula. Donec luctus aliquet odio.'),
(15, 6, 9, 8, '2015-05-02 15:03:42', 'tempus risus. Donec egestas. Duis ac arcu. Nunc mauris. Morbi non sapien molestie orci tincidunt adipiscing. Mauris molestie pharetra'),
(16, 2, 3, 7, '2015-08-23 16:15:54', 'ipsum dolor sit amet,  consectetuer adipiscing elit. Aliquam auctor,  velit eget laoreet'),
(17, 2, 1, 10, '2015-11-07 22:33:24', 'Cras convallis convallis dolor. Quisque tincidunt pede ac urna. Ut tincidunt vehicula risus. Nulla eget metus eu erat semper'),
(18, 9, 5, 6, '2016-01-22 03:02:08', 'eros turpis non enim. Mauris quis turpis vitae purus gravida sagittis.'),
(19, 7, 10, 8, '2015-05-14 01:28:00', 'faucibus lectus,  a sollicitudin orci sem eget massa. Suspendisse eleifend. Cras sed leo. Cras'),
(20, 7, 5, 4, '2016-01-19 23:05:03', 'lacus pede sagittis augue,  eu tempor erat neque non quam. Pellentesque habitant morbi tristique senectus et netus et malesuada'),
(21, 5, 10, 2, '2015-08-28 20:32:11', 'Sed id risus quis diam luctus lobortis. Class aptent taciti sociosqu ad litora torquent per conubia nostra,  per inceptos hymenaeos.'),
(22, 6, 1, 3, '2015-12-03 01:23:07', 'est,  congue a,  aliquet vel,  vulputate eu,  odio. Phasellus at augue id ante'),
(23, 2, 1, 6, '2015-11-15 06:45:04', 'sit amet,  faucibus ut,  nulla. Cras eu tellus eu augue porttitor interdum. Sed auctor'),
(24, 3, 6, 7, '2015-04-30 08:51:09', 'placerat velit. Quisque varius. Nam porttitor scelerisque'),
(25, 6, 4, 2, '2016-02-12 14:07:06', 'semper pretium neque. Morbi quis urna. Nunc'),
(26, 5, 8, 4, '2016-01-19 19:44:50', 'pharetra. Quisque ac libero nec ligula consectetuer rhoncus. Nullam velit dui,  semper et,  lacinia'),
(27, 2, 5, 3, '2015-07-29 23:04:42', 'nec luctus felis purus ac tellus. Suspendisse sed dolor. Fusce mi lorem,  vehicula et,  rutrum eu,  ultrices sit amet, '),
(28, 8, 6, 4, '2016-01-13 03:11:11', 'Suspendisse ac metus vitae velit egestas lacinia. Sed congue,  elit sed consequat auctor,  nunc nulla vulputate dui,  nec'),
(29, 10, 6, 1, '2015-09-21 03:03:36', 'libero lacus,  varius et,  euismod et,  commodo at,  libero. Morbi accumsan laoreet ipsum. Curabitur consequat, '),
(30, 10, 1, 6, '2015-12-29 16:12:22', 'elit,  pharetra ut,  pharetra sed,  hendrerit a, '),
(31, 6, 1, 4, '2015-04-06 02:08:11', 'primis in faucibus orci luctus et ultrices posuere cubilia Curae; Donec tincidunt. Donec vitae erat vel pede blandit congue.'),
(32, 6, 10, 5, '2015-07-24 07:48:47', 'felis. Donec tempor,  est ac mattis semper,  dui'),
(33, 10, 9, 10, '2015-06-03 14:12:14', 'sit amet lorem semper auctor. Mauris vel turpis. Aliquam adipiscing lobortis risus. In mi'),
(34, 8, 1, 9, '2015-12-19 01:32:19', 'ante. Vivamus non lorem vitae odio sagittis semper. Nam tempor diam dictum sapien. Aenean massa. Integer vitae nibh. Donec est'),
(35, 10, 8, 2, '2015-04-15 04:52:39', 'amet,  consectetuer adipiscing elit. Aliquam auctor,  velit eget'),
(36, 10, 9, 6, '2016-01-25 19:38:53', 'sagittis. Duis gravida. Praesent eu nulla at sem molestie sodales. Mauris blandit enim consequat'),
(37, 8, 8, 3, '2015-10-05 18:51:40', 'sem egestas blandit. Nam nulla magna,  malesuada vel, '),
(38, 7, 5, 1, '2015-12-16 04:03:47', 'volutpat. Nulla facilisis. Suspendisse commodo tincidunt nibh. Phasellus nulla. Integer vulputate,  risus a ultricies adipiscing,  enim mi tempor'),
(39, 2, 10, 5, '2015-08-30 03:37:54', 'arcu eu odio tristique pharetra. Quisque ac libero nec ligula consectetuer rhoncus. Nullam velit dui,  semper'),
(40, 4, 8, 4, '2015-06-02 01:30:23', 'Donec consectetuer mauris id sapien. Cras'),
(41, 5, 5, 7, '2016-01-28 11:29:20', 'Aliquam rutrum lorem ac risus. Morbi metus. Vivamus euismod urna. Nullam'),
(42, 6, 8, 10, '2015-07-12 20:46:42', 'Aliquam nec enim. Nunc ut erat. Sed nunc est,  mollis non,  cursus non,  egestas a,  dui. Cras pellentesque.'),
(43, 6, 4, 7, '2015-08-08 16:31:32', 'eros turpis non enim. Mauris quis turpis vitae purus gravida sagittis.'),
(44, 10, 4, 7, '2015-03-18 21:34:49', 'Donec feugiat metus sit amet ante. Vivamus non lorem'),
(45, 5, 6, 2, '2015-04-02 00:07:45', 'tristique pellentesque,  tellus sem mollis dui,  in sodales elit erat vitae risus. Duis a mi fringilla mi lacinia mattis.'),
(46, 4, 7, 4, '2015-07-29 02:37:38', 'pretium et,  rutrum non,  hendrerit'),
(47, 7, 4, 3, '2015-09-12 10:02:44', 'nisi. Aenean eget metus. In nec orci. Donec nibh. Quisque nonummy ipsum'),
(48, 4, 7, 1, '2015-07-21 05:46:43', 'Vivamus euismod urna. Nullam lobortis quam'),
(49, 6, 4, 6, '2015-03-13 04:33:07', 'ac mattis semper,  dui lectus rutrum'),
(50, 8, 1, 1, '2015-03-13 16:38:43', 'tempor erat neque non quam.'),
(51, 5, 2, 3, '2015-07-15 00:38:02', 'cursus. Nunc mauris elit,  dictum eu,  eleifend nec,  malesuada ut,  sem.'),
(52, 4, 8, 1, '2015-10-12 14:00:44', 'eget ipsum. Suspendisse sagittis. Nullam vitae diam. Proin dolor. Nulla semper tellus id nunc interdum feugiat.'),
(53, 10, 5, 3, '2015-11-27 13:09:31', 'lacus. Quisque purus sapien,  gravida non,  sollicitudin a,  malesuada id,  erat. Etiam vestibulum massa rutrum magna. Cras convallis'),
(54, 8, 2, 3, '2015-06-29 10:36:06', 'velit dui,  semper et,  lacinia vitae,  sodales at,  velit. Pellentesque ultricies dignissim lacus. Aliquam'),
(55, 7, 4, 7, '2015-03-20 18:06:58', 'scelerisque,  lorem ipsum sodales purus,  in'),
(56, 5, 2, 10, '2015-09-14 00:14:37', 'consectetuer,  cursus et,  magna. Praesent interdum ligula eu enim. Etiam imperdiet'),
(57, 8, 8, 7, '2015-09-08 00:09:19', 'ultrices iaculis odio. Nam interdum enim non nisi. Aenean eget metus. In'),
(58, 4, 7, 10, '2015-07-23 07:08:20', 'erat vel pede blandit congue. In scelerisque scelerisque dui. Suspendisse ac metus vitae velit egestas lacinia. Sed congue,  elit'),
(59, 9, 6, 2, '2015-03-15 15:19:20', 'mauris ipsum porta elit,  a feugiat tellus lorem eu'),
(60, 10, 3, 10, '2015-06-05 01:56:58', 'Aliquam ornare,  libero at auctor ullamcorper,  nisl arcu iaculis enim, '),
(61, 7, 1, 2, '2015-11-30 13:39:32', 'enim. Etiam gravida molestie arcu. Sed eu nibh vulputate mauris sagittis placerat. Cras dictum ultricies ligula. Nullam enim.'),
(62, 4, 1, 5, '2015-03-19 19:28:34', 'mi lacinia mattis. Integer eu lacus. Quisque imperdiet,  erat nonummy ultricies'),
(63, 2, 4, 5, '2015-05-10 20:55:28', 'mattis. Integer eu lacus. Quisque imperdiet,  erat nonummy'),
(64, 4, 3, 8, '2015-09-07 02:20:17', 'accumsan neque et nunc. Quisque ornare tortor at risus. Nunc'),
(65, 10, 3, 10, '2015-08-25 16:36:41', 'Cras dolor dolor,  tempus non,  lacinia at,  iaculis quis,  pede. Praesent eu dui. Cum sociis natoque penatibus et magnis dis'),
(66, 6, 7, 7, '2015-12-23 12:19:59', 'vitae,  sodales at,  velit. Pellentesque ultricies dignissim lacus. Aliquam rutrum lorem'),
(67, 10, 3, 2, '2015-08-27 06:41:36', 'Mauris magna. Duis dignissim tempor arcu. Vestibulum ut eros non enim commodo hendrerit. Donec porttitor'),
(68, 6, 4, 3, '2016-01-06 16:55:07', 'eget nisi dictum augue malesuada malesuada. Integer id magna et'),
(69, 7, 8, 1, '2016-01-03 00:08:27', 'Phasellus fermentum convallis ligula. Donec luctus aliquet odio. Etiam ligula tortor,  dictum eu,  placerat eget,  venenatis a,  magna. Lorem ipsum'),
(70, 10, 5, 9, '2015-07-31 08:26:42', 'et magnis dis parturient montes,  nascetur ridiculus mus. Proin vel arcu'),
(71, 5, 8, 8, '2015-09-07 04:54:52', 'scelerisque neque sed sem egestas blandit. Nam nulla magna, '),
(72, 5, 7, 4, '2015-07-17 13:41:40', 'arcu imperdiet ullamcorper. Duis at lacus. Quisque purus sapien,  gravida non,  sollicitudin a,  malesuada id,  erat. Etiam vestibulum massa rutrum'),
(73, 2, 1, 2, '2015-10-21 13:23:14', 'vitae purus gravida sagittis. Duis gravida.'),
(74, 6, 6, 1, '2015-10-17 07:29:00', 'in faucibus orci luctus et'),
(75, 8, 8, 1, '2015-12-15 22:48:03', 'risus varius orci,  in consequat enim'),
(76, 10, 8, 6, '2015-04-28 09:39:11', 'quam vel sapien imperdiet ornare. In faucibus. Morbi vehicula. Pellentesque tincidunt tempus risus. Donec egestas. Duis ac arcu.'),
(77, 6, 10, 2, '2015-08-30 20:05:00', 'vitae erat vel pede blandit congue. In scelerisque scelerisque dui.'),
(78, 9, 9, 1, '2015-11-16 11:15:34', 'at,  egestas a,  scelerisque sed,  sapien. Nunc pulvinar arcu et pede. Nunc sed orci lobortis augue'),
(79, 8, 9, 5, '2015-05-06 15:32:39', 'justo eu arcu. Morbi sit amet massa. Quisque porttitor eros nec tellus. Nunc'),
(80, 3, 1, 6, '2015-08-27 12:50:10', 'tincidunt tempus risus. Donec egestas. Duis ac arcu. Nunc mauris. Morbi non sapien molestie orci tincidunt adipiscing. Mauris molestie pharetra'),
(81, 7, 2, 9, '2015-10-31 19:26:15', 'laoreet,  libero et tristique pellentesque, '),
(82, 8, 3, 7, '2015-10-12 20:49:42', 'Maecenas mi felis,  adipiscing fringilla,  porttitor vulputate, '),
(83, 6, 8, 7, '2015-08-25 06:26:21', 'elit,  dictum eu,  eleifend nec,  malesuada ut,  sem. Nulla interdum. Curabitur dictum.'),
(84, 3, 10, 8, '2015-05-04 11:16:29', 'mi pede,  nonummy ut,  molestie in,  tempus eu,  ligula. Aenean euismod mauris eu elit. Nulla facilisi. Sed neque. Sed'),
(85, 3, 7, 7, '2016-01-13 23:56:55', 'lectus,  a sollicitudin orci sem eget massa. Suspendisse'),
(86, 3, 7, 8, '2015-12-01 14:11:02', 'mi pede,  nonummy ut,  molestie in,  tempus eu, '),
(87, 7, 7, 2, '2015-07-03 08:31:52', 'ligula. Aenean euismod mauris eu elit. Nulla facilisi. Sed neque. Sed eget lacus. Mauris non dui'),
(88, 3, 1, 1, '2015-06-15 09:25:37', 'enim. Etiam imperdiet dictum magna. Ut tincidunt orci quis lectus. Nullam suscipit, '),
(89, 6, 8, 9, '2016-02-21 09:47:12', 'libero. Proin sed turpis nec mauris blandit mattis. Cras eget nisi dictum augue malesuada'),
(90, 10, 2, 5, '2015-10-31 16:45:07', 'Pellentesque habitant morbi tristique senectus et netus'),
(91, 9, 8, 2, '2015-08-30 21:07:48', 'Nunc ullamcorper,  velit in aliquet lobortis,  nisi nibh lacinia orci,  consectetuer euismod'),
(92, 7, 8, 9, '2015-11-10 22:30:24', 'dui lectus rutrum urna,  nec luctus felis purus ac tellus. Suspendisse'),
(93, 2, 3, 1, '2015-07-28 06:11:02', 'Praesent eu dui. Cum sociis natoque penatibus et magnis dis parturient montes,  nascetur ridiculus mus.'),
(94, 8, 10, 7, '2015-05-31 20:00:29', 'sit amet orci. Ut sagittis lobortis mauris. Suspendisse aliquet molestie tellus. Aenean egestas hendrerit neque. In ornare sagittis felis. Donec'),
(95, 7, 2, 8, '2015-11-27 22:42:05', 'cursus luctus,  ipsum leo elementum sem,  vitae aliquam eros turpis non enim. Mauris quis'),
(96, 7, 6, 10, '2015-09-30 18:55:29', 'urna convallis erat,  eget tincidunt dui augue eu tellus. Phasellus elit pede, '),
(97, 6, 6, 9, '2015-08-26 07:55:57', 'nulla ante,  iaculis nec,  eleifend non,  dapibus rutrum,  justo.'),
(98, 8, 10, 10, '2015-08-11 19:14:32', 'ac turpis egestas. Fusce aliquet magna a neque. Nullam ut'),
(99, 3, 9, 6, '2015-04-07 23:55:40', 'nisl sem,  consequat nec,  mollis vitae,  posuere at,  velit. Cras lorem lorem,  luctus ut,  pellentesque eget,  dictum'),
(100, 4, 9, 3, '2015-11-11 18:15:03', 'Duis elementum,  dui quis accumsan convallis,  ante lectus convallis est, ');



INSERT INTO commande (id_commande, montant, id_membre, date)
VALUES
(18, 1397, 1, '2016-03-09 10:26:47'),
(19, 798, 1, '2016-03-09 10:29:15'),
(20, 499, 2, '2016-03-14 10:20:53'),
(21, 398, 2, '2016-03-14 10:22:09'),
(22, 0, 2, '2016-03-14 10:25:34'),
(23, 672, 2, '2016-03-14 10:28:01'),
(24, 792, 2, '2016-03-14 10:40:35'),
(25, 1019, 4, '2016-03-15 09:35:00'),
(26, 419, 4, '2016-03-15 09:35:12'),
(27, 443, 5, '2016-03-15 09:44:57'),
(28, 1151, 6, '2016-03-15 09:45:56');



INSERT INTO details_commande (id_details_commande, id_commande, id_produit)
VALUES
    (17, 18, 15),
    (18, 18, 14),
    (19, 19, 16),
    (20, 19, 12),
    (21, 20, 12),
    (22, 20, 23),
    (23, 21, 11),
    (24, 23, 13),
    (25, 24, 15),
    (26, 25, 26),
    (27, 26, 25),
    (28, 27, 27),
    (29, 28, 24),
    (30, 28, 16);



INSERT INTO newsletter (id_newsletter, sujet, message, date_news) VALUES
    (1, 'Test newsletter 2', 'Hello, \r\n\r\nTest news.\r\nBlabla.\r\n\r\nSalut.', '2016-03-15 14:58:16');

