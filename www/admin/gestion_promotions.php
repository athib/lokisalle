<?php

require_once('../includes/init.inc.php');

// VERIFICATION DES DROITS D'ACCES A LA PAGE
if(!$session->hasUser() || ($session->hasUser() && !$session->getUser()->isAdmin()))
{
	$session->addFlashes(LKS_FLASH_ERROR, 'Vous devez être identifié en tant qu\'administrateur pour accéder à cette page.');
	$session->redirect('../connexion');

	exit();
}


// INITIALISATION DES VARIABLES

$user = $session->getUser();
$controller = new gestionpromotionsController($session);
$form_ctrl = new formController($session);
$builder = new Builder();
$promotion_to_edit = null;


/************************
 * SUPPRESSION D'UN CODE
 ************************/
if($controller->getGetParam('action') == 'delete')
{
	$id_code = $controller->getGetParam('id_promo');

	if(!$id_code)
		$session->addFlashes(LKS_FLASH_ERROR, 'Vous devez indiquer un code promo à supprimer.');
	else
	{
		if($controller->promoExists($id_code))
			$session->addFlashes(LKS_FLASH_ERROR, 'Ce code promo n\'existe pas.');
		else
		{
			$controller->deletePromo($id_code);
			$session->addFlashes(LKS_FLASH_OK, 'Le code promo a bien été supprimé.');

			$session->redirect('gestion_promotions');
			exit();
		}
	}
}


/************************
 * EDITION D'UN CODE
 ************************/
if($controller->getGetParam('action') == 'edit')
{
	$id_promo = $controller->getGetParam('id_promo');

	$promotion_to_edit = $controller->getPromoById($id_promo);
}



/************************************************
 * CONTROLE PUIS AJOUT OU MODIFICATION D'UN CODE
 ************************************************/
if($_POST)
{
	// On transforme le code saisi en majuscule
	$_POST['code_promo'] = strtoupper($_POST['code_promo']);

	/* CONTROLE DU FORMAT DU CODE */
	if($form_ctrl->isEmptyField($_POST['code_promo']))
	{
		$form_ctrl->addError('code_promo');
		$session->addFlashes(LKS_FLASH_ERROR, 'Vous ne pouvez pas insérer un code promo vide.');
	}
	elseif(!$form_ctrl->isValid($_POST['code_promo'], LKS_FORMAT_CODE_PROMO))
	{
		$form_ctrl->addError('code_promo');
		$session->addFlashes(LKS_FLASH_ERROR, LKS_FORMAT_CODE_PROMO_MSG);
	}
	elseif($controller->promoExists($_POST['code_promo']))
	{
		$form_ctrl->addError('code_promo');
		$session->addFlashes(LKS_FLASH_ERROR, 'Ce code promo existe déjà.');
	}

	/* CONTROLE DU MONTANT DE LA REDUCTION */
	if($form_ctrl->isEmptyField($_POST['reduction']))
	{
		$form_ctrl->addError('reduction');
		$session->addFlashes(LKS_FLASH_ERROR, 'Vous indiquer le montant de la réduction.');
	}
	elseif(!$form_ctrl->isValid($_POST['reduction'], '#^[\d]{1,3}$#'))
	{
		$form_ctrl->addError('reduction');
		$session->addFlashes(LKS_FLASH_ERROR, 'La réduction doit être un nombre entier de 1 à 3 chiffres inférieur à 100');
	}
	elseif($_POST['reduction'] < 0 || $_POST['reduction'] >100)
	{
		$form_ctrl->addError('reduction');
		$session->addFlashes(LKS_FLASH_ERROR, 'La réduction doit être comprise entre 0 et 100');
	}


	// Si le formulaire ne contient aucune erreur
	if(!$form_ctrl->hasError())
	{
		if(isset($_POST['ajouter_promo']))
		{
			$controller->ajouterPromo($_POST['code_promo'], $_POST['reduction']);
			$session->addFlashes(LKS_FLASH_OK, 'Le code promo a bien été ajouté.');
		}
		elseif(isset($_POST['modifier_promo']))
		{
			$controller->modifierPromo($_POST['id_promo'], $_POST['code_promo'], $_POST['reduction']);
			$session->addFlashes(LKS_FLASH_OK, 'Le code promo a bien été modifié.');
		}

		$session->redirect('gestion_promotions');
		exit();
	}
}



require_once('../includes/header.inc.php');

?>


<div class="row">
	<div class="col-md-12">
		<h1 class="text-center">Gestion des promotions</h1>
	</div>
</div>

<div class="row">
	<div class="col-md-offset-2 col-md-8">
		<?php
		$title = 'Liste des promotions';
		$entetes = array('ID', 'Code promo', 'Réduction');
		$promotions = $controller->getAllPromos();

		echo $builder->generateTable($title, $entetes, $promotions, true, true, false);
		?>
	</div>
</div>

<div class="row">

</div>

<div class="row">
	<div class="col-md-12">
		<form action="gestion_promotions.php" method="post" class="form-inline">
			<fieldset>
			<?php 
			// EDITION D'UN CODE PROMO
			
			if($promotion_to_edit)
			{
				$promodata = $promotion_to_edit->toArray();
				echo '<legend>Modifier un code promo</legend>';
				echo '<input type="hidden" name="id_promo" value="' . $promotion_to_edit->getId() . '" />';
				echo $builder->generateFormInput($promodata, $form_ctrl, 'text', 'code_promo', 'Code promotion', LKS_LABEL_HIDE);
				echo $builder->generateFormInput($promodata, $form_ctrl, 'text', 'reduction', 'Réduction', LKS_LABEL_HIDE);
				echo $builder->generateFormButton('modifier_promo', 'Modifier');
			}
			else
			{
				echo '<legend>Ajouter un code promo</legend>';
				echo $builder->generateFormInput($_POST, $form_ctrl, 'text', 'code_promo', 'Code promotion', LKS_LABEL_HIDE);
				echo $builder->generateFormInput($_POST, $form_ctrl, 'text', 'reduction', 'Réduction', LKS_LABEL_HIDE);
				echo $builder->generateFormButton('ajouter_promo', 'Ajouter');
			}
			?>
			</fieldset>
		</form>
	</div>
</div>



<?php require_once('../includes/footer.inc.php'); ?>