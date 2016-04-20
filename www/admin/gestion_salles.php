<?php

require_once('../includes/init.inc.php');


if(!$session->hasUser() || ($session->hasUser() && !$session->getUser()->isAdmin()))
{
	$session->addFlashes(LKS_FLASH_ERROR, 'Vous devez être identifié en tant qu\'administrateur pour accéder à cette page.');
	$session->redirect('../connexion');

	exit();
}

$user = $session->getUser();
$controller = new gestionsallesController($session);
$form_ctrl = new formController($session);
$builder = new Builder();
$salle_to_edit = null;
$update_photo = false;




if($_POST)
{
	/*********************************
	 * CONTROLE DU TITRE DE LA SALLE
	 */
	if($form_ctrl->isEmptyField($_POST['titre']))
	{
		$form_ctrl->addError('titre');
		$session->addFlashes(LKS_FLASH_ERROR, 'Le nom de la salle doit être indiqué.');
	}
	elseif(!$form_ctrl->isValid($_POST['titre'], LKS_FORMAT_NOM))
	{
		$form_ctrl->addError('titre');
		$session->addFlashes(LKS_FLASH_ERROR, LKS_FORMAT_NOM_MSG);
	}


	/*************************************
	 * CONTROLE DE L'ADRESSE DE LA SALLE
	 */
	if($form_ctrl->isEmptyField($_POST['adresse']))
	{
		$form_ctrl->addError('adresse');
		$session->addFlashes(LKS_FLASH_ERROR, 'L\'adresse de la salle doit être indiquée.');
	}
	elseif(!$form_ctrl->isValid($_POST['adresse'], LKS_FORMAT_ADRESSE))
	{
		$form_ctrl->addError('adresse');
		$session->addFlashes(LKS_FLASH_ERROR, LKS_FORMAT_ADRESSE_MSG);
	}


	/**************************************
	 * CONTROLE DU CODE POSTAL DE LA SALLE
	 */
	if($form_ctrl->isEmptyField($_POST['cp']))
	{
		$form_ctrl->addError('cp');
		$session->addFlashes(LKS_FLASH_ERROR, 'Le code postal doit être indiqué.');
	}
	elseif(!$form_ctrl->isValid($_POST['cp'], LKS_FORMAT_CP))
	{
		$form_ctrl->addError('cp');
		$session->addFlashes(LKS_FLASH_ERROR, LKS_FORMAT_CP_MSG);
	}

	/*************************************
	 * CONTROLE DE LA VILLE DE LA SALLE
	 */
	if($form_ctrl->isEmptyField($_POST['ville']))
	{
		$form_ctrl->addError('ville');
		$session->addFlashes(LKS_FLASH_ERROR, 'La ville doit être indiquée.');
	}
	elseif(!$form_ctrl->isValid($_POST['ville'], LKS_FORMAT_VILLE))
	{
		$form_ctrl->addError('ville');
		$session->addFlashes(LKS_FLASH_ERROR, LKS_FORMAT_VILLE_MSG);
	}

	/*************************************
	 * CONTROLE DU PAYS DE LA SALLE
	 */
	if($form_ctrl->isEmptyField($_POST['pays']))
	{
		$form_ctrl->addError('pays');
		$session->addFlashes(LKS_FLASH_ERROR, 'Le pays doit être indiqué.');
	}
	elseif(!$form_ctrl->isValid($_POST['pays'], LKS_FORMAT_PAYS))
	{
		$form_ctrl->addError('pays');
		$session->addFlashes(LKS_FLASH_ERROR, LKS_FORMAT_PAYS_MSG);
	}

	/*************************************
	 * CONTROLE DE LA LATITUDE LA SALLE
	 */
	if(!$form_ctrl->isEmptyField($_POST['latitude']) && !$form_ctrl->isValid($_POST['latitude'], LKS_FORMAT_COORDS))
	{
		$form_ctrl->addError('latitude');
		$session->addFlashes(LKS_FLASH_ERROR, LKS_FORMAT_COORDS_MSG);
	}

	/*************************************
	 * CONTROLE DE LA LONGITUDE DE LA SALLE
	 */
	if(!$form_ctrl->isEmptyField($_POST['longitude']) && !$form_ctrl->isValid($_POST['longitude'], LKS_FORMAT_COORDS))
	{
		$form_ctrl->addError('longitude');
		$session->addFlashes(LKS_FLASH_ERROR, LKS_FORMAT_COORDS_MSG);
	}

	/*************************************
	 * CONTROLE DE LA CATEGORIE DE LA SALLE
	 */
	// Pas de test car menu déroulant pré-rempli depuis la base de données.

	/*************************************
	 * CONTROLE DE LA CAPACITE DE LA SALLE
	 */
	if($form_ctrl->isEmptyField($_POST['capacite']))
	{
		$form_ctrl->addError('capacite');
		$session->addFlashes(LKS_FLASH_ERROR, 'La capacité de la salle doit être indiquée.');
	}
	elseif(!$form_ctrl->isValid($_POST['capacite'], '#^\d+$#'))
	{
		$form_ctrl->addError('capacite');
		$session->addFlashes(LKS_FLASH_ERROR, 'La capacité doit être un nombre entier positif.');
	}

	/*************************************
	 * CONTROLE DE LA PHOTO DE LA SALLE
	 */
	if(isset($_POST['ajouter_salle']) && !$form_ctrl->hasPhotoSelected())
	{
		$form_ctrl->addError('photo');
		$update_photo = true;
		$session->addFlashes(LKS_FLASH_ERROR, 'La photo est obligatoire lors de l\'ajout d\'une salle');
	}
	elseif(isset($_POST['modifier_salle']) && $form_ctrl->hasPhotoSelected())
	{
		$update_photo = true;
	}



	/*************************************
	 * CONTROLE DE LA DESCRIPTION DE LA SALLE
	 */
	if($form_ctrl->isEmptyField($_POST['description']))
	{
		$form_ctrl->addError('description');
		$session->addFlashes(LKS_FLASH_ERROR, 'La description de la salle est obligatoire.');
	}
	elseif(!$form_ctrl->isValid($_POST['description'], LKS_FORMAT_DESCRIPTION))
	{
		$form_ctrl->addError('description');
		$session->addFlashes(LKS_FLASH_ERROR, LKS_FORMAT_DESCRIPTION_MSG);
	}
	else
		$_POST['description'] = nl2br(htmlspecialchars($_POST['description']));


	/* S'IL Y A DES ERREURS DANS LE FORMULAIRE */

	if($form_ctrl->hasError())
	{
		if(isset($_POST['modifier_salle']))
		{
			$_GET['action'] = 'edit';
			$_GET['id_salle'] = $_POST['id_salle'];
		}
		elseif(isset($_POST['ajouter_salle']))
		{
			$_GET['action'] = 'add';
		}
	}
	else
	{
		/* ON ATTRIBUE DES COORDONNEES PAR DEFAUT SI L'UTILISATEUR N'A RIEN SAISI */

		if($form_ctrl->isEmptyField($_POST['latitude']) || $form_ctrl->isEmptyField($_POST['longitude']))
		{
			switch($_POST['ville'])
			{
				case 'Marseille' :
					$_POST['latitude'] = MARSEILLE_LAT;
					$_POST['longitude'] = MARSEILLE_LONG;
					$ville_default = 'Marseille';
					break;

				case 'Lyon' :
					$_POST['latitude'] = LYON_LAT;
					$_POST['longitude'] = LYON_LONG;
					$ville_default = 'Lyon';
					break;

				default :
					$_POST['latitude'] = PARIS_LAT;
					$_POST['longitude'] = PARIS_LONG;
					$ville_default = 'Paris';

				$session->addFlashes(LKS_FLASH_WARNING, 'Les coordonnées n\'ayant pas été indiquées, celle de <strong>' . $ville_default . '</strong> ont été enregistrées par défaut.');
			}
		}


		/* ENREGISTREMENT EN BASE DE DONNEES SELON L'ACTION (AJOUT ou MODIF) */

		if(isset($_POST['modifier_salle']))
		{
			// UPDATE table
			$nouvelle_salle = new Salle($_POST);
			$controller->modifierSalle($nouvelle_salle);

			if($update_photo)
			{
				$controller->uploadPhoto($_POST['id_salle'], $nouvelle_salle->getInfo('titre'));
			}
		}
		elseif(isset($_POST['ajouter_salle']))
		{
			$salle_to_insert = new Salle($_POST);
			$last_id = $controller->ajouterSalle($salle_to_insert);
			$controller->uploadPhoto($last_id, $salle_to_insert->getInfo('titre'));
		}


		$session->addFlashes(LKS_FLASH_OK, 'La salle a été correctement ajoutée.');
	}
}


/**************************
 * SUPPRESSION D'UNE SALLE
 **************************/
if($controller->getGetParam('action') == 'delete')
{
	$id_salle = $controller->getGetParam('id_salle');

	if(!$id_salle)
		$session->addFlashes(LKS_FLASH_ERROR, 'Vous devez indiquer une salle à supprimer.');
	else
	{
		if(!$controller->salleExists($id_salle))
			$session->addFlashes(LKS_FLASH_ERROR, 'Cette salle n\'existe pas.');
		else
		{
			$controller->deleteSalle($id_salle);
			$session->addFlashes(LKS_FLASH_OK, 'La salle a bien été supprimée.');
		}

		$session->redirect('gestion_salles');
		exit();
	}
}
/************************
 * EDITION D'UNE SALLE
 ************************/
elseif($controller->getGetParam('action') == 'edit')
{
	$id_salle = $controller->getGetParam('id_salle');

	$salle_to_edit = $controller->getSalleById($id_salle);
}


require_once('../includes/header.inc.php');

?>


<div class="row">
	<div class="col-md-12 text-center">
		<h1 class="text-center">Gestion des salles</h1>
		<?php
			if(!$controller->getGetParam('action'))
				echo '<h4><a href="gestion_salles.php?action=add"><em>Ajouter une salle</em></a></h4>';
		?>
		<br>
	</div>
</div>



<?php
if($controller->getGetParam('action'))
{
	$liste_categories = $controller->getAllCategoriesFromDb();

	if($salle_to_edit)
	{
		$salledata = $salle_to_edit->toArray();
		$title_form = 'Modifier la salle <strong>' . $salle_to_edit->getInfo('titre') . '</strong>';
		$title_button = 'Modifier la salle';
		$name_button = 'modifier_salle';
	}
	elseif($controller->getGetParam('action') == 'add')
	{
		$salledata = $_POST;
		$title_form = 'Ajouter une salle';
		$title_button = 'Ajouter la salle';
		$name_button = 'ajouter_salle';
	}

?>

	<div class="row">
	<div class="col-md-12">
		<form action="gestion_salles.php" method="post" class="col-md-12" enctype="multipart/form-data">
			<fieldset>
				<legend><?php echo $title_form; ?></legend>

				<?php
					if($salle_to_edit)
						echo '<input type="hidden" name="id_salle" value="' . $salle_to_edit->getInfo('id_salle') . '" />';
				?>

				<div class="col-md-6">
					<?php
					echo $builder->generateFormInput($salledata, $form_ctrl, 'text', 'titre', 'Nom de la salle');
					echo $builder->generateFormInput($salledata, $form_ctrl, 'text', 'adresse', 'Adresse de la salle');
					echo $builder->generateFormInput($salledata, $form_ctrl, 'text', 'cp', 'Code postal');
					echo $builder->generateFormInput($salledata, $form_ctrl, 'text', 'ville', 'Ville');
					echo $builder->generateFormInput($salledata, $form_ctrl, 'text', 'pays', 'Pays');
					?>
				</div>

				<div class="col-md-6">
					<?php
					echo $builder->generateFormInput($salledata, $form_ctrl, 'text', 'latitude', 'Latitude');
					echo $builder->generateFormInput($salledata, $form_ctrl, 'text', 'longitude', 'Longitude');

					if($salle_to_edit)
						$option_to_check = $salle_to_edit->getInfo('categorie');
					else
						$option_to_check = null;
					echo $builder->generateFormSelect('categorie', 'Catégorie de la salle', $liste_categories, $option_to_check);
					echo $builder->generateFormInput($salledata, $form_ctrl, 'text', 'capacite', 'Capacité');
					echo $builder->generateFormInput(null, null, 'file', 'photo', 'Photo');
					?>
				</div>

				<div class="form-group col-md-12 form-group has-feedback <?php if($_POST) { if($form_ctrl->hasError('description')) echo 'has-error'; else echo 'has-success'; } ?>">
					<label for="description" class="sr-only">Description</label>
					<textarea name="description" id="description" class="form-control" rows="5" placeholder="Description de la salle"><?php if($salle_to_edit) echo str_replace('<br />', '', $salle_to_edit->getInfo('description')); ?></textarea>
				</div>

				<div class="form-group col-md 12 text-center">
					<?php
					echo $builder->generateFormButton($name_button, $title_button, true);
					?>
					<a href="gestion_salles.php"><br><br><em>Retour à la liste des salles</em></a>
				</div>


			</fieldset>
		</form>
	</div>
</div>
<?php } else { ?>

<div class="row">
	<div class="col-md-12">
		<?php
			$title = 'Liste des salles';
			$entetes = array(
				'id_salle' => 'ID',
				'titre' => 'Titre',
				'adresse' => 'Adresse',
				'ville' => 'Ville',
				'cp' => 'CP',
				'pays' => 'Pays',
				'latitude' => 'Lat.',
				'longitude' => 'Long.',
				'categorie' => 'Catégorie',
				'capacite' => 'Capacité',
				'photo' => 'Photo',
				'description' => 'Description'
			);

			$salles = $controller->getAllSalles();


			echo $builder->generateTable($title, $entetes, $salles, true, true, false);
		?>
	</div>
</div>

<?php } ?>


<?php require_once('../includes/footer.inc.php'); ?>