<?php

require_once('includes/init.inc.php');

$controller = new mdpperduController($session);
$form_ctrl = new formController($session);

/******************************
 * CONTROLE DU FORMULAIRE
 */
if($_POST)
{
	/* VERIFICATION DU CHAMP EMAIL */
	
	if($form_ctrl->isEmptyField($_POST['email']))
	{
		$form_ctrl->addError('email');
		$session->addFlashes(LKS_FLASH_ERROR, 'Vous devez saisir une adresse email');
	}
	elseif(!$form_ctrl->isValid($_POST['email'], LKS_FORMAT_EMAIL))
	{
		$form_ctrl->addError('email');
		$session->addFlashes(LKS_FLASH_ERROR, LKS_FORMAT_EMAIL_MSG);
	}


	/* SI LE FORMULAIRE NE CONTIENT AUCUNE ERREUR */
	
	if(!$form_ctrl->hasError())
	{
		$new_password = $controller->genererMDP();

		$user = $controller->updateDBPassword($_POST['email'], $new_password);

		$controller->envoyerMail($user, $new_password);

		$session->redirect('connexion');
		exit();
	}
}



require_once('includes/header.inc.php');


?>

<div class="row">
	<div class="col-md-12">
		<h1 class="text-center">Récupération de mot de passe</h1>
	</div>
</div>

<div class="row">
	<form action="mdpperdu.php" method="post" class="col-md-offset-3 col-md-6">
		<?php
		/* GENERATION DU FORMULAIRE */
		$builder = new Builder();

		echo $builder->generateFormInput($_POST, $form_ctrl, 'email', 'email', 'Votre email', LKS_LABEL_HIDE);

		echo '<p class="text-center">';
		echo $builder->generateFormButton('recup_mdp', 'Récupérer');
		echo '<p>';
		?>
	</form>
</div>



<?php require_once('includes/footer.inc.php'); ?>