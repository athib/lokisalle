<?php
require_once('includes/init.inc.php');

$controller = new rechercheController($session);

/* VERIFICATION DU FORMULAIRE */
if($_POST)
{
	/* VERIFICATION DU FORMAT DE LA DATE */
	
	if($controller->isEmptyField($_POST['date_recherche']))
	{
		$controller->addError('date_recherche');
		$session->addFlashes(LKS_FLASH_ERROR, 'Vous devez saisir une date');
	}
	elseif(!$controller->isValid($_POST['date_recherche'], LKS_FORMAT_DATE))
	{
		$controller->addError('date_recherche');
		$session->addFlashes(LKS_FLASH_ERROR, LKS_FORMAT_DATE_MSG);
	}
	elseif(!$controller->isAfterToday($_POST['date_recherche']))
	{
		$controller->addError('date_recherche');
		$session->addFlashes(LKS_FLASH_ERROR, 'La date saisie doit être supérieure à la date actuelle.');
	}
	else
	{
		// ON récupère les produits correspondants aux critères de recherche
		$produits = $controller->getProduitsByDateAndVille($_POST['date_recherche'], $_POST['ville_recherche']);

		if(empty($produits))
		{
			$session->addFlashes(LKS_FLASH_ERROR, 'Aucune salle ne correspond à votre recherche.');
			$controller->addError('recherche');
		}
		else
			$session->addFlashes(LKS_FLASH_OK, 'Une ou plusieurs salles correspondent à votre recherche.');
	}
}



require_once('includes/header.inc.php');

?>


<div class="row">
	<div class="col-md-12">
		<h1 class="text-center">Rechercher une salle</h1>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<form method="post" class="form-inline">
			<?php
			/* AFFICHAGE DU FORMULAIRE DE RECHERCHE */
			
				$builder = new Builder();
				$liste_villes = $controller->getAllVillesFromDb();

				echo $builder->generateFormInput($_POST, $controller, 'text', 'date_recherche', 'Date d\'arrivée', LKS_LABEL_HIDE);
				echo $builder->generateFormSelect('ville_recherche', 'Choix de la ville', $liste_villes, 'Paris', LKS_LABEL_HIDE);
			?>


			<button type="submit" class="btn btn-primary">Rechercher</button>
		</form>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<?php
		/* SI ON A DEJA POSTE UNE RECHERCHE, ON AFFICHE LES PRODUITS */
			if($_POST)
			{
				if(!$controller->hasError())
					echo $controller->afficheProduits3x3($produits);
			}
		?>
	</div>
</div>


<script src="<?php echo RACINE_SITE; ?>js/jquery-2.2.0.js"></script>
<script>
	$(function(){
		$('#date_recherche').datepicker({
			dateFormat : 'yy-mm-dd',
			firstDay : 1,
			minDate : 0,
			monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
			dayNamesMin: ['D', 'L', 'M', 'M', 'J', 'V', 'S'],
		});
	});
</script>


<?php require_once('includes/footer.inc.php') ?>