<?php

require_once('../includes/init.inc.php');

/* On vérifie si l'utilisateur est connecté et est admin pour accéder à cette page */

if(!$session->hasUser() || ($session->hasUser() && !$session->getUser()->isAdmin()))
{
	$session->addFlashes(LKS_FLASH_ERROR, 'Vous devez être identifié en tant qu\'administrateur pour accéder à cette page.');
	$session->redirect('../connexion');

	exit();
}


/* Initialisation des variables */

$user = $session->getUser();
$controller = new statistiquesController($session);

$builder = new Builder();


require_once('../includes/header.inc.php');

?>


<div class="row">
	<div class="col-md-12 text-center">
		<h1 class="text-center">Statistiques</h1>
	</div>
</div>


<div class="row">
<div class="col-md-offset-2 col-md-8">


<div class="row">
	<div class="col-md-12">
		<?php
		/**
		 * TOP 5 DES SALLES LES MIEUX NOTEES
		 * Prise en compte de la possibilité de doublons (salles avec la même note)
		 */
			$top = $controller->getTop5NotesSalles();
			$title = 'Top 5 des salles les mieux notées<br><small>Les salles ex-aequos sont affichées</small>';
			$entetes = array(
				'id_salle' => 'ID Salle',
				'titre' => 'Titre',
				'ville' => 'Ville',
				'note_moyenne' => 'Note moyenne'
			);

			echo $builder->generateTable($title, $entetes, $top, false, false, 200, false);
		?>
	</div>
</div>

<hr>

<div class="row">
	<div class="col-md-12">
		<?php
		/**
		 * TOP 5 DES SALLES LES PLUS VENDUES
		 * Prise en compte de la possibilité de doublons (salles avec la même note)
		 */
		$top = $controller->getTop5VentesSalles();
		$title = 'Top 5 des salles les plus vendues<br><small>Les salles ex-aequos sont affichées</small>';
		$entetes = array(
			'id_salle' => 'ID Salle',
			'titre' => 'Titre',
			'ville' => 'Ville',
			'nb_ventes' => 'Nombre de ventes'
		);

		echo $builder->generateTable($title, $entetes, $top, false, false, 200, false);
		?>
	</div>
</div>

<hr>

<div class="row">
	<div class="col-md-12">
		<?php
		/**
		 * TOP 5 DES UTILISATEURS QUI ACHETENT LE PLUS EN QUANTITE
		 * Prise en compte de la possibilité de doublons (salles avec la même note)
		 */
		$top = $controller->getTop5MembresAcheteursQuantite();
		$title = 'Top 5 des membres qui achètent le plus (en quantité)<br><small>Les ex-aequos sont affichés</small>';
		$entetes = array(
			'id_membre' => 'ID Membre',
			'pseudo' => 'Pseudo',
			'nb_commandes' => 'Nombre de commandes'
		);

		echo $builder->generateTable($title, $entetes, $top, false, false, 200, false);
		?>
	</div>
</div>

<hr>

<div class="row">
	<div class="col-md-12">
		<?php
		/**
		 * TOP 5 DES UTILISATEURS QUI ACHETENT LE PLUS EN TERMES DE PRIX
		 * Prise en compte de la possibilité de doublons (salles avec la même note)
		 */
		$top = $controller->getTop5MembresAcheteursPrix();
		$title = 'Top 5 des membres qui achètent le plus (en prix)<br><small>Les ex-aequos sont affichés</small>';
		$entetes = array(
			'id_membre' => 'ID Membre',
			'pseudo' => 'Pseudo',
			'nb_achats' => 'Montant des achats (en €)'
		);

		echo $builder->generateTable($title, $entetes, $top, false, false, 200, false);
		?>
	</div>
</div>



</div>
</div>


<?php require_once('../includes/footer.inc.php'); ?>