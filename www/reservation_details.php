<?php

require_once('includes/init.inc.php');


/* INITIALISATION DES VARIABLES */

$controller = new reservationdetailsController($session);
$panier = $session->getPanier();


/* AJOUT COMMENTAIRE */

if($controller->hasCommentSent())
{
	$form = new formController($session);
	
	// filtrage des données postées
	$commentaire = nl2br(htmlspecialchars($_POST['commentaire']));
	$note = $_POST['note'];

	/* TRAITEMENT DU FORMULAIRE */
	if($form->isEmptyField($commentaire))
	{
		$session->addFlashes(LKS_FLASH_ERROR, 'Votre commentaire est vide.');
		$form->addError('commentaire');
	}
	elseif(!$form->isValid($commentaire, LKS_FORMAT_COMMENTS))
	{
		$session->addFlashes(LKS_FLASH_ERROR, LKS_FORMAT_COMMENTS_MSG);
		$form->addError('commentaire');
	}
	else // Tout est ok, on enregistre le commentaire et la note dans la base de données
	{
		$controller->registerComment($_POST['id_salle'], $commentaire, $note);
		$session->addFlashes(LKS_FLASH_OK, 'Votre commentaire a été validé.');
		$session->redirect('reservation_details', '?id_produit=' . $_POST['id_produit']);
		exit();
	}
}


// On contrôle que l'id passé en paramètre soit bien un nombre
if(!isset($_POST['post_commentaire']) && !$controller->hasGetIdValid())
{
	$session->addFlashes(LKS_FLASH_ERROR, 'Vous devez sélectionner une fiche valide pour accéder à ses détails');
	$session->redirect('reservation');
	exit();
}

$id_produit = $controller->getGetId();
$produit = $controller->getProduitById($id_produit);



// Si on a passé un id inexistant via l'url, on redirige vers la page de liste des salles
if($produit == null)
{
	$session->addFlashes(LKS_FLASH_ERROR, 'Le produit demandé n\'existe pas.<br>Vous avez été redirigé sur la liste des salles.');
	$session->redirect('reservation');
	exit();
}

$salle = $produit->getSalle();


/* AJOUT D'UN PRODUIT AU PANIER */

if($controller->hasAjoutPanier())
{
	// Si le produit n'est pas déjà dans le panier, on l'ajoute
	if($panier->ajouterProduit($produit))
	{
		$session->updatePanier($panier);
		$session->addFlashes(LKS_FLASH_OK, 'Votre produit a bien été ajouté au panier.');
		$session->redirect('panier');
		exit();
	}
	else
	{
		$session->addFlashes(LKS_FLASH_ERROR, 'Ce produit existe déjà dans votre panier.');
	}
}




// On récupère les avis liés à la salle
$avis = $controller->getAvisSalle($salle);
$salle->setAvis($avis);

// Récupère 3 suggestions de salles dans la même ville que le produit affiché
$suggestions = $controller->getSuggestions($produit);




require_once('includes/header.inc.php');

?>




<div class="row">
	<div class="col-md-12">
		<h1 class="text-center">Détails du Produit</h1>
	</div>
</div>


	<div class="col-md-12">

<div class="row fiche-produit">
	<div class="col-md-12">
		<div class="col-md-12 text-center">
			<h2 class="">Salle <?php echo $salle->getInfo('titre')?></h2>
		</div>
		<div class="col-md-4">
			<h3 class="text-center">Photo</h3>
			<img class="img-responsive" src="<?php echo $salle->getInfo('photo') ?>" alt="Photo de la salle" />
		</div>
		<div class="col-md-4">
			<h3 class="text-center">Caractéristiques</h3>
			<p>Note moyenne :
				<?php
					if($salle->getNbAvis() > 0)
						echo $salle->getMoyenne() . '/10 <small>(' . $salle->getNbAvis() . ' avis)</small></p>';
					else
						echo '<em>Aucune note pour le moment.</em>';
				?>

			<p>Catégorie : <?php echo $salle->getInfo('categorie'); ?></p>
			<p>Capacité : <?php echo $salle->getInfo('capacite'); ?></p>
		</div>
		<div class="col-md-4">
			<h3 class="text-center">Description</h3>
			<p class="text-justify journal"><?php echo $salle->getInfo('description') ?></p>
		</div>
	</div>
</div>

	</div>

<div class="col-md-12">
<div class="row">
	<div class="col-md-6 fiche-produit">
		<?php
			if($produit->hasPromo())
				echo '<img src="images/promotion.png" class="img-promo-details" alt="En promotion" />';
		?>
		<h3 class="text-center">Informations complémentaires</h3>
		<p>Adresse : <?php echo $salle->getInfo('adresse'); ?></p>
		<p>Code postal : <?php echo $salle->getInfo('cp'); ?></p>
		<p>Ville : <?php echo $salle->getInfo('ville'); ?></p>
		<p>Pays : <?php echo $salle->getInfo('pays'); ?></p>
		<p>Date d'arrivée : <?php echo (new DateTime($produit->getInfo('date_arrivee')))->format('d/m/Y'); ?></p>
		<p>Date de départ : <?php echo (new DateTime($produit->getInfo('date_depart')))->format('d/m/Y'); ?></p>
		<p>
			Prix :
			<?php
				if($produit->hasPromo())
				{
					echo '<span style="text-decoration: line-through;">';
					echo sprintf("%.02f", round($produit->getInfo('prix'), 2));
					echo '</span>&nbsp;';

					$prix_reduit = $produit->getInfo('prix') - ($produit->getInfo('prix') * $produit->getPromotion()->getReduction() / 100);

					echo '<span style="color: red;"><strong>';
					echo sprintf("%.02f", round($prix_reduit, 2));
					echo '</strong></span>';
				}
				else
				{
					echo $produit->getInfo('prix');
				}

			?>
			€ HT
		</p>
		<p>Plan : </p><div id="map"></div>
	</div>

	<!-- Initialisation de la carte Google Maps pour localisation de la salle -->
	<script>
		function initMap() {
			var myLat = <?php echo $salle->getInfo('latitude'); ?>;
			var myLong = <?php echo $salle->getInfo('longitude') ?>;
			var myLatLng = {lat: myLat, lng: myLong};
			var mapDiv = document.getElementById('map');

			var map = new google.maps.Map(mapDiv, {
				center: {lat: myLat, lng: myLong},
				zoom: 12
			});

			var marker = new google.maps.Marker({
				position: myLatLng,
				map: map
			});
		};
	</script>
	<script src="https://maps.googleapis.com/maps/api/js?callback=initMap" async defer></script>

	<div class="col-md-offset-1 col-md-5 fiche-produit">
		<h3 class="text-center">Avis</h3>
		<div class="col-md-12 myAvis">
			<?php
			/* AFFIHAGE DES AVIS DE LA SALLE */
			
			foreach($avis as $avis_unite)
			{
				// On vérifie si l'utilisateur existe toujours dans la base
				if(!$controller->getMembre($avis_unite))
					$auteur = 'utilisateur désinscrit';
				else
					$auteur = $controller->getMembre($avis_unite)->getProperty('pseudo');

				echo '<p>';
				echo 'Par <strong>' . $auteur . '</strong>, <em>le ' . (new DateTime($avis_unite->getInfo('date')))->format('d-m-Y à H:i') . '</em> (' . $avis_unite->getInfo('note') . '/10)';
				echo '<br>';
				echo $avis_unite->getInfo('commentaire');
				echo '</p>';
			}
			?>
		</div>
		<hr/>
		<div class="col-md-12">
			<form action="reservation_details.php" method="post">
				<div class="form-group">
					<label for="commentaire" class="sr-only">Votre commentaire ici...</label>
					<textarea class="form-control" name="commentaire" id="commentaire" cols="10" rows="5" placeholder="Votre commentaire ici..."></textarea>
					<?php if(!$session->hasUser()) echo '<p class="help-block">Vous devez être connecté pour poster un commentaire.</p>'; ?>
				</div>

				<?php
				/* Si l'utilisateur est connecté, on affiche le champ commenentaire et le choix de la note */
				
					if($session->hasUser())
					{
						echo '<input type="hidden" name="id_salle" value="' . $salle->getInfo('id_salle') . '" />';
						echo '<input type="hidden" name="id_produit" value="' . $produit->getInfo('id_produit') . '" />';
						echo '<div class="form-group">';
						echo '<label for="note">Votre note</label>';
						echo '<select class="form-control" id="note" name="note">';

						for($i=1; $i<11; $i++)
						{
							$selected = $i==10 ? 'selected="selected"' : '';
							echo '<option value="'.$i.'" '.$selected.'>'.$i.'/10</option>';
						}

						echo '</select>';
						echo '</div>';

						echo '<div class="form-group">';
						echo '<button type="submit" name="post_commentaire" class="btn btn-primary pull-right">Poster</button>';
						echo '</div>';
					}
				?>
			</form>
		</div>
	</div>
</div>
</div>

<div class="row">
	<form action="reservation_details.php?id_produit=<?php echo $id_produit;?>" method="post" class="text-center">
		<?php
		/* Si l'utilisateur est connecté, et que le produit est disponible, on affiche le bouton d'ajout */
			if($session->hasUser())
			{
				if($produit->isAvailable())
					echo '<button name="ajout_panier" type="submit" class="btn btn-primary btn-lg">Ajouter au panier</button>';
				else
					echo '<button name="ajout_panier" type="submit" class="btn btn-primary btn-lg" disabled>Non disponible</button>';
			}
			else
			{
				echo '<p class="help-block">Vous devez être connecté pour ajouter un produit dans votre panier.</p>';
			}

		?>
	</form>
</div>

<div class="row"></div>


<div class="col-md-12">
<div class="row">
	<div class="col-md-12 fiche-produit">
		<div class="col-md-12"><h2 class="text-center">Suggestions</h2></div>
		<?php
			foreach($suggestions as $suggestion)
			{
				echo $controller->afficheProduit($suggestion);
			}
		?>
	</div>
</div>
</div>

<div class="row"></div>



<?php require_once('includes/footer.inc.php'); ?>