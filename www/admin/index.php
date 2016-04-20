<?php

require_once('../includes/init.inc.php');

// Vérification des droits d'accès à la page
if(!$session->hasUser() || ($session->hasUser() && !$session->getUser()->isAdmin()))
{
	$session->addFlashes(LKS_FLASH_ERROR, 'Vous devez être identifié en tant qu\'administrateur pour accéder à cette page.');
	$session->redirect('../connexion');

	exit();
}

$user = $session->getUser();



require_once('../includes/header.inc.php');

?>


<div class="row">
	<div class="col-md-12">
		<h1 class="text-center">Page d'accueil Administration</h1>
	</div>
</div>


<div class="row">
	<div class="col-md-offset-3 col-md-6 col-sm-offset-2 col-sm-8 col-xs-12">
		<table class="table table-condensed table-hover">
			<thead>
				<tr><th>Vos options d'admministration</th></tr>
			</thead>
			<tbody>
				<tr><td><a class="url_cell" href="gestion_salles.php">Gestion des salles</a></td></tr>
				<tr><td><a class="url_cell" href="gestion_produits.php">Gestion des produits</a></td></tr>
				<tr><td><a class="url_cell" href="gestion_avis.php">Gestion des avis</a></td></tr>
				<tr><td><a class="url_cell" href="gestion_promotions.php">Gestion des promotions</a></td></tr>
				<tr><td><a class="url_cell" href="gestion_commandes.php">Gestion des commandes</a></td></tr>
				<tr><td><a class="url_cell" href="gestion_membres.php">Gestion des membres</a></td></tr>
				<tr><td><a class="url_cell" href="statistiques.php">Statistiques</a></td></tr>
				<tr><td><a class="url_cell" href="envoi_newsletter.php">Envoyer une newsletter</a></td></tr>
			</tbody>
		</table>
	</div>
</div>




<?php require_once('../includes/footer.inc.php'); ?>