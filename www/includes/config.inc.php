<?php

/************************************************/
/* PARAMETRES DE CONNEXION A LA BASE DE DONNEES */
/************************************************/
const LOKISALLE_DB_HOST = 'localhost';
const LOKISALLE_DB_NAME = 'ifocop_lokisalle';
const LOKISALLE_DB_LOGIN = 'root';
const LOKISALLE_DB_PASSWORD = 'root';

/************************************************/


/******************************/
/* PARAMETRES DES FORMULAIRES */
/******************************/
const LKS_FORMAT_PSEUDO = '#^[\w-_.]{3,15}$#';
const LKS_FORMAT_PSEUDO_MSG = 'Le pseudo doit être constitué de 3 à 15 caractères alphanumériques (trait
d\'union, underscore et point autorisés).';

const LKS_FORMAT_PASSWORD = '#^\w{3,15}$#';
const LKS_FORMAT_PASSWORD_MSG = 'Le mot de passe doit être constitué de 3 à 15 caractères alphanumériques
(sans accents ni caractères spéciaux).';

const LKS_FORMAT_EMAIL = '#^[a-z0-9-_.]+@[a-z0-9-.]+\.[a-z]{2,}$#';
const LKS_FORMAT_EMAIL_MSG = 'L\'e-mail saisi n\'a pas un format valide (minuscules, chiffres, trait
d\'union, underscore, point sous la forme "pseudo@exemple.com").';

const LKS_FORMAT_PRENOM = '#^[a-zA-ZáàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ-]{3,20}$#';
const LKS_FORMAT_PRENOM_MSG = 'Le prénom doit contenir entre 3 et 20 lettres (trait d\'union autorisé).';

const LKS_FORMAT_NOM = '#^[a-zA-ZáàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ \'-]{3,20}$#';
const LKS_FORMAT_NOM_MSG = 'Le nom doit contenir entre 1 et 20 lettres (trait d\'union et apostrophes
autorisés).';

const LKS_FORMAT_ADRESSE = '#^[a-zA-Z0-9áàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸ, \'-]{3,30}$#';
const LKS_FORMAT_ADRESSE_MSG = 'L\'adresse doit être constituée de 3 à 30 caractères.';

const LKS_FORMAT_VILLE = '#^[a-zA-Z0-9áàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸ, \'-]{3,30}$#';
const LKS_FORMAT_VILLE_MSG = 'La ville doit être constituée de 3 à 30 caractères.';

const LKS_FORMAT_CP = '#^[0-9]{5}$#';
const LKS_FORMAT_CP_MSG = 'Le code postal doit être constitué de 5 chiffres.';

const LKS_FORMAT_PAYS = '#^[a-zA-Z0-9áàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸ \'-]{3,20}$#';
const LKS_FORMAT_PAYS_MSG = 'Le pays doit être constituée de 3 à 20 caractères.';

const LKS_FORMAT_COMMENTS = '#.{1,300}#s';
const LKS_FORMAT_COMMENTS_MSG = 'Commentaire limité à 300 caractères';

const LKS_FORMAT_DESCRIPTION = '#.{1,1000}#s';
const LKS_FORMAT_DESCRIPTION_MSG = 'La description est limitée à 1000 caractères';

const LKS_FORMAT_DATE = '#^\d{4}-\d{2}-\d{2}$#';
const LKS_FORMAT_DATE_MSG = 'La date doit être au format "YYYY-MM-DD".';

const LKS_FORMAT_PROMO = '#^[A-Z0-9]{6}$#';
const LKS_FORMAT_PROMO_MSG = 'Désolé, ce code promo n\'est pas valide.';

const LKS_FORMAT_CODE_PROMO = '#^[\w]{6}$#';
const LKS_FORMAT_CODE_PROMO_MSG = 'Le code promo doit contenir exactement 6 caractères alphanumériques non accentués.';

const LKS_FORMAT_COORDS = '#^[\d]+[.]?[\d]*$#';
const LKS_FORMAT_COORDS_MSG = 'Le format de la coordonée n\'est pas correct : "[au moins 1 chiffre].[1 ou plusieurs chiffres]".';


define('LKS_DATA_RADIO_SEX', serialize(['h' => 'Un homme', 'f' => 'Une femme']));
define('LKS_DATA_YES_NO', serialize([1 => 'Oui', '0' => 'Non']));


/******************************/
/* ALERTES BOOTSTRAP          */
/******************************/
const LKS_FLASH_ERROR   = 'danger';
const LKS_FLASH_OK      = 'success';
const LKS_FLASH_WARNING = 'warning';
const LKS_LABEL_HIDE    = 'sr-only';




/*****************************/
/* PARAMETRES DIVERS DU SITE */
/*****************************/
const LKS_CONTACT = 'lokisalle@athib.com';
const LKS_COOKIE_TIME = 60; // cookie reglé à 60 secondes pour les tests
const LKS_FICHE_DESC_LIMIT = 100;
const LKS_TVA = 1.2;


/***************************
 * COORDONNEES PAR DEFAULT *
 * pour le cas où on se les saisit pas lors de l'ajout
/***************************/
const PARIS_LAT = 48.853187;
const PARIS_LONG = 2.349891;

const MARSEILLE_LAT = 43.284188;
const MARSEILLE_LONG = 5.371238;

const LYON_LAT = 45.762533;
const LYON_LONG = 4.822626;