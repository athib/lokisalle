<?php

require_once('../includes/init.inc.php');

/* Vérification des droits d'accès à la page */
if(!$session->hasUser() || ($session->hasUser() && !$session->getUser()->isAdmin()))
{
	$session->addFlashes(LKS_FLASH_ERROR, 'Vous devez être identifié en tant qu\'administrateur pour accéder à cette page.');
	$session->redirect('../connexion');

	exit();
}

/* Initialisation des variables */
$user = $session->getUser();
$controller = new gestioncommandesController($session);
$builder = new Builder();
$commande_to_view = null;

// options de tri par défaut
$champ_a_trier = 'date';
$ordre_tri = 'desc';


/**************************
 * CHANGEMENT ORDRE DE TRI
 **************************/
if($controller->getGetParam('orderby'))
{
	$champ_a_trier = $controller->getGetParam('orderby');
	$ordre_tri = $controller->getGetParam('sort');

	if($ordre_tri != 'asc' && $ordre_tri != 'desc')
	{
		$session->addFlashes(LKS_FLASH_WARNING, 'L\'ordre de tri spécifié est incorrect. Ordre croissant appliqué par défault.');
		$ordre_tri = 'ASC';
	}
}

/**************************
 * DETAILS COMMANDE
 **************************/
if($controller->getGetParam('id_commande'))
{
	$id_commande = $controller->getGetParam('id_commande');
	
	// on vérifie que l'id commande récupéré est bien un nombre, sinon message d'erreur et redirection
	if(!preg_match('#^\d+$#', $id_commande))
	{
		$session->addFlashes(LKS_FLASH_ERROR, 'L\'ID de la commande doit être un nombre.');
		$session->redirect('gestion_commandes');
		exit();
	}
	elseif(!$commande_to_view = $controller->getCommandeById($id_commande))
	{
		$session->addFlashes(LKS_FLASH_ERROR, 'Il n\'y a aucune commande ayant l\'id ' . $id_commande . '.');
		$session->redirect('gestion_commandes');
		exit();
	}
}

// Si affichage général (cad pas de détails commande à afficher)
if(!$commande_to_view)
{
	$liste_commandes = $controller->getAllCommandesFromDb($champ_a_trier, $ordre_tri);
}




require_once('../includes/header.inc.php');

?>


<div class="row">
	<div class="col-md-12 text-center">
		<h1 class="text-center">Gestion des commandes</h1>
	</div>
</div>

<div class="row">
<?php
	// si pas de détails commande, affichage de la liste des commandes
	if(!$commande_to_view)
	{

		echo '<div class="col-md-offset-2 col-md-8">';

		$title = 'Liste des commandes';
		$entetes = array(
			'id_commande' => 'ID Commande',
			'montant' => 'Montant',
			'id_membre' => 'ID Membre',
			'date' => 'Date'
		);

		echo $builder->generateTable($title, $entetes, $liste_commandes, false, false, 200, true);

		echo '<p class="text-center">';
		echo '<strong>Chiffre d\'affaire de la société : ' . $controller->getTotalCA() . '€</strong>';
		echo '</p>';

		echo '</div>';
	}
	else // affichage du détail d'une commande
	{
		echo '<div class="col-md-12">';

		$id_cmd = $controller->getGetParam('id_commande');

		$title = 'Détails de la commande n°' . $id_cmd;
		$sort_prefix = 'id_commande=' . $id_cmd . '&';
		
		// ce tableau permet d'associer le nom d'un champ de la table (pour le tri) à un nom plus visuel (pour l'affichage)
		$entetes = array(
			'id_commande' => 'ID',
			'montant' => 'Montant',
			'date' => 'Date',
			'id_membre' => 'ID Membre',
			'pseudo' => 'Pseudo',
			'id_produit' => 'ID Produit',
			'id_salle' => 'ID Salle',
			'titre' => 'Titre',
			'ville' => 'Ville'
		);

		// on récupère les détails de la commande à afficher
		$details_commande = $controller->getDetailsCommande($commande_to_view, $champ_a_trier, $ordre_tri);

		// on génère le tableau avec les infos
		echo $builder->generateTable($title, $entetes, $details_commande, false, false, 200, true, $sort_prefix);

		echo '<p class="text-center">';
		echo '<a href="gestion_commandes.php">Retour à la liste des commandes</a>';
		echo '</p>';

		echo '</div>';
	}
?>
</div>



<?php require_once('../includes/footer.inc.php'); ?>