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
$controller = new gestionproduitsController($session);
$form_ctrl = new formController($session);
$builder = new Builder();
$produit_to_edit = null;

// options de tri par défaut
$champ_a_trier = 'id_produit'; // champ de tri par défaut
$ordre_tri = 'desc'; // ordre de tri par défaut.


/***********************************
 * Vérification du formulaire
 */
if($_POST)
{
	/**********************************
	 * CONTROLE DE LA DATE D'ARRIVEE
	 */
	if($form_ctrl->isEmptyField($_POST['date_arrivee']))
	{
		$form_ctrl->addError('date_arrivee');
		$session->addFlashes(LKS_FLASH_ERROR, 'La date d\'arrivée est obligatoire.');
	}
	elseif(!$form_ctrl->isValid($_POST['date_arrivee'], LKS_FORMAT_DATE))
	{
		$form_ctrl->addError('date_arrivee');
		$session->addFlashes(LKS_FLASH_ERROR, LKS_FORMAT_DATE_MSG);
	}
	elseif(!$form_ctrl->laterThanToday($_POST['date_arrivee']))
	{
		$form_ctrl->addError('date_arrivee');
		$session->addFlashes(LKS_FLASH_ERROR, 'La date d\'arrivée ne peut être antérieure à aujourd\'hui.');
	}


	/**********************************
	 * CONTROLE DE LA DATE DE DEPART
	 */
	if($form_ctrl->isEmptyField($_POST['date_depart']))
	{
		$form_ctrl->addError('date_depart');
		$session->addFlashes(LKS_FLASH_ERROR, 'La date de départ est obligatoire.');
	}
	elseif(!$form_ctrl->isValid($_POST['date_depart'], LKS_FORMAT_DATE))
	{
		$form_ctrl->addError('date_depart');
		$session->addFlashes(LKS_FLASH_ERROR, LKS_FORMAT_DATE_MSG);
	}
	elseif(!$form_ctrl->laterThanDate($_POST['date_depart'], $_POST['date_arrivee']))
	{
		$form_ctrl->addError('date_depart');
		$session->addFlashes(LKS_FLASH_ERROR, 'La date de départ doit être postérieure à la date du jour.');
	}


	/**********************************
	 * CONTROLE DE LA PROMO
	 */
	if($_POST['id_promo'] == 0)
		$_POST['id_promo'] = null;


	/********************
	 * CONTROLE DU PRIX
	 */
	if($form_ctrl->isEmptyField($_POST['prix']))
	{
		$form_ctrl->addError('prix');
		$session->addFlashes(LKS_FLASH_ERROR, 'Le prix est obligatoire.');
	}
	elseif(!$form_ctrl->isValid($_POST['prix'], '#^\d+$#'))
	{
		$form_ctrl->addError('prix');
		$session->addFlashes(LKS_FLASH_ERROR, 'Le prix doit être un entier positif composé de 1 ou plusieurs chiffres.');
	}


	/********************************************
	 * CONTROLE DE LA DISPONIBILITE DE LA SALLE
	 * (On ne doit pas pouvoir créer un produit avec la même
	 * salle et des dates qui se chevauchent)
	 */
	$produit = $controller->salleOccupee($_POST['id_produit'], $_POST['id_salle'], $_POST['date_arrivee'], $_POST['date_depart']);
	if($produit)
	{
		$form_ctrl->addError('date_arrivee');
		$form_ctrl->addError('date_depart');
		$session->addFlashes(LKS_FLASH_ERROR, 'Ces dates ne sont pas disponibles pour cette salle.');
		$session->addFlashes(LKS_FLASH_ERROR, 'Cf. Produit ' . $produit->getInfo('id_produit') . ' du ' . $produit->getInfo('date_arrivee') . ' au ' . $produit->getInfo('date_depart') . '.');
	}


	// Si le formulaire contient des erreurs
	if($form_ctrl->hasError())
	{
		if(isset($_POST['modifier_produit']))
		{
			$_GET['action'] = 'edit';
			$_GET['id_produit'] = $_POST['id_produit'];
		}
		elseif(isset($_POST['ajouter_produit']))
		{
			$_GET['action'] = 'add';
		}
	}
	else
	{
		// Pas d'erreur et cas de MODIFICATION
		if(isset($_POST['modifier_produit']))
		{
			$controller->modifierProduit($_POST);
			$session->addFlashes(LKS_FLASH_OK, 'Le produit a bien été modifié.');

		}
		// Pas d'erreur et cas d'AJOUT
		elseif(isset($_POST['ajouter_produit']))
		{
			$controller->ajouterProduit($_POST);
			$session->addFlashes(LKS_FLASH_OK, 'Le produit a bien été ajouté.');
		}

		$session->redirect('gestion_produits');
		exit();
	}
}


/***************************
 * SUPPRESSION D'UN PRODUIT
 ***************************/
if($controller->getGetParam('action') == 'delete')
{
	$id_produit = $controller->getGetParam('id_produit');

	if(!$id_produit)
		$session->addFlashes(LKS_FLASH_ERROR, 'Vous devez indiquer un produit à supprimer.');
	else
	{
		if(!$controller->produitExists($id_produit))
			$session->addFlashes(LKS_FLASH_ERROR, 'Ce produit n\'existe pas.');
		else
		{
			$controller->deleteProduit($id_produit);
			$session->addFlashes(LKS_FLASH_OK, 'Le produit a bien été supprimé.');
		}

		$session->redirect('gestion_produits');
		exit();
	}
}
/************************
 * EDITION D'UN PRODUIT
 ************************/
elseif($controller->getGetParam('action') == 'edit')
{
	$id_produit = $controller->getGetParam('id_produit');

	$produit_to_edit = $controller->getProduitById($id_produit);
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
		$session->addFlashes(LKS_FLASH_WARNING, 'L\'ordre de tri spécifié est incorrect. Ordre croissant appliqué par défault.');
		$ordre_tri = 'ASC';
	}
}

$liste_produits = $controller->getAllProduits($champ_a_trier, $ordre_tri);

require_once('../includes/header.inc.php');

?>


<div class="row">
	<div class="col-md-12 text-center">
		<h1 class="text-center">Gestion des produits</h1>
		<?php
		if(!$controller->getGetParam('action'))
			echo '<h4><a href="gestion_produits.php?action=add"><em>Ajouter un produit</em></a></h4>';
		?>
		<br>
	</div>
</div>

<?php
if($controller->getGetParam('action'))
{
	if($controller->getGetParam('action') == 'add')
	{
		$donnees_produit = $_POST;
		$title_form = 'Ajouter un produit';
		$title_button = 'Ajouter le produit';
		$name_button = 'ajouter_produit';
	}
	elseif($produit_to_edit)
	{
		$donnees_produit = $produit_to_edit->toArray();
		$title_form = 'Modifier le produit <strong>' . $produit_to_edit->getInfo('id_produit') . '</strong>';
		$title_button = 'Modifier le produit';
		$name_button = 'modifier_produit';
	}

?>

<div class="row">
	<div class="col-md-12">
		<form action="gestion_produits.php" method="post" class="col-md-offset-3 col-md-6">
			<fieldset>
				<legend><?php echo $title_form; ?></legend>

				<?php
				if($produit_to_edit)
					echo '<input type="hidden" name="id_produit" value="' . $produit_to_edit->getInfo('id_produit') . '" />';
				?>

				<div class="row">
					<?php
					$liste_salles = $controller->getAllSallesFromDb();
					$liste_promotions = $controller->getAllPromoFromDb();

					if($produit_to_edit)
					{
						$salle_to_check = $produit_to_edit->getInfo('id_salle') . ' - ' . $produit_to_edit->getSalle()->getInfo('titre') . ' (' . $produit_to_edit->getSalle()->getInfo('ville') . ')';
						$promo_to_check = $produit_to_edit->getPromotion() ? $produit_to_edit->getPromotion()->getCodePromo() : null;
						$etat_to_check = $produit_to_edit->getInfo('etat');
					}
					elseif($_POST)
					{
						$salle_to_check = $_POST['id_salle'];
						$promo_to_check = $_POST['id_promo'];
						$etat_to_check = $_POST['etat'];
					}
					else
					{
						$salle_to_check = null;
						$promo_to_check = null;
						$etat_to_check = 0;
					}

					echo $builder->generateFormInput($donnees_produit, $form_ctrl, 'text', 'date_arrivee', 'Date d\'arrivée (AAAA-MM-DD)', LKS_LABEL_HIDE);
					echo $builder->generateFormInput($donnees_produit, $form_ctrl, 'text', 'date_depart', 'Date de départ (AAAA-MM-DD)', LKS_LABEL_HIDE);
					echo $builder->generateFormSelect('id_salle', 'Choix de la salle', $liste_salles, $salle_to_check);
					echo $builder->generateFormSelect('id_promo', 'Choix de la promotion', $liste_promotions, $promo_to_check);
					echo $builder->generateFormInput($donnees_produit, $form_ctrl, 'text', 'prix', 'Prix', LKS_LABEL_HIDE);
					echo $builder->generateFormRadio('etat', 'Produit déjà réservé ? &nbsp; ', unserialize(LKS_DATA_YES_NO), $etat_to_check);
					?>
				</div>

				<div class="row text-right">

					<?php
					echo $builder->generateFormButton($name_button, $title_button, true);
					?>
					<a href="gestion_produits.php"><br><br><em>Retour à la liste des produits</em></a>

				</div>


			</fieldset>
		</form>
	</div>
</div>

<?php } else { ?>

<div class="row">
	<div class="col-md-12">
		<?php
		$title = 'Liste des produits';
		$entetes = array(
			'id_produit' => 'ID',
			'date_arrivee' => 'Arrivée',
			'date_depart' => 'Départ',
			'id_salle' => 'ID salle',
			'titre' => 'Salle',
			'ville' => 'Ville',
			'code_promo' => 'Promo',
			'prix' => 'Prix (€)',
			'etat' => 'Etat'
		);

		echo $builder->generateTable($title, $entetes, $liste_produits, true, true, '200', true);
		?>
	</div>
</div>

<?php } ?>

<?php // On génère des DatePicker pour les champs date
if($controller->getGetParam('action') == 'add' || $controller->getGetParam('action') == 'edit') { ?>
	<script src="<?php echo RACINE_SITE; ?>js/jquery-2.2.0.js"></script>
	<script>
		$(function(){
			$('#date_arrivee').datepicker({
				dateFormat : 'yy-mm-dd',
				firstDay : 1,
				minDate : 0,
				monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
				dayNamesMin: ['D', 'L', 'M', 'M', 'J', 'V', 'S']
			});

			$('#date_depart').datepicker({
				dateFormat : 'yy-mm-dd',
				firstDay : 1,
				minDate : 1,
				monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
				dayNamesMin: ['D', 'L', 'M', 'M', 'J', 'V', 'S']
			});
		});
	</script>
<?php } ?>

<?php require_once('../includes/footer.inc.php'); ?>