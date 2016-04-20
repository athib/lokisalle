<?php

require_once('../includes/init.inc.php');

/* VERIFICATION DES DROITS D'ACCES A LA PAGE*/
if(!$session->hasUser() || ($session->hasUser() && !$session->getUser()->isAdmin()))
{
	$session->addFlashes(LKS_FLASH_ERROR, 'Vous devez être identifié en tant qu\'administrateur pour accéder à cette page.');
	$session->redirect('../connexion');

	exit();
}


/* Initialisation des variables */

$user = $session->getUser();
$controller = new gestionavisController($session);
$builder = new Builder();

$champ_a_trier = 'date';
$ordre_tri = 'DESC';


/***************************
 * SUPPRESSION D'UN AVIS
 ***************************/
if($controller->getGetParam('action') == 'delete')
{
	$id_avis = $controller->getGetParam('id_avis');

	if(!$controller->deleteAvis($id_avis))
	{
		$session->addFlashes(LKS_FLASH_ERROR, 'Cet avis n\'existe pas.');
	}
	else
	{
		$session->addFlashes(LKS_FLASH_OK, 'L\'avis a bien été supprimé.');
	}
}

/**************************
 * CHANGEMENT ORDRE DE TRI
 **************************/
elseif($controller->getGetParam('orderby'))
{
	$champ_a_trier = $controller->getGetParam('orderby');
	$ordre_tri = $controller->getGetParam('sort');

	if($ordre_tri != 'asc' && $ordre_tri != 'desc')
	{
		$session->addFlashes(LKS_FLASH_WARNING, 'L\'ordre de tri spécifié est incorrect. Ordre décroissant par date appliqué par défault.');
		$ordre_tri = 'ASC';
	}

}

// Récupération des avis dans l'ordre choisi
$liste_avis = $controller->getAllAvisFromDb($champ_a_trier, $ordre_tri);


require_once('../includes/header.inc.php');

?>


<div class="row">
	<div class="col-md-12">
		<h1 class="text-center">Gestion des avis</h1>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<?php
		//initialisation des infos pour la génération du tableau
		$title = 'Liste des avis<br><small><em>(Passez votre curseur sur un commentaire pour le voir en intégralité)</em></small>';
		
		// ce tableau permet d'associer le nom d'un champ de la table (pour le tri) à un nom plus visuel (pour l'affichage)
		$entetes = array(
			'id_avis' => 'ID',
			'id_membre' => 'ID Membre',
			'id_salle' => 'ID Salle',
			'commentaire' => 'Commentaire',
			'note' => 'Note',
			'date' => 'Date'
		);
		
		// génération du tableau
		echo $builder->generateTable($title, $entetes, $liste_avis, false, true, 10, true);
		?>
	</div>
</div>






<?php require_once('../includes/footer.inc.php'); ?>