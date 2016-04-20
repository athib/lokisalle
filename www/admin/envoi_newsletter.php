<?php

require_once('../includes/init.inc.php');

/* ON VERIFIE QUE L'UTILISATEUR EST CONNECTE ET EST ADMIN */

if(!$session->hasUser() || ($session->hasUser() && !$session->getUser()->isAdmin()))
{
	$session->addFlashes(LKS_FLASH_ERROR, 'Vous devez être identifié en tant qu\'administrateur pour accéder à cette page.');
	$session->redirect('../connexion');

	exit();
}


/* INITIALISATION DES VARIABLES */

$user = $session->getUser();
$controller = new envoinewsController($session);
$form_ctrl = new formController($session);

$builder = new Builder();


/***********************************
 *  VERIFICATION DU FORMULAIRE 
 */
if($_POST)
{
	/* CONTROLE DU PSEUDO */

	if($form_ctrl->isEmptyField($_POST['pseudo']))
	{
		$form_ctrl->addError('pseudo');
		$session->addFlashes(LKS_FLASH_ERROR, 'Le pseudo est obligatoire');
	}
	elseif(!$form_ctrl->isValid($_POST['pseudo'], LKS_FORMAT_PSEUDO))
	{
		$form_ctrl->addError('pseudo');
		$session->addFlashes(LKS_FLASH_ERROR, LKS_FORMAT_PSEUDO_MSG);
	}

	/* CONTROLE DU SUJET */

	if($form_ctrl->isEmptyField($_POST['sujet']))
	{
		$form_ctrl->addError('sujet');
		$session->addFlashes(LKS_FLASH_ERROR, 'Le sujet est obligatoire.');
	}
	else
	{
		$_POST['sujet'] = htmlspecialchars($_POST['sujet']);
	}


	/* CONTROLE DU MESSAGE */

	if($form_ctrl->isEmptyField($_POST['message']))
	{
		$form_ctrl->addError('message');
		$session->addFlashes(LKS_FLASH_ERROR, 'Le message ne peut être vide.');
	}
	else
	{
		$_POST['message'] = htmlspecialchars($_POST['message']);
	}


	if(!$form_ctrl->hasError())
	{
		// Construction des en-têtes de l'email (format HTML, UTF8)
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset="utf-8"' . "\r\n";
		$headers .= 'From: ' . $_POST['pseudo'] . '<' . LKS_CONTACT . '>' . "\r\n";

		// On converti les retours à la ligne pour un affichage correcte dans l'email HTML
		$message = nl2br($_POST['message']);


		$emails_abonnes = $controller->getEmailsAbonnes();

		foreach($emails_abonnes as $key => $value)
		{
			mail($value, $_POST['sujet'], $message, $headers);
		}

		$controller->registerNewsletter($_POST['sujet'], $_POST['message']);

		$session->addFlashes(LKS_FLASH_OK, 'La newsletter a bien été envoyée.');
		$session->redirect('envoi_newsletter');
		exit();
	}
}

require_once('../includes/header.inc.php');

?>


<div class="row">
	<div class="col-md-12">
		<h1 class="text-center">Envoyer une newsletter</h1>
	</div>
</div>

<div class="row">
	<div class="col-md-12 text-center">
		<p>
			<?php
			// Affichage du nombre d'abonnées à la newsletter
			
				$nb_abonnes = $controller->getNbAbonnes();

				if($nb_abonnes > 1)
					echo "Actuellement, il y a <strong>$nb_abonnes membres</strong> abonnés à la newsletter.";
				elseif($nb_abonnes == 1)
					echo 'Actuellement, il y a <strong>un seul membre</strong> abonné à la newsletter';
				else
					echo 'Actuellement, il n\'y a <strong>aucun membre</strong> abonné à la newsletter.';
			?>
		</p>
	</div>
</div>


<div class="row">

	<form action="envoi_newsletter.php" method="post" class="col-md-offset-3 col-md-6">
		<?php
		$userdata = $session->getUser()->toArray();

		echo $builder->generateFormInput($userdata, $form_ctrl, 'text', 'pseudo', 'Votre pseudo', LKS_LABEL_HIDE);
		echo $builder->generateFormInput($_POST, $form_ctrl, 'text', 'sujet', 'Sujet de la news', LKS_LABEL_HIDE);
		?>

		<div class="form-group has-feedback <?php if($_POST) { if($form_ctrl->hasError('message')) echo 'has-error'; else echo 'has-success'; } ?>">
			<label for="message" class="sr-only">Votre message</label>
	        <textarea name="message" id="message" class="form-control" rows="5" placeholder="Votre message..."><?php
		        if($_POST)
		        {
			        if(!$form_ctrl->hasError('message'))
				        echo $_POST['message'];
		        }
		        ?></textarea>
		</div>

		<?php
		echo $builder->generateFormButton('envoyer_news', 'Envoyer la news', false);
		?>
	</form>

</div>




<?php require_once('../includes/footer.inc.php'); ?>