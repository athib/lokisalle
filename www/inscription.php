<?php

require_once('includes/init.inc.php');

$controller = new inscriptionController($session);

/********************************
* Vérification du formulaire
*********************************/
if($_POST)
{
    /**********************************
    * Contrôle de la saisie du pseudo *
    **********************************/
    if($controller->isEmptyField($_POST['pseudo']))
    {
        $controller->addError('pseudo');
        $session->addFlashes(LKS_FLASH_ERROR, 'Le champ "pseudo" est obligatoire.');
    }
    elseif(!$controller->isValid($_POST['pseudo'], LKS_FORMAT_PSEUDO))
    {
        $controller->addError('pseudo');
        $session->addFlashes(LKS_FLASH_ERROR, LKS_FORMAT_PSEUDO_MSG);
    }
    elseif(!$controller->canUsePseudo($_POST['pseudo']))
    {
        $controller->addError('pseudo');
        $session->addFlashes(LKS_FLASH_ERROR, 'Ce pseudo est déjà utilisé.');
    }

    /****************************************
    * Contrôle de la saisie du mot de passe *
    *****************************************/
    if($controller->isEmptyField($_POST['password']) || $controller->isEmptyField($_POST['password_confirm']))
    {
        $controller->addError('password');
        $session->addFlashes(LKS_FLASH_ERROR, 'Vous devez saisir un mot de passe puis le confirmer.');
    }
    elseif(!$controller->isValid($_POST['password'], LKS_FORMAT_PASSWORD))
    {
        $controller->addError('password');
        $session->addFlashes(LKS_FLASH_ERROR, LKS_FORMAT_PASSWORD_MSG);
    }
    elseif(!$controller->isPasswordConfirmed($_POST['password'], $_POST['password_confirm']))
    {
        $controller->addError('password');
        $session->addFlashes(LKS_FLASH_ERROR, 'Le mot de passe et sa confirmation doivent être identiques.');
    }

    /***********************************
    * Contrôle de la saisie de l'email *
    ************************************/
    if($controller->isEmptyField($_POST['email']))
    {
        $controller->addError('email');
        $session->addFlashes(LKS_FLASH_ERROR, 'Le champ "e-mail" est obligatoire.');
    }
    elseif(!$controller->isValid($_POST['email'], LKS_FORMAT_EMAIL))
    {
        $controller->addError('email');
        $session->addFlashes(LKS_FLASH_ERROR, LKS_FORMAT_EMAIL_MSG);
    }
    elseif(!$controller->canUseEmail($_POST['email']))
    {
        $controller->addError('email');
        $session->addFlashes(LKS_FLASH_ERROR, 'Cet email est déjà utilisé.');
    }

    /**********************************
    * Contrôle de la saisie du prénom *
    ***********************************/
    if($controller->isEmptyField($_POST['prenom']))
    {
        $controller->addError('prenom');
        $session->addFlashes(LKS_FLASH_ERROR, 'Le champ "prénom" est obligatoire.');
    }
    elseif(!$controller->isValid($_POST['prenom'], LKS_FORMAT_PRENOM))
    {
        $controller->addError('prenom');
        $session->addFlashes(LKS_FLASH_ERROR, LKS_FORMAT_PRENOM_MSG);
    }

    /*******************************
    * Contrôle de la saisie du nom *
    ********************************/
    if($controller->isEmptyField($_POST['nom']))
    {
        $controller->addError('nom');
        $session->addFlashes(LKS_FLASH_ERROR, 'Le champ "nom" est obligatoire');
    }
    elseif(!$controller->isValid($_POST['nom'], LKS_FORMAT_NOM))
    {
        $controller->addError('nom');
        $session->addFlashes(LKS_FLASH_ERROR, LKS_FORMAT_NOM_MSG);
    }

    /*************************************
    * Contrôle de la saisie de l'adresse *
    **************************************/
    if($controller->isEmptyField($_POST['adresse']))
    {
        $controller->addError('adresse');
        $session->addFlashes(LKS_FLASH_ERROR, 'Le champ "adresse" est obligatoire.');
    }
    elseif(!$controller->isValid($_POST['adresse'], LKS_FORMAT_ADRESSE))
    {
        $controller->addError('adresse');
        $session->addFlashes(LKS_FLASH_ERROR, LKS_FORMAT_ADRESSE_MSG);
    }

    /************************************
    * Contrôle de la saisie de la ville *
    *************************************/
    if($controller->isEmptyField($_POST['ville']))
    {
        $controller->addError('ville');
        $session->addFlashes(LKS_FLASH_ERROR, 'Le champ "ville" est obligatoire.');
    }
    elseif(!$controller->isValid($_POST['ville'], LKS_FORMAT_VILLE))
    {
        $controller->addError('ville');
        $session->addFlashes(LKS_FLASH_ERROR, LKS_FORMAT_VILLE_MSG);
    }

    /***************************************
    * Contrôle de la saisie du code postal *
    ****************************************/
    if($controller->isEmptyField($_POST['cp']))
    {
        $controller->addError('cp');
        $session->addFlashes(LKS_FLASH_ERROR, 'Le code postal est obligatoire.');
    }
    elseif(!$controller->isValid($_POST['cp'], LKS_FORMAT_CP))
    {
        $controller->addError('cp');
        $session->addFlashes(LKS_FLASH_ERROR, LKS_FORMAT_CP_MSG);
    }

    /********************************
    * Contrôle de la saisie du pays *
    *********************************/
    if($controller->isEmptyField($_POST['pays']))
    {
        $controller->addError('pays');
        $session->addFlashes(LKS_FLASH_ERROR, 'Le champ "pays" est obligatoire.');
    }
    elseif(!$controller->isValid($_POST['pays'], LKS_FORMAT_PAYS))
    {
        $controller->addError('pays');
        $session->addFlashes(LKS_FLASH_ERROR, LKS_FORMAT_PAYS_MSG);
    }


    if(!$controller->hasError())
    {
        $session->addFlashes(LKS_FLASH_OK, 'Votre inscription a été validée.');

        $user = new User($_POST);

        $controller->registerToDatabase($user);

        $session->setUser($user);
        $session->redirect('profil');

        exit();
    }
}

?>
<?php require_once('includes/header.inc.php'); ?>


<div class="row">
    <h1 class="text-center">S'inscrire</h1>
    <p class="help-block text-center"><em>Tous les champs sont obligatoires.</em></p>
</div>

<div class="row">
<form action="inscription.php" method="post" class="col-md-offset-3 col-md-6">
    <?php // initialisation des variables pour la génération du formulaire
        $builder = new Builder();
    ?>

    <fieldset>
        <legend>Infos de connexion</legend>
        <?php
            echo $builder->generateFormInput($_POST, $controller, 'text', 'pseudo', 'Votre pseudo (15 caractères max.)', LKS_LABEL_HIDE);
            echo $builder->generateFormInput($_POST, $controller, 'password', 'password', 'Votre mot de passe', LKS_LABEL_HIDE);
            echo $builder->generateFormInput($_POST, $controller, 'password', 'password_confirm', 'Votre confirmation de mot de passe', LKS_LABEL_HIDE);
            echo $builder->generateFormInput($_POST, $controller, 'email', 'email', 'Votre e-mail', LKS_LABEL_HIDE);
        ?>
    </fieldset>


    <fieldset>
        <legend>Etat Civil</legend>
        <?php
            echo $builder->generateFormInput($_POST, $controller, 'text', 'prenom', 'Votre prénom', LKS_LABEL_HIDE);
            echo $builder->generateFormInput($_POST, $controller, 'text', 'nom', 'Votre nom', LKS_LABEL_HIDE);
            echo $builder->generateFormRadio('sexe', 'Vous êtes : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', unserialize(LKS_DATA_RADIO_SEX), 'h');
        ?>
    </fieldset>

    <fieldset>
        <legend>Adresse</legend>
        <?php
            echo $builder->generateFormInput($_POST, $controller, 'text', 'adresse', 'Votre adresse', LKS_LABEL_HIDE);
            echo $builder->generateFormInput($_POST, $controller, 'text', 'ville', 'Votre ville', LKS_LABEL_HIDE);
            echo $builder->generateFormInput($_POST, $controller, 'text', 'cp', 'Votre code postal', LKS_LABEL_HIDE);
            echo $builder->generateFormInput($_POST, $controller, 'text', 'pays', 'Votre pays', LKS_LABEL_HIDE);
        ?>
    </fieldset>

    <button type="submit" class="btn btn-primary pull-right">S'inscrire</button>

</form>
</div>

<div class="row"></div>

<div class="row">
    <div class="col-md-offset-2 col-md-8">
        <p class="help-block text-justify">
            <small><em>Les informations portées sur ce formulaire sont obligatoires. Elles font l’objet d’un traitement informatisé destiné à simuler l'activité de Lokisalle. Vos données ne sont destinées à aucune fin commerciale.</em></small>
        </p>
        <p class="help-block text-justify">
            <small><em>Conformément à la loi "informatique et libertés" du 6 janvier 1978 modifiée, vous bénéficiez d’un droit d’accès et de rectification aux informations qui vous concernent. Si vous souhaitez exercer ce droit et obtenir communication des informations vous concernant, veuillez utiliser le formulaire de contact sur cette <a href="contact.php">page</a></em></small>.
        </p>
    </div>
</div>

<?php require_once('includes/footer.inc.php'); ?>
