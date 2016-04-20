<?php

require_once('../includes/init.inc.php');

/* Vérification des droits d'accès */

if(!$session->hasUser() || ($session->hasUser() && !$session->getUser()->isAdmin()))
{
	$session->addFlashes(LKS_FLASH_ERROR, 'Vous devez être identifié en tant qu\'administrateur pour accéder à cette page.');
	$session->redirect('../connexion');

	exit();
}

/* initialisation des variables */

$user = $session->getUser();
$controller = new gestionmembresController($session);


/***************************
 * SUPPRESSION D'UN MEMBRE
 ***************************/
if($controller->getGetParam('action') == 'delete_user')
{
	$id_membre = $controller->getGetParam('id_membre');

	if(!$controller->deleteUser($id_membre))
	{
		$session->addFlashes(LKS_FLASH_ERROR, 'Cet utilisateur n\'existe pas.');
	}
	else
	{
		$session->addFlashes(LKS_FLASH_OK, 'L\'utilisateur a bien été supprimé.');
	}
}

/***************************
 * MODIF STATUT D'UN MEMBRE
 ***************************/
if($controller->getGetParam('action') == 'toggle_status')
{
	$id_membre = $controller->getGetParam('id_membre');

	$controller->updateStatus($id_membre);
}


// récupération de la liste des membres
$membres = $controller->getAllMembres();


require_once('../includes/header.inc.php');

?>


<div class="row">
	<div class="col-md-12">
		<h1 class="text-center">Gestion des membres</h1>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="table-responsive">
		<table class="table table-condensed table-hover">
			<thead>
				<tr>
					<th colspan="10" class="text-center">Liste des membres</th>
				</tr>
				<tr>
					<th>ID</th>
					<th>Pseudo</th>
					<th>Prénom</th>
					<th>Nom</th>
					<th>E-mail</th>
					<th>Sexe</th>
					<th>Ville</th>
					<th>Statut</th>
					<th>Promouvoir</th>
					<th>Supprimer</th>
				</tr>
			</thead>
			<tbody>
				<?php
					foreach($membres as $membre)
					{
						echo '<tr>';

						echo '<td>' . $membre->getProperty('id_membre') . '</td>';
						echo '<td>' . $membre->getProperty('pseudo') . '</td>';
						echo '<td>' . $membre->getProperty('prenom') . '</td>';
						echo '<td>' . $membre->getProperty('nom') . '</td>';
						echo '<td>' . $membre->getProperty('email') . '</td>';
						echo '<td>' . $membre->getProperty('sexe') . '</td>';
						echo '<td>' . $membre->getProperty('ville') . '</td>';
						echo '<td>' . $membre->getProperty('statut') . '</td>';

						echo '<td class="text-center my-icon">';
						if($membre->isAdmin())
						{
							if($membre->getProperty('id_membre') == 1)
							{
								echo '<img src="' . RACINE_SITE . 'images/admin_icon.png" />';
							}
							else
							{
								echo '<a href="?action=toggle_status&id_membre=' . $membre->getProperty('id_membre') . '">';
								echo '<img src="' . RACINE_SITE . 'images/admin_icon.png" />';
								echo '</a>';
							}
						}
						else
						{
							echo '<a href="?action=toggle_status&id_membre=' . $membre->getProperty('id_membre') . '">';
							echo '<img src="' . RACINE_SITE . 'images/member_icon.png" />';
							echo '</a>';
						}
						echo '</td>';

						echo '<td class="text-center my-icon"><a href="?action=delete_user&id_membre=' . $membre->getProperty('id_membre') . '"><img src="' . RACINE_SITE . 'images/trash_icon.png" /></a></td>';

						echo '</tr>';
					}
				?>
			</tbody>
		</table>
		</div>
	</div>
</div>


<?php require_once('../includes/footer.inc.php'); ?>