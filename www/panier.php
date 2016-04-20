<?php
require_once('includes/init.inc.php');

/* ON REDIRIGE VERS LA PAGE DE CONNEXION SI L'UTILISATREUR N'EST PAS CONNECTE */

if(!$session->hasUser())
{
	$session->addFlashes(LKS_FLASH_ERROR, 'Vous devez être identifié pour accéder à votre panier');
	$session->redirect('connexion');

	exit();
}


/* INITIALISATION DES VARIABLES ET CONTROLLEURS */

$controller = new panierController($session);
$form_ctrl = new formController($session);
$user = $session->getUser();
$panier = $session->getPanier();


/* SI ON A PASSE L'ACTION AJOUTER VIA L'URL (clic "ajouter panier")*/
if($controller->hasGetAction('ajouter'))
{
	// On récupère dans l'url l'id du produit à ajouter
	$id_produit = $controller->getGetId();

	// On récupère une instance du produit à ajouter
	$produit = $controller->getProduitById($id_produit);

	// On vérifie si le produit est déjà dans le panier, et si non on l'ajoute
	if($panier->ajouterProduit($produit))
	{
		$session->updatePanier($panier);
		$session->addFlashes(LKS_FLASH_OK, 'Votre produit a bien été ajouté au panier.');
		$session->redirect('panier');
		exit();
	}
	else // Le produit est déjà dans le panier, message d'erreur
	{
		$session->addFlashes(LKS_FLASH_ERROR, 'Ce produit existe déjà dans votre panier.');
	}
}

/***********************
 * VALIDER PANIER
 **********************/
if(isset($_POST['valider_commande']))
{
	// On passe les informations de POST au controlleur
	$controller->setPostInfos($_POST);

	// Si l'utilisateur n'a pas coché la case "accepter les CGV"
	if(!$controller->hasCGV())
		$session->addFlashes(LKS_FLASH_ERROR, 'Vous devez accepter les Conditions Générales de Vente pour valider votre commande.');
	// CGV ok : on valide la commande
	else
	{
		// passerCommande() effectue toutes les modifications dans la base de données
		$id_commande = $controller->passerCommande();
		
		// on récupère la dernière commande pour pouvoir gérer l'envoi de l'email récap au client
		$last_commande = $controller->getCommandeById($id_commande);

		// mail récap avec détail de la commande
		$controller->envoyerMailRecap($panier, $user, $last_commande);

		// On vide le panier pour en regénérer un tout neuf
		$controller->viderPanier();
		$panier = $session->getPanier();
		$session->addFlashes(LKS_FLASH_OK, 'Commande validée !');
		unset($_POST);
	}
}

/************************
*   VIDER LE PANIER
*************************/
if($controller->hasViderAction())
{
	$controller->viderPanier();
	$panier = $session->getPanier();
}


/********************************
 * SUPPRIMER UN PRODUIT DU PANIER
 *********************************/
if($controller->hasDeleteAction())
{
	// vérification de la validité d' l'id produit à supprimer
	if($controller->getGetId() == null)
		$session->addFlashes(LKS_FLASH_ERROR, 'Il faut indiquer l\'identifiant d\'un produit pour le supprimer du panier.');
	elseif($controller->getGetId() == false)
		$session->addFlashes(LKS_FLASH_ERROR, 'L\'identifiant du produit à supprimer doit être un entier positif.');
	else
	{
		// si l'id corresponda à un produit dans la panier
		$produit = (new produitController($session))->getProduitById($controller->getGetId());

		$checkRemove = $panier->retirerProduit($produit);

		if($checkRemove == null)
			$session->addFlashes(LKS_FLASH_ERROR, 'Il n\'y a aucun produit à retirer dans le panier.');
		elseif($checkRemove == false)
			$session->addFlashes(LKS_FLASH_ERROR, 'Nous n\'avons pas pu retirer ce produit du panier');
		else
		{
			$session->updatePanier($panier);
			$session->addFlashes(LKS_FLASH_OK, 'Le produit a bien été supprimé du panier.');
		}
	}
}



require_once('includes/header.inc.php');

?>

<div class="row">
	<h1 class="text-center">Votre panier</h1>
</div>


<div class="row">
	<div class="col-md-12">
		<div class="table-responsive">
	<table class="table table-condensed table-hover">
		<thead>
			<tr><th class="text-center" colspan="10">Contenu de votre panier (<a href="?action=vider">Vider</a>)</th></tr>
			<tr>
				<td>Produit</td>
				<td>Salle</td>
				<td>Photo</td>
				<td>Ville</td>
				<td>Capacité</td>
				<td>Date d'arrivée</td>
				<td>Date de départ</td>
				<td>Prix HT</td>
				<td>TVA</td>
				<td>Retirer</td>
			</tr>
		</thead>
		<tbody>
		<?php
		// AFFICHAGE DU PANIER
			if($panier->isEmpty())
				echo '<tr><td colspan="10" class="text-center"><em>Votre panier est vide</em></td></tr>';
			else
			{
				foreach($panier->getProduits() as $produit)
				{
					echo '<tr>';

					echo '<td>' . $produit->getInfo('id_produit') . '</td>';
					echo '<td>' . $produit->getSalle()->getInfo('titre') . '</td>';
					echo '<td><img class="img-responsive img-panier" src="' . $produit->getSalle()->getInfo('photo') . '" alt="Photo de la salle"></td>';
					echo '<td>' . $produit->getSalle()->getInfo('ville') . '</td>';
					echo '<td>' . $produit->getSalle()->getInfo('capacite') . '</td>';
					echo '<td>' . (new DateTime($produit->getInfo('date_arrivee')))->format('d/m/Y') . '</td>';
					echo '<td>' . (new DateTime($produit->getInfo('date_depart')))->format('d/m/Y') . '</td>';

					echo '<td>';
					if($produit->hasPromo())
					{
						echo '<span style="text-decoration: line-through;">';
						echo sprintf("%.02f", round($produit->getInfo('prix'), 2));
						echo '</span><br>';

						$prix_reduit = $produit->getInfo('prix') - ($produit->getInfo('prix') * $produit->getPromotion()->getReduction() / 100);

						echo '<span style="color: red;">';
						echo sprintf("%.02f", round($prix_reduit, 2));
						echo '</span>';
					}
					else
					{
						echo $produit->getInfo('prix');
					}
					echo '</td>';

					echo '<td>20,00%</td>';
					echo '<td class="text-center my-icon"><a href="?action=delete&id=' . $produit->getInfo('id_produit') . '"><img src="images/trash_icon.png" alt="Supprimer du panier"/></a></td>';

					echo '</tr>';
				}
			}
		?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="6">
				</td>
				<td class="text-right">Prix total TTC</td>
				<td colspan="2" class="text-right"><?php echo sprintf("%.02f", round($panier->getPrixTotalTTC(), 2)); ?>€ TTC</td>
				<td></td>
			</tr>
		</tfoot>
	</table>
		</div>
	</div>
</div>

<div class="row">
	<form action="panier.php" method="post" class="text-center">
		<?php
		if($panier->getNbProduits() > 0)
		{
			echo '<div class="checkbox">';
			echo '<label>';
			echo '<input type="checkbox" name="cgv" value="1"> J\'accepte les <a href="cgv.php">Conditions Générales de Vente</a>';
			echo '</label>';
			echo '</div>';

			echo '<input name="valider_commande" type="submit" class="btn btn-primary btn-lg" value="Valider la commande">';
		}

		?>
	</form>
</div>





<?php require_once('includes/footer.inc.php'); ?>