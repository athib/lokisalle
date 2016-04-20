<?php

/**************************************/
/* CHEMIN D'ACCES A LA RACINE DU SITE */
/**************************************/
define('RACINE_SITE', '/lokisalle/www/');  // Localhost
//const RACINE_SITE = '/lokisalle/';    // Hébergeur
/**************************************/

require_once('config.inc.php');



/*********************************************************/
/* AUTOLOADER POUR LE CHARGEMENT AUTOMATIQUE DES CLASSES */
/*********************************************************/
spl_autoload_register('autoload');

function autoload($class)
{
	if(file_exists(dirname(__DIR__) . "/class/entity/$class.class.php"))
		require(dirname(__DIR__) . "/class/entity/$class.class.php");
	else
		require(dirname(__DIR__) . "/class/controller/$class.php");
}
/*********************************************************/


/* Création d'une instance de la connexion à la Base de données, et d'une instance de la Session */
/* Ces deux objets seront disponibles sur toutes les pages après l'inclusion de init.inc.php */

$bdd = new Database(LOKISALLE_DB_HOST, LOKISALLE_DB_NAME, LOKISALLE_DB_LOGIN, LOKISALLE_DB_PASSWORD);

$session = new Session($bdd);


/* POUR LE DEV, AFFICHAGE FORMATE D'UNE VARIABLE */

function debug($var)
{
	echo '<pre>';
	var_dump($var);
	echo '</pre>';
}